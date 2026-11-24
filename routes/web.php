<?php

use App\Http\Controllers\Admin\BouquetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlowerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\ReviewSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware('auth')->name('home');
Route::get('/login', [SessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('login.store');
Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/ulasan/{order}', [ReviewSubmissionController::class, 'create'])->middleware('signed')->name('reviews.create');
Route::post('/ulasan/{order}', [ReviewSubmissionController::class, 'store'])->middleware(['signed', 'throttle:10,1'])->name('reviews.store');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
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
