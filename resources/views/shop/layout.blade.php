<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Bunga') · Botanical Atelier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="storefront">
    <header class="store-header">
        <a class="brand store-brand" href="{{ route('shop.index') }}"><span class="brand-mark">✿</span><span><strong>Botanical Atelier</strong><small>FLORIST STUDIO</small></span></a>
        <nav class="store-nav" aria-label="Navigasi toko">
            <a href="{{ route('shop.index') }}">Koleksi</a>
            @auth
                <a href="{{ route('customer.orders.index') }}">Pesanan Saya</a>
            @endauth
        </nav>
        <div class="store-actions">
            <a class="button button-outline" href="{{ route('cart.index') }}"><span class="material-symbols-outlined">shopping_bag</span>Keranjang</a>
            @auth
                @if (auth()->user()->isAdmin())
                    <a class="store-account-link" href="{{ route('admin.dashboard') }}">Panel Atelier</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="store-account-link store-logout" type="submit">Keluar</button></form>
            @else
                <a class="store-account-link" href="{{ route('login') }}">Masuk</a>
                <a class="button button-primary" href="{{ route('register') }}">Buat akun</a>
            @endauth
        </div>
    </header>
    <main class="store-shell">
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning" role="status">{{ session('warning') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <strong>Mohon periksa kembali data:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    <footer class="store-footer"><span>✿</span> Botanical Atelier <small>Dirangkai dengan sepenuh hati.</small></footer>
</body>
</html>
