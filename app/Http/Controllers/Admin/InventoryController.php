<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowerBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return app(FlowerController::class)->index();
    }

    public function restock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'flower_id' => ['required', 'exists:flowers,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'batch_code' => ['required', 'string', 'max:80', 'unique:flower_batches,batch_code'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'cold_room_location' => ['nullable', 'string', 'max:80'],
            'received_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:received_at'],
        ]);
        $data['received_at'] = $data['received_at'] ?? now()->toDateString();

        FlowerBatch::create($data);

        return to_route('admin.inventory.index')->with('success', 'Batch bunga berhasil dicatat ke stok.');
    }
}
