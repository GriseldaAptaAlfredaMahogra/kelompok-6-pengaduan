<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Botanical Atelier') · Botanical Atelier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark">✿</span>
                <span><strong>Botanical Atelier</strong><small>FLORIST STUDIO</small></span>
            </a>
            <div class="studio-switch"><span class="material-symbols-outlined">storefront</span><span>Studio Utama<small>Menteng, Jakarta</small></span><span class="material-symbols-outlined">unfold_more</span></div>
            <nav class="side-nav" aria-label="Navigasi utama">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">dashboard</span>Ringkasan</a>
                <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><span class="material-symbols-outlined">shopping_bag</span>Pesanan Buket <span class="nav-count">{{ \App\Models\StoreOrder::whereNotIn('status', ['delivered', 'cancelled'])->count() }}</span></a>
                <a class="{{ request()->routeIs('admin.bouquets.*') ? 'active' : '' }}" href="{{ route('admin.bouquets.index') }}"><span class="material-symbols-outlined">local_florist</span>Katalog &amp; Rangkaian</a>
                <a class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}"><span class="material-symbols-outlined">psychiatry</span>Stok Bunga Segar</a>
                <a class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}"><span class="material-symbols-outlined">local_shipping</span>Supplier</a>
            </nav>
            <div class="sidebar-bottom">
                <form class="logout-form" method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><span class="material-symbols-outlined">logout</span> Keluar dari Atelier</button></form>
                <div class="temperature-card"><div><span>Suhu Pendingin</span><strong>4.2°C</strong></div><div class="temperature-track"><i></i></div><small>Kelembapan ruang 82% · Optimal</small></div>
                <span class="studio-caption">Merangkai momen, satu bunga pada satu waktu.</span>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <button class="icon-button mobile-menu" type="button" aria-label="Buka menu" data-menu-toggle><span class="material-symbols-outlined">menu</span></button>
                <div class="today-pill"><span class="status-dot"></span> Toko Buka <span>·</span> Siap Antar Hari Ini</div>
                <form class="top-search" action="{{ route('admin.orders.index') }}" method="GET">
                    <span class="material-symbols-outlined">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari pesanan, bunga, atau pelanggan...">
                </form>
                <a class="button button-primary top-new-order" href="{{ route('admin.orders.create') }}"><span class="material-symbols-outlined">add</span> Pesanan Baru</a>
                <div class="profile-chip"><span class="profile-avatar">BA</span><span><strong>Botanical Atelier</strong><small>Head Florist</small></span></div>
            </header>
            <section class="page-content">
                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error" role="alert">
                        <strong>Mohon periksa kembali data:</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
