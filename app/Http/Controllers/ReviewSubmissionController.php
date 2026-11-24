<?php

namespace App\Http\Controllers;

use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ReviewSubmissionController extends Controller
{
    public function create(StoreOrder $order): View
    {
        abort_unless($order->status === 'delivered', 404);

        return view('botanical.reviews.create', [
            'order' => $order->loadMissing('items', 'review'),
            'formAction' => URL::signedRoute('reviews.store', ['order' => $order]),
        ]);
    }

    public function store(Request $request, StoreOrder $order): RedirectResponse
    {
        abort_unless($order->status === 'delivered', 404);

        if ($order->review()->exists()) {
            return redirect()->to(URL::signedRoute('reviews.create', ['order' => $order]));
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        DB::transaction(function () use ($data, $order): void {
            $lockedOrder = StoreOrder::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->status === 'delivered', 404);

            if (! $lockedOrder->review()->exists()) {
                $lockedOrder->review()->create($data);
            }
        });

        return redirect()->to(URL::signedRoute('reviews.create', ['order' => $order]));
    }
}
