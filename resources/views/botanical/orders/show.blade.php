@extends('botanical.layout')

@section('title', 'Detail '.$order->order_number)

@section('content')
    <div class="breadcrumbs"><a href="{{ route('admin.orders.index') }}">Pesanan Buket</a><span>›</span><span>#{{ $order->order_number }}</span><span>›</span><span>Detail &amp; Kustomisasi</span></div>
    <section class="order-hero">
        <div><div class="eyebrows"><span class="eyebrow-chip">PESANAN ATELIER</span><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></div><h1>Pesanan #{{ $order->order_number }}</h1><p>Dibuat {{ $order->created_at->format('d M Y, H:i') }} WIB @if ($order->requested_delivery_at) · Pengantaran {{ $order->requested_delivery_at->format('d M Y, H:i') }}@endif</p></div>
        <div class="order-hero-actions"><a class="button button-outline" href="{{ route('admin.orders.index') }}"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
            @if ($order->status === 'delivered')
                <a class="button button-primary" href="{{ route('admin.orders.complete', $order) }}"><span class="material-symbols-outlined">verified</span> Lihat Konfirmasi</a>
            @else
                <a class="button button-primary" href="#order-status"><span class="material-symbols-outlined">sync</span> Perbarui Status</a>
            @endif
        </div>
    </section>
    <div class="order-detail-grid">
        <div class="form-column">
            <section class="panel detail-customer">
                <div class="customer-avatar">{{ Str::upper(Str::substr($order->customer_name, 0, 1)) }}</div>
                <div><span class="eyebrow">PENERIMA RANGKAIAN</span><h2>{{ $order->customer_name }}</h2><p>{{ $order->phone }}</p><small>{{ $order->delivery_address ?: 'Alamat pengantaran belum dicatat' }}</small></div>
                <div class="occasion-chip">{{ $order->occasion ?: 'Momen spesial' }}</div>
            </section>
            <section class="panel form-panel">
                <div class="panel-heading"><div><span class="eyebrow">FLORAL ARCHITECTURE</span><h2>Komposisi &amp; Kustomisasi</h2></div><span class="count-pill">{{ $order->items->count() }} item</span></div>
                <div class="order-item-list">
                    @foreach ($order->items as $item)
                        <div class="order-item-detail"><span class="flower-thumb">{{ $item->flower ? '❀' : '✿' }}</span><div><strong>{{ $item->item_name }}</strong><small>{{ $item->quantity }} item · Rp {{ number_format($item->unit_price, 0, ',', '.') }} / item</small></div><strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong></div>
                    @endforeach
                </div>
                <div class="custom-note"><span class="eyebrow">CATATAN FLORIST</span><p>{{ $order->customization_notes ?: 'Tidak ada catatan kustomisasi tambahan.' }}</p></div>
                <div class="custom-note message-note"><span class="eyebrow">KARTU UCAPAN</span><p>{{ $order->message_card ?: 'Belum ada pesan kartu ucapan.' }}</p></div>
            </section>
            <section class="panel form-panel" id="order-status">
                <div class="panel-heading"><div><span class="eyebrow">ORDER JOURNEY</span><h2>Perbarui Status Pesanan</h2></div><span class="step-number"><span class="material-symbols-outlined">timeline</span></span></div>
                <form class="status-form" method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    <label class="field"><span>Tahap pesanan</span><select name="status" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ['pending' => 'Menunggu', 'arranging' => 'Sedang dirangkai', 'ready' => 'Siap dikirim', 'dispatched' => 'Dalam pengiriman', 'delivered' => 'Selesai & terkirim', 'cancelled' => 'Dibatalkan'][$status] }}</option>@endforeach</select></label>
                    <button class="button button-primary" type="submit"><span class="material-symbols-outlined">sync</span> Simpan status</button>
                </form>
            </section>
        </div>
        <aside class="panel order-summary detail-summary">
            <span class="eyebrow">RINCIAN PESANAN</span><h2>Atelier receipt</h2>
            <div class="summary-line"><span>Subtotal rangkaian</span><strong>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</strong></div>
            <div class="summary-line"><span>Pengantaran</span><strong>{{ $order->delivery_fee ? 'Rp '.number_format($order->delivery_fee, 0, ',', '.') : 'Gratis' }}</strong></div>
            <div class="summary-total"><span>Total pesanan</span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
            <div class="receipt-status"><span class="material-symbols-outlined">verified</span><span><strong>{{ $order->status_label }}</strong><small>{{ $order->completed_at?->format('d M Y, H:i') ?: 'Pembaruan status tersimpan' }}</small></span></div>
        </aside>
    </div>
@endsection
