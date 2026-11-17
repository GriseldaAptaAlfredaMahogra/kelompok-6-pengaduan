<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\StoreOrder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();
        $orders = StoreOrder::with('items')->latest()->take(5)->get();
        $flowers = Flower::withSum('batches as stock_on_hand', 'quantity')->get();

        return view('botanical.dashboard', [
            'orders' => $orders,
            'flowers' => $flowers,
            'metrics' => [
                'sales' => StoreOrder::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total'),
                'active_orders' => StoreOrder::whereNotIn('status', ['delivered', 'cancelled'])->count(),
                'bouquets' => Bouquet::where('is_active', true)->count(),
                'low_stock' => $flowers->filter(fn (Flower $flower) => (int) $flower->stock_on_hand <= $flower->low_stock_threshold)->count(),
            ],
        ]);
    }
}
