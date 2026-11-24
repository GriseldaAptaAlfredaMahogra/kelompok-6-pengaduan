@extends('shop.layout')

@section('title', 'Koleksi Rangkaian')

@section('content')
    <section class="store-hero">
        <span class="eyebrow">BUNGA SEGAR, MOMEN BERARTI</span>
        <h1>Rangkaian untuk<br>setiap cerita.</h1>
        <p>Temukan buket pilihan yang dirangkai penuh perhatian untuk momen istimewa Anda.</p>
    </section>

    <div class="store-section-heading">
        <div><span class="eyebrow">KOLEKSI ATELIER</span><h2>Rangkaian pilihan</h2></div>
        <span>{{ $bouquets->total() }} rangkaian</span>
    </div>

    @if ($bouquets->isEmpty())
        <div class="empty-state panel"><span class="material-symbols-outlined">local_florist</span><strong>Koleksi sedang disiapkan</strong><p>Silakan kembali lagi untuk melihat rangkaian bunga terbaru.</p></div>
    @else
        <div class="product-grid">
            @foreach ($bouquets as $bouquet)
                <article class="product-card">
                    @if ($bouquet->image_url)
                        <img class="product-image" src="{{ $bouquet->image_url }}" alt="{{ $bouquet->name }}" loading="lazy">
                    @else
                        <div class="product-image product-art"><span>✿</span><small>BOTANICAL ATELIER</small></div>
                    @endif
                    <div class="product-copy">
                        <div class="product-topline"><span class="eyebrow">{{ $bouquet->category }}</span><span class="product-price">Rp {{ number_format((float) $bouquet->price, 0, ',', '.') }}</span></div>
                        <h2>{{ $bouquet->name }}</h2>
                        <p>{{ $bouquet->description ?: 'Rangkaian bunga segar pilihan, dirangkai dengan penuh perhatian.' }}</p>
                    </div>
                    <div class="product-actions">
                        <form method="POST" action="{{ route('cart.items.store') }}">
                            @csrf
                            <input type="hidden" name="bouquet_id" value="{{ $bouquet->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="button button-primary" type="submit">Tambah ke keranjang</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        @if ($bouquets->hasPages())
            <div class="pagination-wrap">{{ $bouquets->links() }}</div>
        @endif
    @endif
@endsection
