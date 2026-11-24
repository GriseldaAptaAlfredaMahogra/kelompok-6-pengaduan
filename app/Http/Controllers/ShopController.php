<?php

namespace App\Http\Controllers;

use App\Models\Bouquet;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        return view('shop.index', [
            'bouquets' => Bouquet::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->paginate(12),
        ]);
    }
}
