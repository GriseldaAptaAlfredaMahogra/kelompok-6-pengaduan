@extends('botanical.layout')

@section('title', 'Pesanan Buket')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">ATELIER LIVE QUEUE</span><h1>Pesanan Buket</h1><p>Ikuti setiap rangkaian dari catatan pertama hingga sampai ke tangan penerima.</p></div>
        <a class="button button-primary" href="{{ route('admin.orders.create') }}"><span class="material-symbols-outlined">add</span> Pesanan Baru</a>
    </div>
    <section class="panel">
        <div class="panel-heading"><div><span class="eyebrow">ORDER JOURNAL</span><h2>Semua pesanan</h2></div><span class="count-pill">{{ $orders->total() }} pesanan</span></div>
        <div class="table-wrap">
            <table class="data-table orders-table"><thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Rangkaian</th><th>Tanggal dibuat</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->order_number }}</strong><small class="table-subtext">{{ $order->occasion ?: 'Pesanan atelier' }}</small></td>
                        <td>{{ $order->customer_name }}<small class="table-subtext">{{ $order->phone }}</small></td>
                        <td>{{ $order->items->first()?->item_name ?? 'Rangkaian custom' }}<small class="table-subtext">{{ $order->items->sum('quantity') }} item</small></td>
                        <td>{{ $order->created_at->format('d M Y') }}<small class="table-subtext">{{ $order->created_at->format('H:i') }} WIB</small></td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                        <td><a class="icon-button" href="{{ route('admin.orders.show', $order) }}" aria-label="Buka detail pesanan"><span class="material-symbols-outlined">arrow_forward</span></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-empty">Belum ada pesanan. Buat pesanan pertama untuk memulai antrean.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    </section>
@endsection
