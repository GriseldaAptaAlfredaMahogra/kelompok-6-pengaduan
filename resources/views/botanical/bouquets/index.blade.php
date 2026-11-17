@extends('botanical.layout')

@section('title', 'Katalog Rangkaian')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">BOTANICAL COLLECTION</span><h1>Katalog &amp; Rangkaian</h1><p>Kelola koleksi buket yang siap menemani momen spesial pelanggan.</p></div>
        <a class="button button-primary" href="{{ route('admin.bouquets.create') }}"><span class="material-symbols-outlined">add</span> Tambah Rangkaian</a>
    </div>
    <div class="collection-toolbar"><span>{{ $bouquets->total() }} rangkaian</span><span class="toolbar-note">Harga dan detail dapat diperbarui kapan saja.</span></div>
    <div class="product-grid">
        @forelse ($bouquets as $bouquet)
            <article class="product-card">
                @if ($bouquet->image_url)
                    <img class="product-image" src="{{ $bouquet->image_url }}" alt="{{ $bouquet->name }}" loading="lazy">
                @else
                    <div class="product-image product-art product-art-{{ $loop->iteration % 4 }}"><span>✿</span><small>BOTANICAL ATELIER</small></div>
                @endif
                <div class="product-copy"><div class="product-topline"><span class="eyebrow">{{ $bouquet->category }}</span><span class="badge {{ $bouquet->is_active ? 'badge-ready' : 'badge-cancelled' }}">{{ $bouquet->is_active ? 'Aktif' : 'Diarsipkan' }}</span></div><h2>{{ $bouquet->name }}</h2><p>{{ $bouquet->description ?: 'Rangkaian bunga pilihan, dirangkai dengan penuh perhatian.' }}</p><strong class="product-price">Rp {{ number_format($bouquet->price, 0, ',', '.') }}</strong></div>
                <div class="product-actions"><a class="button button-outline" href="{{ route('admin.bouquets.edit', $bouquet) }}">Edit detail</a>
                    @if ($bouquet->is_active)
                        <form method="POST" action="{{ route('admin.bouquets.destroy', $bouquet) }}">@csrf @method('DELETE')<button class="button button-soft" type="submit">Arsipkan</button></form>
                    @endif
                </div>
            </article>
        @empty
            <div class="panel empty-state"><span class="material-symbols-outlined">filter_vintage</span><strong>Mulai koleksi pertama</strong><p>Tambahkan rangkaian buket agar pesanan dapat dibuat.</p><a class="button button-primary" href="{{ route('admin.bouquets.create') }}">Tambah rangkaian</a></div>
        @endforelse
    </div>
    <div class="pagination-wrap">{{ $bouquets->links() }}</div>
@endsection
