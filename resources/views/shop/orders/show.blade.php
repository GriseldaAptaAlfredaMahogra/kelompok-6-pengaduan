@extends('shop.layout')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="store-section-heading">
        <div><span class="eyebrow">{{ $order->order_number }}</span><h1>Detail pesanan</h1></div>
        <a class="text-link" href="{{ route('customer.orders.index') }}">Kembali ke pesanan saya</a>
    </div>
    <section class="panel customer-order-detail">
        <div class="customer-order-status"><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span><span>Dibuat {{ $order->created_at->format('d M Y, H:i') }}</span></div>
        @foreach ($order->items as $item)
            <div class="checkout-item"><span>{{ $item->item_name }} × {{ $item->quantity }}</span><strong>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</strong></div>
        @endforeach
        <div class="checkout-total"><span>Total pesanan</span><strong>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></div>
        @if ($order->status === 'delivered')
            @if ($order->review)
                <div class="customer-review">
                    <span class="eyebrow">ULASAN ANDA</span>
                    <div class="review-stars" role="img" aria-label="Rating {{ $order->review->rating }} dari 5">
                        @for ($star = 1; $star <= 5; $star++)
                            <span class="material-symbols-outlined {{ $star <= $order->review->rating ? 'filled' : '' }}">star</span>
                        @endfor
                    </div>
                    <blockquote class="submitted-comment">{{ $order->review->comment }}</blockquote>
                </div>
            @else
                <div class="customer-review-prompt">
                    <p>Rangkaian sudah sampai? Bagikan pengalaman Anda untuk membantu pelanggan lain.</p>
                    <a class="button button-primary" href="{{ route('reviews.create', $order) }}"><span class="material-symbols-outlined">rate_review</span> Beri Ulasan</a>
                </div>
            @endif
        @endif
        <dl class="delivery-details">
            <div><dt>Penerima</dt><dd>{{ $order->customer_name }}</dd></div>
            <div><dt>Telepon</dt><dd>{{ $order->phone }}</dd></div>
            <div><dt>Alamat pengiriman</dt><dd>{{ $order->delivery_address }}</dd></div>
            @if ($order->requested_delivery_at)
                <div><dt>Tanggal pengiriman</dt><dd>{{ $order->requested_delivery_at->format('d M Y') }}</dd></div>
            @endif
            @if ($order->message_card)
                <div><dt>Pesan kartu</dt><dd>{{ $order->message_card }}</dd></div>
            @endif
        </dl>
    </section>
@endsection
