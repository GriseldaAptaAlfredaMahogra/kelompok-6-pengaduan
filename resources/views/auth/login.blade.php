<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Botanical Atelier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-card">
        <a class="brand login-brand" href="{{ route('login') }}"><span class="brand-mark">✿</span><span><strong>Botanical Atelier</strong><small>FLORIST STUDIO</small></span></a>
        <span class="eyebrow">WELCOME BACK TO THE ATELIER</span>
        <h1>Ruang merangkai<br>menanti Anda.</h1>
        <p>Masuk untuk mengelola pesanan, koleksi, dan bunga segar hari ini.</p>
        @if ($errors->any())
            <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label class="field"><span>Email atelier</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="nama@botanicalatelier.id"></label>
            <label class="field"><span>Kata sandi</span><input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan kata sandi"></label>
            <label class="check-field remember-field"><input type="checkbox" name="remember" value="1"><span>Ingat perangkat ini</span></label>
            <button class="button button-primary full-width" type="submit">Masuk ke Atelier <span class="material-symbols-outlined">arrow_forward</span></button>
        </form>
        <small class="login-note">Botanical Atelier · Studio Utama, Menteng</small>
    </main>
</body>
</html>
