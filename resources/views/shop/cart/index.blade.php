@extends('shop.layout')

@section('title', 'Keranjang')

@section('content')
    <div class="store-section-heading">
        <div><span class="eyebrow">PILIHAN ANDA</span><h1>Keranjang belanja</h1></div>
        <a class="text-link" href="{{ route('shop.index') }}">Lanjut melihat koleksi <span class="material-symbols-outlined">arrow_forward</span></a>
    </div>

    @if ($items->isEmpty())
        <div class="empty-state panel"><span class="material-symbols-outlined">shopping_bag</span><strong>Keranjang Anda masih kosong</strong><p>Pilih rangkaian favorit untuk memulai pesanan.</p><a class="button button-primary" href="{{ route('shop.index') }}">Jelajahi koleksi</a></div>
    @else
        <div class="cart-layout">
            <section class="cart-list panel" aria-label="Isi keranjang">
                @foreach ($items as $item)
                    <article class="cart-line">
                        <div class="cart-product-mark">✿</div>
                        <div class="cart-product-copy">
                            <span class="eyebrow">{{ $item->bouquet->category }}</span>
                            <h2>{{ $item->bouquet->name }}</h2>
                            <span>Rp {{ number_format((float) $item->bouquet->price, 0, ',', '.') }}</span>
                        </div>
                        <form class="cart-quantity" method="POST" action="{{ route('cart.items.update', $item) }}">
                            @csrf
                            @method('PATCH')
                            <label class="field"><span>Jumlah</span><input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="100" required></label>
                            <button class="text-link" type="submit">Perbarui</button>
                        </form>
                        <form method="POST" action="{{ route('cart.items.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="icon-button" type="submit" aria-label="Hapus {{ $item->bouquet->name }}"><span class="material-symbols-outlined">delete</span></button>
                        </form>
                    </article>
                @endforeach
            </section>
            <aside class="cart-summary panel">
                <span class="eyebrow">RINGKASAN</span>
                <h2>Total pesanan</h2>
                <div><span>Subtotal</span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
                <small>Biaya pengiriman akan dikonfirmasi oleh atelier.</small>
                <a class="button button-primary full-width" href="{{ route('checkout') }}">Lanjut ke checkout <span class="material-symbols-outlined">arrow_forward</span></a>
            </aside>
        </div>
    @endif
@endsection
