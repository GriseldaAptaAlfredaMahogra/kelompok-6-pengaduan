<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->role = 'customer';
        $user->save();

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        $cartWasAdjusted = $user->mergePendingCart($request->session()->pull('pending_cart', []));
        $redirect = redirect()->intended(route('shop.index'));

        if ($cartWasAdjusted) {
            $redirect->with('warning', 'Beberapa item keranjang dilewati atau jumlahnya disesuaikan.');
        }

        return $redirect;
    }
}
