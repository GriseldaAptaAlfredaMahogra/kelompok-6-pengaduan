@extends('botanical.layout')

@section('title', 'Ringkasan Toko')

@section('content')
    <section class="welcome-panel">
        <div>
            <div class="eyebrows"><span class="eyebrow-chip">BOTANICAL ATELIER</span><span class="eyebrow-chip blush">WORKSHOP SIAP MERANGKAI</span></div>
            <h1>Selamat pagi, florist <span class="flower-emoji">✿</span></h1>
            <p>Pesanan hari ini tersusun rapi. Pantau rangkaian, kesegaran bunga, dan kabar pelanggan dari satu tempat.</p>
        </div>
        <div class="welcome-actions">
            <a class="button button-primary" href="{{ route('admin.orders.create') }}"><span class="material-symbols-outlined">add</span> Buat Pesanan</a>
            <a class="button button-soft" href="{{ route('admin.inventory.index') }}"><span class="material-symbols-outlined">psychiatry</span> Periksa Stok</a>
        </div>
    </section>

    <section class="metric-grid">
        <article class="metric-card"><div class="metric-label">PENJUALAN HARI INI <span class="metric-icon green"><span class="material-symbols-outlined">payments</span></span></div><strong class="metric-value">Rp {{ number_format($metrics['sales'], 0, ',', '.') }}</strong><small>Pesanan tercatat hari ini</small></article>
        <article class="metric-card"><div class="metric-label">PESANAN AKTIF <span class="metric-icon mint"><span class="material-symbols-outlined">shopping_bag</span></span></div><strong class="metric-value">{{ $metrics['active_orders'] }}</strong><small>Menunggu hingga siap dikirim</small></article>
        <article class="metric-card"><div class="metric-label">KATALOG AKTIF <span class="metric-icon sand"><span class="material-symbols-outlined">local_florist</span></span></div><strong class="metric-value">{{ $metrics['bouquets'] }}</strong><small>Rangkaian siap dipesan</small></article>
        <article class="metric-card"><div class="metric-label">VARIAN STOK RENDAH <span class="metric-icon coral"><span class="material-symbols-outlined">warning</span></span></div><strong class="metric-value">{{ $metrics['low_stock'] }}</strong><small>Perlu ditinjau sebelum dirangkai</small></article>
    </section>

    <div class="dashboard-grid">
        <section class="panel order-queue">
            <div class="panel-heading">
                <div><span class="eyebrow">ATELIER LIVE QUEUE</span><h2>Antrean Pesanan</h2></div>
                <a class="text-link" href="{{ route('admin.orders.index') }}">Lihat semua <span class="material-symbols-outlined">arrow_forward</span></a>
            </div>
            <div class="order-list">
                @forelse ($orders as $order)
                    <a class="order-row" href="{{ route('admin.orders.show', $order) }}">
                        <span class="order-thumb flower-thumb">{{ $order->status === 'delivered' ? '✿' : '❀' }}</span>
                        <span class="order-row-main"><span class="order-meta">#{{ $order->order_number }} <span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></span><strong>{{ $order->items->first()?->item_name ?? 'Pesanan buket' }}</strong><small>{{ $order->customer_name }} · {{ $order->items->sum('quantity') }} item</small></span>
                        <span class="order-row-total">Rp {{ number_format($order->total, 0, ',', '.') }}<small>{{ $order->created_at->format('H:i') }} WIB</small></span>
                    </a>
                @empty
                    <div class="empty-state"><span class="material-symbols-outlined">spa</span><strong>Antrean masih tenang</strong><p>Pesanan baru akan tampil di sini.</p><a class="button button-outline" href="{{ route('admin.orders.create') }}">Buat pesanan pertama</a></div>
                @endforelse
            </div>
        </section>

        <aside class="panel stock-panel">
            <div class="panel-heading"><div><span class="eyebrow">COLD-ROOM LEDGER</span><h2>Stok Bunga Segar</h2></div><a class="icon-button" href="{{ route('admin.inventory.index') }}" aria-label="Kelola stok"><span class="material-symbols-outlined">arrow_forward</span></a></div>
            <div class="compact-stock-list">
                @forelse ($flowers->sortBy(fn ($flower) => $flower->stock_on_hand ?? 0)->take(5) as $flower)
                    @php($stock = (int) ($flower->stock_on_hand ?? 0))
                    <div class="compact-stock-row"><span class="stock-flower-icon">{{ $loop->iteration % 2 ? '❀' : '✿' }}</span><span><strong>{{ $flower->name }} {{ $flower->variety }}</strong><small>{{ $flower->category }}</small></span><span class="stock-amount {{ $stock <= $flower->low_stock_threshold ? 'low' : '' }}">{{ $stock }}<small>{{ $flower->unit }}</small></span></div>
                @empty
                    <div class="empty-state small"><p>Belum ada data bunga. Mulai dengan mencatat varian baru.</p><a href="{{ route('admin.inventory.index') }}">Kelola stok</a></div>
                @endforelse
            </div>
            <a class="button button-soft full-width" href="{{ route('admin.inventory.index') }}">Buka ruang stok <span class="material-symbols-outlined">east</span></a>
        </aside>
    </div>

    <section class="panel category-panel">
        <div class="panel-heading"><div><span class="eyebrow">BOTANICAL COLLECTION</span><h2>Rangkaian untuk setiap momen</h2></div><a class="button button-outline" href="{{ route('admin.bouquets.index') }}">Kelola katalog</a></div>
        <div class="collection-grid">
            <a class="collection-card blush-card" href="{{ route('admin.bouquets.index') }}"><span>01 · SIGNATURE</span><strong>Hand-tied bouquet</strong><small>Rangkaian personal dari bunga pilihan.</small><span class="material-symbols-outlined collection-icon">filter_vintage</span></a>
            <a class="collection-card sage-card" href="{{ route('admin.bouquets.index') }}"><span>02 · CELEBRATION</span><strong>Standing flower</strong><small>Hadirkan kesan hangat di hari istimewa.</small><span class="material-symbols-outlined collection-icon">yard</span></a>
            <a class="collection-card cream-card" href="{{ route('admin.bouquets.index') }}"><span>03 · KEEPSAKE</span><strong>Preserved blooms</strong><small>Keindahan bunga yang lebih tahan lama.</small><span class="material-symbols-outlined collection-icon">local_florist</span></a>
        </div>
    </section>
@endsection
