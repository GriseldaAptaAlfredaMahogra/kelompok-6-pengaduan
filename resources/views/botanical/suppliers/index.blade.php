@extends('botanical.layout')

@section('title', 'Supplier Bunga')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">GROWERS &amp; PARTNERS</span><h1>Supplier Atelier</h1><p>Kenali mitra kebun yang menjaga setiap tangkai tetap segar.</p></div>
    </div>
    <div class="supplier-layout">
        <section class="panel">
            <div class="panel-heading"><div><span class="eyebrow">SUPPLIER DIRECTORY</span><h2>Mitra Kebun</h2></div><span class="count-pill">{{ $suppliers->count() }} mitra</span></div>
            <div class="supplier-list">
                @forelse ($suppliers as $supplier)
                    <article class="supplier-card"><div class="supplier-mark">✿</div><div class="supplier-copy"><h3>{{ $supplier->name }}</h3><p>{{ $supplier->contact_name ?: 'Kontak belum ditambahkan' }}</p><div class="supplier-contact">@if ($supplier->phone)<span><span class="material-symbols-outlined">call</span>{{ $supplier->phone }}</span>@endif @if ($supplier->email)<span><span class="material-symbols-outlined">mail</span>{{ $supplier->email }}</span>@endif</div><small>{{ $supplier->batches_count }} batch stok tercatat</small></div><span class="badge badge-ready">Mitra aktif</span></article>
                @empty
                    <div class="empty-state"><span class="material-symbols-outlined">yard</span><strong>Belum ada supplier</strong><p>Tambahkan mitra supplier untuk pencatatan stok masuk.</p></div>
                @endforelse
            </div>
        </section>
        <form class="panel form-panel compact-form" method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf
            <span class="eyebrow">NEW GROWER</span><h2>Tambah Supplier</h2>
            <label class="field"><span>Nama kebun / supplier</span><input name="name" required value="{{ old('name') }}" placeholder="Kebun Lembang Highland"></label>
            <label class="field"><span>Nama kontak</span><input name="contact_name" value="{{ old('contact_name') }}" placeholder="Nama pengelola"></label>
            <label class="field"><span>Nomor telepon</span><input name="phone" value="{{ old('phone') }}" placeholder="+62 ..."></label>
            <label class="field"><span>Email</span><input name="email" type="email" value="{{ old('email') }}" placeholder="kebun@example.id"></label>
            <label class="field"><span>Alamat</span><textarea name="address" rows="3" placeholder="Alamat kebun atau pusat distribusi">{{ old('address') }}</textarea></label>
            <button class="button button-primary full-width" type="submit"><span class="material-symbols-outlined">add</span> Simpan supplier</button>
        </form>
    </div>
@endsection
