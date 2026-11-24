# Botanical Atelier

Panel admin toko buket bunga berbasis Laravel Blade. Antarmuka mengikuti empat layar Google Stitch yang disimpan di `stitch_flower_bouquet_store_dashboard/`: ringkasan, katalog cold-room/restock, detail kustomisasi pesanan, dan konfirmasi pesanan selesai.

## Kebutuhan

- PHP 8.3 atau lebih baru dan Composer
- MySQL 8+ (atau MariaDB yang kompatibel)
- Node.js dan npm

## Menjalankan aplikasi

1. Pasang dependensi PHP dan JavaScript:

   ```sh
   composer install
   npm install
   ```

2. Jika `.env` belum ada, salin `.env.example` menjadi `.env`. Jika sudah ada, pertahankan file itu dan sesuaikan hanya `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` dengan MySQL lokal.

3. Siapkan database dan aset:

   ```sh
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

4. Buat akun pemilik atelier satu kali (ganti nilai contoh dengan email dan kata sandi pilihan Anda). Akun yang dibuat sebelum fitur pendaftaran pelanggan tersedia tetap memiliki akses panel admin:

   ```sh
   php artisan tinker
   ```

   ```php
   \App\Models\User::create([
       'name' => 'Pemilik Atelier',
       'email' => 'pemilik@botanicalatelier.id',
       'password' => 'ganti-dengan-kata-sandi-kuat',
   ]);
   ```

5. Jalankan server:

   ```sh
   php artisan serve
   ```

Buka `http://127.0.0.1:8000` untuk melihat koleksi dan melakukan pembelian. Pelanggan dapat membuat akun di `/register` atau masuk melalui `/login`; keranjang dan riwayat pesanan tersimpan untuk akun tersebut. Masuk dengan akun pemilik atelier untuk mengelola toko di `/admin`.

## Fitur

- Dashboard ringkasan penjualan, pesanan aktif, katalog, dan peringatan stok.
- CRUD katalog rangkaian (pembuatan, pengubahan, dan arsip soft).
- Pencatatan bunga, batch stok/cold-room, tanggal kedaluwarsa, dan restock supplier.
- Direktori supplier.
- Pembuatan pesanan dengan detail penerima, bunga tambahan, catatan kustomisasi, dan kartu ucapan.
- Registrasi pelanggan dengan password ter-hash, keranjang per akun, checkout, dan riwayat pesanan pribadi.
- Perubahan status pesanan hingga `Selesai & Terkirim`.

Foto produk pada desain Stitch dihosting oleh Google. Jika URL sumber tidak lagi tersedia, tambahkan foto milik toko melalui kolom URL gambar katalog atau sediakan aset lokal.
