@extends('botanical.layout')

@section('title', 'Stok Bunga Segar')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">COLD-ROOM &amp; RESTOCK</span><h1>Katalog Stok Bunga Segar</h1><p>Pantau kesegaran bunga, batch masuk, dan kebutuhan restock supplier.</p></div>
        <a class="button button-primary" href="#restock"><span class="material-symbols-outlined">add</span> Catat Restock</a>
    </div>
    <section class="metric-grid inventory-metrics">
        <article class="metric-card"><div class="metric-label">VARIAN TERDAFTAR</div><strong class="metric-value">{{ $flowers->count() }}</strong><small>Bunga utama, filler &amp; foliage</small></article>
        <article class="metric-card"><div class="metric-label">TOTAL TANGKAI TERSEDIA</div><strong class="metric-value">{{ number_format($flowers->sum(fn ($flower) => (int) ($flower->stock_on_hand ?? 0))) }}</strong><small>Terhitung dari batch stok aktif</small></article>
        <article class="metric-card"><div class="metric-label">RESTOCK PRIORITAS</div><strong class="metric-value">{{ $flowers->filter(fn ($flower) => (int) ($flower->stock_on_hand ?? 0) <= $flower->low_stock_threshold)->count() }}</strong><small>Varian mendekati batas minimum</small></article>
        <article class="metric-card"><div class="metric-label">BATCH MASUK</div><strong class="metric-value">{{ $batches->count() }}</strong><small>Batch terbaru yang tercatat</small></article>
    </section>
    <div class="inventory-layout">
        <section class="panel">
            <div class="panel-heading"><div><span class="eyebrow">COLD-ROOM LEDGER</span><h2>Varian Bunga</h2></div></div>
            <div class="inventory-list">
                @forelse ($flowers as $flower)
                    @php($stock = (int) ($flower->stock_on_hand ?? 0))
                    <article class="inventory-item">
                        <span class="flower-thumb">{{ $loop->iteration % 2 ? '✿' : '❀' }}</span>
                        <div class="inventory-copy"><span class="eyebrow">{{ $flower->category }}</span><h3>{{ $flower->name }} {{ $flower->variety }}</h3><small>{{ $flower->batches->count() }} batch · Min. stok {{ $flower->low_stock_threshold }} {{ $flower->unit }}</small></div>
                        <div class="stock-status"><strong class="{{ $stock <= $flower->low_stock_threshold ? 'text-coral' : '' }}">{{ $stock }} <small>{{ $flower->unit }}</small></strong><span class="badge {{ $stock <= $flower->low_stock_threshold ? 'badge-cancelled' : 'badge-ready' }}">{{ $stock <= $flower->low_stock_threshold ? 'Perlu restock' : 'Stok aman' }}</span></div>
                    </article>
                @empty
                    <div class="empty-state"><span class="material-symbols-outlined">psychiatry</span><strong>Belum ada varian bunga</strong><p>Tambahkan bunga pertama dari formulir di sebelah.</p></div>
                @endforelse
            </div>
            <div class="table-wrap">
                <h3 class="subsection-title">Batch Terbaru</h3>
                <table class="data-table"><thead><tr><th>Batch</th><th>Bunga</th><th>Supplier</th><th>Jumlah</th><th>Rak</th><th>Diterima</th></tr></thead><tbody>
                    @forelse ($batches as $batch)
                        <tr><td>#{{ $batch->batch_code }}</td><td>{{ $batch->flower?->name }} {{ $batch->flower?->variety }}</td><td>{{ $batch->supplier?->name ?? '—' }}</td><td>{{ $batch->quantity }}</td><td>{{ $batch->cold_room_location ?: '—' }}</td><td>{{ $batch->received_at?->format('d M Y') ?: '—' }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="table-empty">Belum ada batch masuk.</td></tr>
                    @endforelse
                </tbody></table>
            </div>
        </section>
        <aside class="inventory-forms">
            <form class="panel form-panel compact-form" method="POST" action="{{ route('admin.inventory.restock') }}" id="restock">
                @csrf
                <span class="eyebrow">RESTOCK BATCH</span><h2>Catat Bunga Masuk</h2>
                <label class="field"><span>Varian bunga</span><select name="flower_id" required><option value="">Pilih bunga</option>@foreach ($flowers as $flower)<option value="{{ $flower->id }}" @selected(old('flower_id') == $flower->id)>{{ $flower->name }} {{ $flower->variety }}</option>@endforeach</select></label>
                <label class="field"><span>Supplier</span><select name="supplier_id"><option value="">Pilih supplier</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select></label>
                <label class="field"><span>Kode batch</span><input name="batch_code" value="{{ old('batch_code') }}" required maxlength="80" placeholder="BATCH-2026-001"></label>
                <div class="form-grid two"><label class="field"><span>Jumlah tangkai</span><input type="number" name="quantity" min="1" required value="{{ old('quantity') }}"></label><label class="field"><span>Harga beli / unit</span><input type="number" name="purchase_price" min="0" step="100" required value="{{ old('purchase_price', 0) }}"></label></div>
                <div class="form-grid two"><label class="field"><span>Rak cold-room</span><input name="cold_room_location" value="{{ old('cold_room_location') }}" placeholder="Rak A-01"></label><label class="field"><span>Kedaluwarsa</span><input type="date" name="expires_at" value="{{ old('expires_at') }}"></label></div>
                <button class="button button-primary full-width" type="submit"><span class="material-symbols-outlined">inventory</span> Simpan batch stok</button>
            </form>
            <form class="panel form-panel compact-form" method="POST" action="{{ route('admin.inventory.flowers.store') }}">
                @csrf
                <span class="eyebrow">FLOWER VARIETY</span><h2>Tambah Varian</h2>
                <label class="field"><span>Nama bunga</span><input name="name" required placeholder="Mawar"></label>
                <label class="field"><span>Varietas</span><input name="variety" placeholder="Ekuador Pastel"></label>
                <div class="form-grid two"><label class="field"><span>Kategori</span><select name="category" required><option>Bunga Utama</option><option>Filler</option><option>Foliage</option><option>Preserved</option></select></label><label class="field"><span>Satuan</span><input name="unit" value="tangkai" required></label></div>
                <label class="field"><span>Batas minimum stok</span><input type="number" name="low_stock_threshold" min="0" value="10" required></label>
                <button class="button button-outline full-width" type="submit">Tambahkan varian</button>
            </form>
        </aside>
    </div>
@endsection
