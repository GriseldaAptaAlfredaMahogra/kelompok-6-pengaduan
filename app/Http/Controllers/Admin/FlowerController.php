<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flower;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlowerController extends Controller
{
    public function index(): View
    {
        return view('botanical.inventory.index', [
            'flowers' => Flower::withSum('batches as stock_on_hand', 'quantity')->with('batches.supplier')->orderBy('name')->get(),
            'batches' => \App\Models\FlowerBatch::with(['flower', 'supplier'])->latest('received_at')->take(6)->get(),
            'suppliers' => \App\Models\Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Flower::create($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'variety' => ['nullable', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'unit' => ['required', 'string', 'max:30'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]));

        return to_route('admin.inventory.index')->with('success', 'Varian bunga berhasil ditambahkan.');
    }
}
