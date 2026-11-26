@extends('botanical.layout')

@section('title', 'Pesanan Selesai')

@section('content')
    <div class="breadcrumbs"><a href="{{ route('admin.orders.index') }}">Pesanan Buket</a><span>›</span><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a><span>›</span><span>Selesai &amp; Terkirim</span></div>
    <section class="completion-hero">
        <div class="completion-seal"><span class="material-symbols-outlined">verified</span></div>
        <span class="eyebrow">BERHASIL DISERAHKAN · BOTANICAL ATELIER</span>
        <h1>Pesanan Berhasil<br>Selesai &amp; Terkirim</h1>
        <p>Rangkaian untuk <strong>{{ $order->customer_name }}</strong> telah diselesaikan oleh atelier.</p>
        <div class="completion-facts"><span><small>NOMOR PESANAN</small><strong>#{{ $order->order_number }}</strong></span><span><small>STATUS</small><strong>{{ $order->status_label }}</strong></span><span><small>DISELESAIKAN</small><strong>{{ $order->completed_at?->format('d M Y, H:i') ?? 'Baru saja' }}</strong></span></div>
        @if ($order->review)
            <p class="review-received"><span class="material-symbols-outlined">verified</span> Ulasan pembeli sudah diterima.</p>
        @elseif ($order->user_id)
            <section class="review-share">
                <span class="material-symbols-outlined">mark_email_read</span>
                <div><strong>Ulasan dari {{ $order->customer_name }}</strong><p>Pembeli dapat memberikan rating dan ulasan dari halaman Pesanan Saya setelah masuk ke akun.</p></div>
            </section>
        @else
            <p class="review-received"><span class="material-symbols-outlined">info</span> Pesanan ini tidak terhubung ke akun pelanggan, sehingga ulasan akun tidak tersedia.</p>
        @endif
        <div class="completion-actions"><a class="button button-primary" href="{{ route('admin.orders.show', $order) }}"><span class="material-symbols-outlined">receipt_long</span> Lihat Detail Pesanan</a><a class="button button-outline" href="{{ route('admin.orders.create') }}"><span class="material-symbols-outlined">add</span> Buat Pesanan Baru</a></div>
    </section>
    <section class="panel completion-receipt">
        <div class="panel-heading"><div><span class="eyebrow">ATELIER RECEIPT</span><h2>Rangkaian yang dikirim</h2></div><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
        @foreach ($order->items as $item)
            <div class="order-item-detail"><span class="flower-thumb">✿</span><div><strong>{{ $item->item_name }}</strong><small>{{ $item->quantity }} item</small></div><strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong></div>
        @endforeach
        @if ($order->message_card)<blockquote class="customer-message">“{{ $order->message_card }}”</blockquote>@endif
    </section>
@endsection
