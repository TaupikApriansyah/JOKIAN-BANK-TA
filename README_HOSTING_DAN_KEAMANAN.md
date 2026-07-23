# Hosting dan Keamanan SIBERKAS

## 1. File database SQL

Paket ini menyertakan file siap import:

- `SIBERKAS_DATABASE_IMPORT.sql`
- `database/sql/siberkas_demo.sql`

Import **hanya ke database baru atau kosong** karena file SQL akan membuat ulang tabel SIBERKAS. Setelah import SQL, jangan menjalankan `php artisan migrate:fresh` atau `php artisan migrate:refresh`.

Akun demo dari SQL/seeder:

| Role | Email | Password awal |
|---|---|---|
| Admin | admin@gmail.com | admin123 |
| CS | cs@gmail.com | cs123 |
| Akuntan | akuntan@gmail.com | akuntan123 |

Ganti password seluruh akun demo sebelum aplikasi dipakai sungguhan.

## 2. Deploy ke hosting

1. Upload isi project, tetapi jangan menjadikan folder root project sebagai document root.
2. Arahkan document root domain/subdomain ke folder `public`.
3. Salin `.env.hosting.example` menjadi `.env`, lalu isi `APP_URL` dan data database hosting.
4. Jalankan dari terminal hosting:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. Pastikan folder berikut dapat ditulis oleh web server:

```text
storage/
bootstrap/cache/
```

Umumnya permission folder memakai `775` dan file `664`, tergantung pengaturan hosting.

6. Untuk database, pilih salah satu:
   - **Import SQL** melalui phpMyAdmin, lalu tidak perlu menjalankan migrate untuk instalasi baru.
   - **Tanpa import SQL**: buat database kosong lalu jalankan `php artisan migrate --seed`.

## 3. Keamanan yang sudah diterapkan

- Login dibatasi maksimal 5 percobaan per menit per kombinasi email dan IP.
- Pendaftaran akun mandiri dan reset password publik dimatikan. Akun hanya dibuat oleh Admin.
- Password tersimpan dengan hash Laravel, bukan teks biasa.
- Session diregenerasi saat login dan token CSRF Laravel aktif pada form POST/PUT/DELETE.
- CS hanya dapat membuka berkas, transaksi, arsip, dan bukti miliknya sendiri.
- Akuntan hanya mengakses area verifikasi dan laporan akuntansi.
- Akun nonaktif otomatis tidak dapat lagi masuk ke area sistem.
- Bukti pembayaran dan arsip baru disimpan di `storage/app/private`, bukan folder publik.
- File hanya diunduh melalui route yang memeriksa role dan kepemilikan data.
- Upload dibatasi tipe file dan ukuran: arsip maksimal 10 MB, bukti pembayaran maksimal 2 MB.
- Header keamanan: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `CSP` opsional, dan HSTS saat HTTPS.
- User yang masih mempunyai data terkait tidak bisa dihapus. Ubah statusnya menjadi nonaktif.

## 4. Export laporan

PDF dan Excel sekarang langsung dikirim sebagai file download. Tidak ada halaman preview/cetak lagi.

- Excel memakai format `.xlsx` asli dan membutuhkan ekstensi PHP `zip` pada hosting.
- Bila ekstensi `zip` tidak tersedia, sistem otomatis memakai CSV sebagai cadangan.
- PDF dibuat langsung oleh aplikasi, tanpa package tambahan.

## 5. Hal yang wajib dicek sebelum go live

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` memakai `https://`
- `FORCE_HTTPS=true`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_DRIVER=database`
- PHP extension aktif: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dan `zip`
- Tidak ada file `.env` yang dapat diakses dari web karena document root hanya ke `public`.
