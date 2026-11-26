<?php

namespace App\Http\Controllers;

use App\Models\Bouquet;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('shop.orders.index', [
            'orders' => $request->user()->orders()->with(['items', 'review'])->latest()->paginate(12),
        ]);
    }

    public function show(Request $request, StoreOrder $order): View
    {
        $order = $request->user()->orders()
            ->whereKey($order->id)
            ->with(['items', 'review'])
            ->firstOrFail();

        return view('shop.orders.show', ['order' => $order]);
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $items = $user->cartItems()->with('bouquet')->latest()->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('shop.checkout', [
            'items' => $items,
            'total' => $items->sum(fn ($item): float => (float) $item->bouquet->price * $item->quantity),
            'user' => $user,
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'occasion' => ['nullable', 'string', 'max:120'],
            'requested_delivery_at' => ['nullable', 'date', 'after_or_equal:today'],
            'customization_notes' => ['nullable', 'string', 'max:2000'],
            'message_card' => ['nullable', 'string', 'max:1000'],
        ]);
        $user = $request->user();

        $order = DB::transaction(function () use ($data, $user): StoreOrder {
            $user = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $cartItems = $user->cartItems()->with('bouquet')->lockForUpdate()->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang Anda kosong. Tambahkan rangkaian sebelum membuat pesanan.',
                ]);
            }

            $items = [];
            $subtotal = 0;

            foreach ($cartItems as $cartItem) {
                $bouquet = Bouquet::query()
                    ->whereKey($cartItem->bouquet_id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $bouquet) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu rangkaian sudah tidak tersedia. Perbarui keranjang Anda.',
                    ]);
                }

                $unitPrice = (float) $bouquet->price;
                $lineTotal = $unitPrice * $cartItem->quantity;
                $subtotal += $lineTotal;
                $items[] = [
                    'bouquet_id' => $bouquet->id,
                    'flower_id' => null,
                    'item_name' => $bouquet->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $order = $user->orders()->create([
                'order_number' => 'BA-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'delivery_address' => $data['delivery_address'],
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
            $user->cartItems()->delete();

            return $order;
        });

        return to_route('customer.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }
}
