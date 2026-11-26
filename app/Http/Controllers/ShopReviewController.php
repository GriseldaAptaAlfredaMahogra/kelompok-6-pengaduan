<?php

namespace App\Http\Controllers;

use App\Models\OrderReview;
use Illuminate\View\View;

class ShopReviewController extends Controller
{
    public function index(): View
    {
        $reviews = OrderReview::query()
            ->whereHas('order', fn ($query) => $query->where('status', 'delivered'))
            ->with('order.items')
            ->latest();

        return view('shop.reviews.index', [
            'reviews' => $reviews->paginate(10),
            'reviewCount' => (clone $reviews)->count(),
            'averageRating' => (float) (clone $reviews)->avg('rating'),
        ]);
    }
}
