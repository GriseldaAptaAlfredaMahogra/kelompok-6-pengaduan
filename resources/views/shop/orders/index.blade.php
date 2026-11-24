@extends('shop.layout')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="store-section-heading">
        <div><span class="eyebrow">AKUN ANDA</span><h1>Riwayat pesanan</h1></div>
        <a class="button button-primary" href="{{ route('shop.index') }}">Lihat koleksi</a>
    </div>
    @forelse ($orders as $order)
        <a class="customer-order-row panel" href="{{ route('customer.orders.show', $order) }}">
            <div><span class="eyebrow">{{ $order->order_number }}</span><h2>{{ $order->items->pluck('item_name')->join(', ') }}</h2><small>{{ $order->created_at->format('d M Y, H:i') }}</small></div>
            <div class="customer-order-meta"><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span><strong>Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></div>
        </a>
    @empty
        <div class="empty-state panel"><span class="material-symbols-outlined">receipt_long</span><strong>Belum ada pesanan</strong><p>Pesanan yang Anda buat akan muncul di sini.</p><a class="button button-primary" href="{{ route('shop.index') }}">Jelajahi koleksi</a></div>
    @endforelse
    @if ($orders->hasPages())
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    @endif
@endsection
