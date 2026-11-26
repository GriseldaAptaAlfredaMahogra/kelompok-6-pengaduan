@extends('shop.layout')

@section('title', 'Ulasan Pembeli')

@section('content')
    <div class="store-section-heading">
        <div><span class="eyebrow">CERITA PELANGGAN</span><h1>Ulasan Pembeli</h1><p>Lihat pengalaman pelanggan setelah menerima rangkaian mereka.</p></div>
        <span>{{ $reviewCount }} ulasan</span>
    </div>

    <section class="store-review-summary panel" aria-label="Ringkasan rating">
        <span class="material-symbols-outlined" aria-hidden="true">star</span>
        <div><strong>{{ number_format($averageRating, 1, ',', '.') }} / 5</strong><small>Rating rata-rata dari {{ $reviewCount }} ulasan terverifikasi</small></div>
    </section>

    <section class="panel store-review-list-panel">
        <div class="review-list">
            @forelse ($reviews as $review)
                <article class="review-card">
                    <span class="review-avatar"><span class="material-symbols-outlined" aria-hidden="true">verified</span></span>
                    <div class="review-card-main">
                        <div class="review-card-heading">
                            <div><strong>Pembeli terverifikasi</strong><small>{{ $review->order->items->pluck('item_name')->join(', ') ?: 'Rangkaian Botanical Atelier' }}</small></div>
                            <time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('d M Y') }}</time>
                        </div>
                        <div class="review-stars" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                            @for ($star = 1; $star <= 5; $star++)
                                <span class="material-symbols-outlined {{ $star <= $review->rating ? 'filled' : '' }}">star</span>
                            @endfor
                        </div>
                        <p>{{ $review->comment }}</p>
                    </div>
                </article>
            @empty
                <div class="empty-state"><span class="material-symbols-outlined">rate_review</span><strong>Belum ada ulasan</strong><p>Ulasan pelanggan akan tampil di sini setelah pesanan diterima.</p></div>
            @endforelse
        </div>
        @if ($reviews->hasPages())
            <div class="pagination-wrap">{{ $reviews->links() }}</div>
        @endif
    </section>
@endsection
