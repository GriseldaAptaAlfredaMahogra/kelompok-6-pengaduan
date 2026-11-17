@extends('botanical.layout')

@section('title', $bouquet->exists ? 'Edit Rangkaian' : 'Tambah Rangkaian')

@section('content')
    <div class="page-heading compact">
        <div><span class="eyebrow">ATELIER COLLECTION</span><h1>{{ $bouquet->exists ? 'Edit Rangkaian' : 'Rangkaian Baru' }}</h1><p>Lengkapi detail koleksi buket Botanical Atelier.</p></div>
        <a class="button button-outline" href="{{ route('admin.bouquets.index') }}"><span class="material-symbols-outlined">arrow_back</span> Kembali ke katalog</a>
    </div>
    <form class="panel form-panel" method="POST" action="{{ $bouquet->exists ? route('admin.bouquets.update', $bouquet) : route('admin.bouquets.store') }}">
        @csrf
        @if ($bouquet->exists) @method('PUT') @endif
        <div class="form-grid">
            <label class="field"><span>Nama rangkaian</span><input name="name" value="{{ old('name', $bouquet->name) }}" required maxlength="120" placeholder="The Luminary Pastel Bouquet"></label>
            <label class="field"><span>Kategori</span><select name="category" required><option {{ old('category', $bouquet->category) === 'Buket Tangan' ? 'selected' : '' }}>Buket Tangan</option><option {{ old('category', $bouquet->category) === 'Standing Flower' ? 'selected' : '' }}>Standing Flower</option><option {{ old('category', $bouquet->category) === 'Preserved & Dried' ? 'selected' : '' }}>Preserved &amp; Dried</option><option {{ old('category', $bouquet->category) === 'Bloom Box' ? 'selected' : '' }}>Bloom Box</option></select></label>
            <label class="field"><span>Harga (Rp)</span><input type="number" name="price" value="{{ old('price', $bouquet->price) }}" min="0" step="1000" required></label>
            <label class="field"><span>URL gambar (opsional)</span><input type="url" name="image_url" value="{{ old('image_url', $bouquet->image_url) }}" placeholder="https://..."></label>
            <label class="field field-wide"><span>Cerita rangkaian</span><textarea name="description" rows="4" maxlength="2000" placeholder="Ceritakan bunga, warna, dan momen yang menginspirasinya...">{{ old('description', $bouquet->description) }}</textarea></label>
            <label class="check-field field-wide"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $bouquet->exists ? $bouquet->is_active : true))><span>Tampilkan sebagai koleksi aktif dan tersedia untuk pesanan baru</span></label>
        </div>
        <div class="form-actions"><a class="button button-outline" href="{{ route('admin.bouquets.index') }}">Batal</a><button class="button button-primary" type="submit"><span class="material-symbols-outlined">check</span> Simpan rangkaian</button></div>
    </form>
@endsection
