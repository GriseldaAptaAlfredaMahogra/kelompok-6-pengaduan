<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ulasan Pesanan · Botanical Atelier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="review-public-shell">
        <div class="review-brand"><span class="brand-mark">✿</span><span><strong>Botanical Atelier</strong><small>FLORIST STUDIO</small></span></div>
        <section class="review-public-card">
            <div class="review-public-icon"><span class="material-symbols-outlined">{{ $order->review ? 'favorite' : 'rate_review' }}</span></div>
            <span class="eyebrow">CATATAN DARI PELANGGAN</span>
            @if ($order->review)
                <h1>Terima kasih, {{ $order->customer_name }}.</h1>
                <p>Ulasan untuk pesanan <strong>#{{ $order->order_number }}</strong> sudah kami terima.</p>
                <div class="review-stars submitted-stars" role="img" aria-label="Rating {{ $order->review->rating }} dari 5">
                    @for ($star = 1; $star <= 5; $star++)
                        <span class="material-symbols-outlined {{ $star <= $order->review->rating ? 'filled' : '' }}">star</span>
                    @endfor
                </div>
                <blockquote class="submitted-comment">{{ $order->review->comment }}</blockquote>
            @else
                <h1>Bagaimana rangkaian bunga Anda?</h1>
                <p>Halo <strong>{{ $order->customer_name }}</strong>, cerita Anda membantu kami merangkai momen yang lebih indah.</p>
                <div class="review-order-note"><span class="material-symbols-outlined">local_florist</span><span>Pesanan <strong>#{{ $order->order_number }}</strong><small>{{ $order->items->pluck('item_name')->join(', ') }}</small></span></div>
                <form class="review-form" method="POST" action="{{ $formAction }}">
                    @csrf
                    <fieldset class="rating-field">
                        <legend>Berapa bintang untuk pengalaman Anda?</legend>
                        <div class="rating-choices">
                            @for ($rating = 1; $rating <= 5; $rating++)
                                <label class="rating-choice">
                                    <input type="radio" name="rating" value="{{ $rating }}" required @checked((string) old('rating') === (string) $rating)>
                                    <span class="material-symbols-outlined" aria-hidden="true">star</span>
                                    <span class="sr-only">{{ $rating }} bintang</span>
                                </label>
                            @endfor
                        </div>
                    </fieldset>
                    @error('rating')<p class="review-error">{{ $message }}</p>@enderror
                    <label class="review-label" for="comment">Ceritakan pengalaman Anda</label>
                    <textarea id="comment" name="comment" rows="5" minlength="10" maxlength="2000" placeholder="Apa yang paling Anda sukai dari rangkaian kami?" required>{{ old('comment') }}</textarea>
                    @error('comment')<p class="review-error">{{ $message }}</p>@enderror
                    <button class="button button-primary full-width" type="submit"><span class="material-symbols-outlined">send</span> Kirim ulasan</button>
                </form>
            @endif
        </section>
        <p class="review-footer">Dirangkai dengan sepenuh hati · Botanical Atelier</p>
    </main>
</body>
</html>
