@extends('botanical.layout')

@section('title', 'Ulasan Pembeli')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">CUSTOMER NOTES</span><h1>Ulasan Pembeli</h1><p>Dengar kesan pelanggan setelah rangkaian sampai di tangan mereka.</p></div>
    </div>

    <section class="metric-grid review-metrics">
        <article class="metric-card"><div class="metric-label">RATING RATA-RATA <span class="metric-icon sand"><span class="material-symbols-outlined">star</span></span></div><strong class="metric-value">{{ number_format($averageRating, 1, ',', '.') }} <span class="rating-total">/ 5</span></strong><small>Dari {{ $reviewCount }} ulasan pembeli</small></article>
        <article class="metric-card"><div class="metric-label">ULASAN MASUK <span class="metric-icon green"><span class="material-symbols-outlined">reviews</span></span></div><strong class="metric-value">{{ $reviewCount }}</strong><small>Masukan terverifikasi dari pesanan</small></article>
        <article class="metric-card"><div class="metric-label">RATING 5 BINTANG <span class="metric-icon mint"><span class="material-symbols-outlined">favorite</span></span></div><strong class="metric-value">{{ $ratingCounts->get(5, 0) }}</strong><small>Pelanggan sangat puas</small></article>
    </section>

    <div class="reviews-layout">
        <section class="panel rating-panel">
            <div class="panel-heading"><div><span class="eyebrow">RATING OVERVIEW</span><h2>Sebaran rating</h2></div></div>
            <div class="rating-distribution">
                @for ($rating = 5; $rating >= 1; $rating--)
                    @php($count = $ratingCounts->get($rating, 0))
                    <div class="rating-distribution-row"><span>{{ $rating }} <span class="material-symbols-outlined">star</span></span><div class="rating-track"><i style="width: {{ $reviewCount ? round($count / $reviewCount * 100) : 0 }}%"></i></div><small>{{ $count }}</small></div>
                @endfor
            </div>
        </section>

        <section class="panel review-list-panel">
            <div class="panel-heading">
                <div><span class="eyebrow">BUYER JOURNAL</span><h2>Suara pelanggan</h2></div>
                <form class="review-filter" method="GET" action="{{ route('admin.reviews.index') }}">
                    <label class="sr-only" for="rating-filter">Filter rating</label>
                    <select id="rating-filter" name="rating">
                        <option value="">Semua rating</option>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected((string) request('rating') === (string) $rating)>{{ $rating }} bintang</option>
                        @endfor
                    </select>
                    <button class="button button-outline" type="submit">Terapkan</button>
                </form>
            </div>
            <div class="review-list">
                @forelse ($reviews as $review)
                    <article class="review-card">
                        <span class="review-avatar">{{ mb_substr($review->order->customer_name, 0, 1) }}</span>
                        <div class="review-card-main">
                            <div class="review-card-heading"><div><strong>{{ $review->order->user?->name ?? $review->order->customer_name }}</strong><small>#{{ $review->order->order_number }} @if ($review->order->user) · {{ $review->order->user->email }} @endif · {{ $review->order->items->pluck('item_name')->join(', ') }}</small></div><time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('d M Y') }}</time></div>
                            <div class="review-stars" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span class="material-symbols-outlined {{ $star <= $review->rating ? 'filled' : '' }}">star</span>
                                @endfor
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    </article>
                @empty
                    <div class="empty-state"><span class="material-symbols-outlined">rate_review</span><strong>Belum ada ulasan</strong><p>Setelah pesanan selesai, bagikan tautan ulasan kepada pembeli.</p><a class="button button-outline" href="{{ route('admin.orders.index') }}">Lihat pesanan selesai</a></div>
                @endforelse
            </div>
            <div class="pagination-wrap">{{ $reviews->links() }}</div>
        </section>
    </div>
@endsection
