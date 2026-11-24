<?php

namespace App\Http\Controllers;

use App\Models\Bouquet;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $items = $request->user()->cartItems()->with('bouquet')->latest()->get();

        return view('shop.cart.index', [
            'items' => $items,
            'total' => $items->sum(fn (CartItem $item): float => (float) $item->bouquet->price * $item->quantity),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bouquet_id' => ['required', 'integer', Rule::exists('bouquets', 'id')->where('is_active', true)],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $bouquet = Bouquet::query()->whereKey($data['bouquet_id'])->where('is_active', true)->firstOrFail();

        if (! $request->user()) {
            $pendingCart = $request->session()->get('pending_cart', []);
            $bouquetId = (string) $bouquet->id;
            $quantity = ($pendingCart[$bouquetId] ?? 0) + $data['quantity'];

            if ($quantity > 100) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah maksimal untuk satu rangkaian adalah 100.',
                ]);
            }

            $pendingCart[$bouquetId] = $quantity;
            $request->session()->put('pending_cart', $pendingCart);
            $request->session()->put('url.intended', route('cart.index'));

            return to_route('login');
        }

        DB::transaction(function () use ($request, $bouquet, $data): void {
            $user = User::query()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $item = $user->cartItems()->where('bouquet_id', $bouquet->id)->lockForUpdate()->first();
            $quantity = ($item?->quantity ?? 0) + $data['quantity'];

            if ($quantity > 100) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah maksimal untuk satu rangkaian adalah 100.',
                ]);
            }

            $user->cartItems()->updateOrCreate(
                ['bouquet_id' => $bouquet->id],
                ['quantity' => $quantity],
            );
        });

        return to_route('cart.index')->with('success', 'Rangkaian ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $ownedItem = $request->user()->cartItems()->whereKey($cartItem->id)->firstOrFail();
        $ownedItem->update(['quantity' => $data['quantity']]);

        return to_route('cart.index')->with('success', 'Jumlah keranjang diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->user()->cartItems()->whereKey($cartItem->id)->firstOrFail()->delete();

        return to_route('cart.index')->with('success', 'Rangkaian dihapus dari keranjang.');
    }
}
