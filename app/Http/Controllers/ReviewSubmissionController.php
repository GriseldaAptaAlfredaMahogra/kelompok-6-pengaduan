<?php

namespace App\Http\Controllers;

use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReviewSubmissionController extends Controller
{
    public function pending(Request $request): RedirectResponse
    {
        $order = $request->user()->orders()
            ->where('status', 'delivered')
            ->whereDoesntHave('review')
            ->latest()
            ->first();

        if (! $order) {
            return to_route('customer.orders.index')
                ->with('warning', 'Tidak ada pesanan yang menunggu ulasan.');
        }

        return to_route('reviews.create', $order);
    }

    public function create(Request $request, StoreOrder $order): View
    {
        $order = $request->user()->orders()
            ->whereKey($order->getKey())
            ->with('items', 'review')
            ->firstOrFail();

        abort_unless($order->status === 'delivered', 404);

        return view('botanical.reviews.create', [
            'order' => $order,
            'formAction' => route('reviews.store', ['order' => $order]),
        ]);
    }

    public function store(Request $request, StoreOrder $order): RedirectResponse
    {
        $order = $request->user()->orders()
            ->whereKey($order->getKey())
            ->firstOrFail();

        abort_unless($order->status === 'delivered', 404);

        if ($order->review()->exists()) {
            return to_route('customer.orders.show', $order)
                ->with('success', 'Ulasan untuk pesanan ini sudah pernah dikirim.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $reviewCreated = DB::transaction(function () use ($data, $order, $request): bool {
            $lockedOrder = $request->user()->orders()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedOrder->status === 'delivered', 404);

            if ($lockedOrder->review()->exists()) {
                return false;
            }

            $lockedOrder->review()->create($data);

            return true;
        });

        return to_route('customer.orders.show', $order)
            ->with('success', $reviewCreated
                ? 'Terima kasih, ulasan Anda berhasil dikirim.'
                : 'Ulasan untuk pesanan ini sudah pernah dikirim.');
    }
}
