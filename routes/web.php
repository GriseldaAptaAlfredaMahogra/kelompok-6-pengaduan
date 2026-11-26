<?php

use App\Http\Controllers\Admin\BouquetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlowerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\ReviewSubmissionController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

Route::get('/', function (Request $request): RedirectResponse {
    $user = $request->user();

    return to_route($user === null ? 'login' : ($user->isAdmin() ? 'admin.dashboard' : 'shop.index'));
})->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

Route::get('/login', [SessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('login.store');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('register.store');

Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::post('/cart/items', [CartController::class, 'store'])->middleware('throttle:30,1')->name('cart.items.store');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

    Route::get('/checkout', [CustomerOrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerOrderController::class, 'placeOrder'])->name('checkout.store');

    Route::get('/my/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/my/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/my/review', [ReviewSubmissionController::class, 'pending'])->name('customer.reviews.pending');
    Route::get('/my/orders/{order}/review', [ReviewSubmissionController::class, 'create'])->name('reviews.create');
    Route::post('/my/orders/{order}/review', [ReviewSubmissionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reviews.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('bouquets', BouquetController::class)->except('show');

    Route::get('inventory', [FlowerController::class, 'index'])->name('inventory.index');
    Route::post('inventory/flowers', [FlowerController::class, 'store'])->name('inventory.flowers.store');
    Route::post('inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');

    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
});