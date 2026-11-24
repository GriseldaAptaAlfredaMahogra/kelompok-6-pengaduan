<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Akun · Botanical Atelier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-card">
        <a class="brand login-brand" href="{{ route('shop.index') }}"><span class="brand-mark">✿</span><span><strong>Botanical Atelier</strong><small>FLORIST STUDIO</small></span></a>
        <span class="eyebrow">AKUN BARU, CERITA BARU</span>
        <h1>Buat akun<br>atelier Anda.</h1>
        <p>Daftar untuk menyimpan keranjang dan memantau pesanan bunga Anda.</p>
        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <strong>Mohon periksa kembali data:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <label class="field"><span>Nama lengkap</span><input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus maxlength="150" placeholder="Nama Anda"></label>
            <label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="255" placeholder="nama@email.com"></label>
            <label class="field"><span>Kata sandi</span><input type="password" name="password" autocomplete="new-password" required minlength="8" placeholder="Minimal 8 karakter"></label>
            <label class="field"><span>Konfirmasi kata sandi</span><input type="password" name="password_confirmation" autocomplete="new-password" required minlength="8" placeholder="Ulangi kata sandi"></label>
            <button class="button button-primary full-width" type="submit">Buat akun <span class="material-symbols-outlined">arrow_forward</span></button>
        </form>
        <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
        <small class="login-note">Botanical Atelier · Rangkaian bunga dengan sepenuh hati</small>
    </main>
</body>
</html>
