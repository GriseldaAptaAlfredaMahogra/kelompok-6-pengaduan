<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bouquet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BouquetController extends Controller
{
    public function index(): View
    {
        return view('botanical.bouquets.index', [
            'bouquets' => Bouquet::latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('botanical.bouquets.form', ['bouquet' => new Bouquet]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active');

        Bouquet::create($data);

        return to_route('admin.bouquets.index')->with('success', 'Buket baru berhasil ditambahkan.');
    }

    public function edit(Bouquet $bouquet): View
    {
        return view('botanical.bouquets.form', compact('bouquet'));
    }

    public function update(Request $request, Bouquet $bouquet): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $bouquet->update($data);

        return to_route('admin.bouquets.index')->with('success', 'Perubahan katalog berhasil disimpan.');
    }

    public function destroy(Bouquet $bouquet): RedirectResponse
    {
        $bouquet->update(['is_active' => false]);

        return to_route('admin.bouquets.index')->with('success', 'Buket disembunyikan dari katalog aktif.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);
    }
}
