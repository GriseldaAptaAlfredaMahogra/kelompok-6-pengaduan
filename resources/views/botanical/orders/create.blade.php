@extends('botanical.layout')

@section('title', 'Pesanan Baru')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">CUSTOM BOUQUET ORDER</span><h1>Rancang Pesanan Baru</h1><p>Catat detail penerima dan beri sentuhan personal pada rangkaian.</p></div>
        <a class="button button-outline" href="{{ route('admin.orders.index') }}"><span class="material-symbols-outlined">arrow_back</span> Kembali ke pesanan</a>
    </div>
    <form class="order-create-grid" method="POST" action="{{ route('admin.orders.store') }}" data-order-form>
        @csrf
        <div class="form-column">
            <section class="panel form-panel">
                <div class="panel-heading"><div><span class="eyebrow">STEP 01 · THE RECIPIENT</span><h2>Detail Pelanggan</h2></div><span class="step-number">01</span></div>
                <div class="form-grid">
                    <label class="field"><span>Nama penerima / pelanggan</span><input name="customer_name" value="{{ old('customer_name') }}" required maxlength="150" placeholder="Sarah Kinanti"></label>
                    <label class="field"><span>Nomor WhatsApp</span><input name="phone" type="tel" value="{{ old('phone') }}" required maxlength="40" placeholder="+62 812 ..."></label>
                    <label class="field"><span>Momen spesial</span><input name="occasion" value="{{ old('occasion') }}" maxlength="120" placeholder="Wisuda, ulang tahun, anniversary"></label>
                    <label class="field"><span>Waktu pengantaran</span><input name="requested_delivery_at" type="datetime-local" value="{{ old('requested_delivery_at') }}"></label>
                    <label class="field field-wide"><span>Alamat pengantaran</span><textarea name="delivery_address" rows="2" maxlength="1000" placeholder="Alamat lengkap penerima">{{ old('delivery_address') }}</textarea></label>
                </div>
            </section>
            <section class="panel form-panel">
                <div class="panel-heading"><div><span class="eyebrow">STEP 02 · FLORAL ARCHITECTURE</span><h2>Pilih Rangkaian Utama</h2></div><span class="step-number">02</span></div>
                @if ($bouquets->isEmpty())
                    <div class="empty-note">Katalog masih kosong. <a href="{{ route('admin.bouquets.create') }}">Tambahkan rangkaian</a> sebelum membuat pesanan.</div>
                @else
                    <div class="bouquet-choice-list">
                        @foreach ($bouquets as $bouquet)
                            <label class="bouquet-choice">
                                <input type="radio" name="bouquet_id" value="{{ $bouquet->id }}" data-price="{{ $bouquet->price }}" @checked(old('bouquet_id', $bouquets->first()->id) == $bouquet->id) required>
                                <span class="choice-marker"></span>
                                <span class="choice-main"><strong>{{ $bouquet->name }}</strong><small>{{ $bouquet->category }} · {{ Str::limit($bouquet->description, 76) }}</small></span>
                                <strong class="choice-price">Rp {{ number_format($bouquet->price, 0, ',', '.') }}</strong>
                            </label>
                        @endforeach
                    </div>
                    <label class="field quantity-field"><span>Jumlah rangkaian</span><input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" required data-bouquet-quantity></label>
                @endif
            </section>
            <section class="panel form-panel">
                <div class="panel-heading"><div><span class="eyebrow">STEP 03 · PERSONAL TOUCH</span><h2>Personalisasi &amp; Bunga Tambahan</h2></div><span class="step-number">03</span></div>
                <p class="form-hint">Pilih bunga tambahan bila dibutuhkan. Stok tersedia ditampilkan sebagai panduan florist.</p>
                <div class="flower-option-list">
                    @forelse ($flowers as $flower)
                        @php($stock = (int) ($flower->stock_on_hand ?? 0))
                        <label class="flower-option">
                            <span><strong>{{ $flower->name }} {{ $flower->variety }}</strong><small>{{ $flower->category }} · {{ $stock }} {{ $flower->unit }} tersedia</small></span>
                            <span class="flower-quantity"><span>+ Rp 15.000 / {{ $flower->unit }}</span><input type="number" name="flower_items[{{ $flower->id }}]" min="0" max="100" value="{{ old('flower_items.'.$flower->id, 0) }}" aria-label="Jumlah {{ $flower->name }}"></span>
                        </label>
                    @empty
                        <p class="form-hint">Belum ada data bunga segar. Tambahkan dari halaman stok.</p>
                    @endforelse
                </div>
                <label class="field"><span>Catatan kustomisasi rangkaian</span><textarea name="customization_notes" rows="3" maxlength="2000" placeholder="Warna pembungkus, ukuran, pita, atau arahan khusus...">{{ old('customization_notes') }}</textarea></label>
                <label class="field"><span>Pesan kartu ucapan</span><textarea name="message_card" rows="3" maxlength="1000" placeholder="Tuliskan pesan kecil dari pengirim...">{{ old('message_card') }}</textarea></label>
            </section>
        </div>
        <aside class="panel order-summary">
            <span class="eyebrow">ORDER SUMMARY</span><h2>Ringkasan Rangkaian</h2>
            <div class="summary-flower-mark">✿</div>
            <p class="summary-selected" data-selected-bouquet>{{ $bouquets->first()?->name ?? 'Pilih rangkaian utama' }}</p>
            <div class="summary-line"><span>Jumlah</span><strong data-summary-quantity>1</strong></div>
            <div class="summary-line"><span>Pengantaran</span><strong>Atelier delivery</strong></div>
            <div class="summary-total"><span>Estimasi total</span><strong data-summary-total>Rp {{ number_format($bouquets->first()?->price ?? 0, 0, ',', '.') }}</strong></div>
            <p class="summary-disclaimer">Bunga musiman dapat memiliki variasi kecil. Tim atelier akan menghubungi pelanggan bila ada penyesuaian.</p>
            <button class="button button-primary full-width" type="submit" @disabled($bouquets->isEmpty())><span class="material-symbols-outlined">check_circle</span> Simpan Pesanan</button>
        </aside>
    </form>
@endsection
