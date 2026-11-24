<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return to_route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'shop.index');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user();
        $cartWasAdjusted = $user->mergePendingCart($request->session()->pull('pending_cart', []));
        $defaultRoute = $user->isAdmin() ? route('admin.dashboard') : route('shop.index');
        $redirect = redirect()->intended($defaultRoute);

        if ($cartWasAdjusted) {
            $redirect->with('warning', 'Beberapa item keranjang dilewati atau jumlahnya disesuaikan.');
        }

        return $redirect;
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
