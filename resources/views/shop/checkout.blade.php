@extends('shop.layout')

@section('title', 'Checkout')

@section('content')
    <div class="store-section-heading">
        <div><span class="eyebrow">SATU LANGKAH LAGI</span><h1>Detail pengiriman</h1></div>
        <a class="text-link" href="{{ route('cart.index') }}">Kembali ke keranjang</a>
    </div>
    <div class="checkout-layout">
        <form class="panel form-panel" method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <h2>Informasi penerima</h2>
            <label class="field"><span>Nama penerima</span><input type="text" name="customer_name" value="{{ old('customer_name', $user->name) }}" maxlength="150" required autocomplete="name"></label>
            <label class="field"><span>Nomor telepon</span><input type="tel" name="phone" value="{{ old('phone') }}" maxlength="40" required autocomplete="tel" placeholder="+62"></label>
            <label class="field"><span>Alamat pengiriman</span><textarea name="delivery_address" rows="3" maxlength="1000" required autocomplete="street-address">{{ old('delivery_address') }}</textarea></label>
            <div class="form-grid">
                <label class="field"><span>Keperluan / acara</span><input type="text" name="occasion" value="{{ old('occasion') }}" maxlength="120" placeholder="Contoh: ulang tahun"></label>
                <label class="field"><span>Tanggal pengiriman</span><input type="date" name="requested_delivery_at" value="{{ old('requested_delivery_at') }}" min="{{ now()->toDateString() }}"></label>
            </div>
            <label class="field"><span>Catatan untuk rangkaian</span><textarea name="customization_notes" rows="3" maxlength="2000" placeholder="Warna atau detail yang Anda inginkan">{{ old('customization_notes') }}</textarea></label>
            <label class="field"><span>Pesan kartu ucapan</span><textarea name="message_card" rows="2" maxlength="1000" placeholder="Tuliskan pesan untuk penerima">{{ old('message_card') }}</textarea></label>
            <button class="button button-primary full-width" type="submit">Buat pesanan <span class="material-symbols-outlined">arrow_forward</span></button>
        </form>
        <aside class="cart-summary panel">
            <span class="eyebrow">PESANAN ANDA</span>
            <h2>Rangkaian pilihan</h2>
            @foreach ($items as $item)
                <div class="checkout-item"><span>{{ $item->bouquet->name }} × {{ $item->quantity }}</span><strong>Rp {{ number_format((float) $item->bouquet->price * $item->quantity, 0, ',', '.') }}</strong></div>
            @endforeach
            <div class="checkout-total"><span>Total</span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
        </aside>
    </div>
@endsection
