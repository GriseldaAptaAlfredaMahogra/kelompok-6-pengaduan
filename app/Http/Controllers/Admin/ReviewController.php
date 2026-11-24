<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);
        $reviews = OrderReview::with('order.items')->latest();

        if (isset($data['rating'])) {
            $reviews->where('rating', $data['rating']);
        }

        $ratingCounts = OrderReview::query()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return view('botanical.reviews.index', [
            'reviews' => $reviews->paginate(10)->withQueryString(),
            'reviewCount' => OrderReview::count(),
            'averageRating' => (float) OrderReview::avg('rating'),
            'ratingCounts' => $ratingCounts,
        ]);
    }
}
