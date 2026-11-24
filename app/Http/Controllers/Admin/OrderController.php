<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\FlowerBatch;
use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'arranging', 'ready', 'dispatched', 'delivered', 'cancelled'];

    public function index(): View
    {
        $orders = StoreOrder::with('items')->latest();

        if ($search = request('q')) {
            $orders->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return view('botanical.orders.index', [
            'orders' => $orders->paginate(12)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('botanical.orders.create', [
            'bouquets' => Bouquet::where('is_active', true)->orderBy('name')->get(),
            'flowers' => Flower::withSum('batches as stock_on_hand', 'quantity')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'occasion' => ['nullable', 'string', 'max:120'],
            'requested_delivery_at' => ['nullable', 'date', 'after_or_equal:today'],
            'bouquet_id' => ['required', 'exists:bouquets,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'customization_notes' => ['nullable', 'string', 'max:2000'],
            'message_card' => ['nullable', 'string', 'max:1000'],
            'flower_items' => ['nullable', 'array'],
            'flower_items.*' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);
        foreach (array_keys($data['flower_items'] ?? []) as $flowerId) {
            if (! ctype_digit((string) $flowerId) || ! Flower::whereKey($flowerId)->exists()) {
                throw ValidationException::withMessages([
                    'flower_items' => 'Pilihan bunga tidak valid. Muat ulang halaman dan coba kembali.',
                ]);
            }
        }

        $order = DB::transaction(function () use ($data) {
            $bouquet = Bouquet::whereKey($data['bouquet_id'])->where('is_active', true)->firstOrFail();
            $items = [[
                'bouquet_id' => $bouquet->id,
                'flower_id' => null,
                'item_name' => $bouquet->name,
                'quantity' => $data['quantity'],
                'unit_price' => $bouquet->price,
                'line_total' => $bouquet->price * $data['quantity'],
            ]];
            $subtotal = (float) $bouquet->price * $data['quantity'];

            foreach ($data['flower_items'] ?? [] as $flowerId => $quantity) {
                if ((int) $quantity < 1) {
                    continue;
                }

                $flower = Flower::withSum('batches as stock_on_hand', 'quantity')->findOrFail($flowerId);
                $unitPrice = 15000;
                $items[] = [
                    'bouquet_id' => null,
                    'flower_id' => $flower->id,
                    'item_name' => $flower->name.($flower->variety ? ' '.$flower->variety : ''),
                    'quantity' => (int) $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice * (int) $quantity,
                ];
                $subtotal += $unitPrice * (int) $quantity;
            }

            $order = StoreOrder::create([
                'order_number' => 'BA-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'delivery_address' => $data['delivery_address'] ?? null,
                'occasion' => $data['occasion'] ?? null,
                'requested_delivery_at' => $data['requested_delivery_at'] ?? null,
                'status' => 'pending',
                'customization_notes' => $data['customization_notes'] ?? null,
                'message_card' => $data['message_card'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => $subtotal,
            ]);
            $order->items()->createMany($items);

            foreach ($order->items()->whereNotNull('flower_id')->get() as $item) {
                $remaining = $item->quantity;
                $batches = FlowerBatch::where('flower_id', $item->flower_id)
                    ->where('quantity', '>', 0)
                    ->orderBy('expires_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    if ($remaining === 0) {
                        break;
                    }

                    $allocated = min($remaining, $batch->quantity);
                    $batch->decrement('quantity', $allocated);
                    $item->batches()->attach($batch->id, ['quantity' => $allocated]);
                    $remaining -= $allocated;
                }

                if ($remaining > 0) {
                    throw ValidationException::withMessages([
                        'flower_items.'.$item->flower_id => 'Stok '.$item->item_name.' tidak mencukupi untuk jumlah yang diminta.',
                    ]);
                }
            }

            return $order;
        });

        return to_route('admin.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(StoreOrder $order): View
    {
        return view('botanical.orders.show', [
            'order' => $order->load('items.bouquet', 'items.flower'),
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, StoreOrder $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', self::STATUSES)]]);

        DB::transaction(function () use ($data, $order) {
            $order = StoreOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === 'cancelled' && $data['status'] !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan yang telah dibatalkan tidak dapat diaktifkan kembali.',
                ]);
            }

            if ($data['status'] === 'cancelled' && $order->status !== 'cancelled') {
                $order->loadMissing('items.batches');

                foreach ($order->items as $item) {
                    foreach ($item->batches as $batch) {
                        $batch->increment('quantity', $batch->pivot->quantity);
                    }
                }

                $order->stock_released_at = now();
            }

            $order->status = $data['status'];
            $order->completed_at = $data['status'] === 'delivered' ? now() : null;
            $order->save();
        });

        if ($data['status'] === 'delivered') {
            return to_route('admin.orders.complete', $order)->with('success', 'Pesanan telah ditandai selesai dan terkirim.');
        }

        return to_route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function complete(StoreOrder $order): View|RedirectResponse
    {
        if ($order->status !== 'delivered') {
            return to_route('admin.orders.show', $order);
        }

        return view('botanical.orders.complete', [
            'order' => $order->load('items', 'review'),
            'reviewUrl' => $order->review
                ? null
                : URL::signedRoute('reviews.create', ['order' => $order]),
        ]);
    }
}
