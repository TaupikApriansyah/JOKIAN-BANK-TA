# Cara Menjalankan SIBERKAS

## A. Lokal dengan migration Laravel

1. Salin `.env.example` menjadi `.env`.
2. Isi koneksi MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siberkas
DB_USERNAME=root
DB_PASSWORD=
```

3. Buat database `siberkas` pada phpMyAdmin.
4. Jalankan:

```powershell
composer install
php artisan key:generate
php artisan optimize:clear
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## B. Lokal/hosting dengan file SQL

1. Buat database kosong di phpMyAdmin.
2. Import `SIBERKAS_DATABASE_IMPORT.sql`.
3. Sesuaikan `.env` dengan database yang dipakai.
4. Jalankan:

```powershell
composer install
php artisan key:generate
php artisan optimize:clear
php artisan config:clear
php artisan serve
```

> File SQL berisi `DROP TABLE`, jadi hanya import pada database baru/kosong. Jangan menjalankan `migrate:fresh` atau `migrate:refresh` pada database yang berisi data penting.

## Update dari versi project lama

Jangan import SQL ke database lama. Gunakan:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
php artisan serve
```

Migrasi ini juga menghapus kolom lama `tracking_status.id_berkas` yang tidak digunakan agar proses tracking tidak error.

## Perlu `npm run dev`?

Tidak perlu untuk pemakaian normal. Cukup:

```powershell
php artisan serve
```

Jalankan `npm install` lalu `npm run build` hanya ketika kamu mengubah file pada `resources/css` atau `resources/js`.

## Alur modul Akuntansi

```text
CS input nasabah dan berkas
        ↓
CS mencatat transaksi + bukti pembayaran
        ↓
Status: Menunggu Verifikasi
        ↓
Akuntan memeriksa transaksi dan posting jurnal
        ↓
Jurnal umum, buku besar, piutang, dan laporan diperbarui
```

## Batasan scope

Sistem mencatat transaksi administratif terkait layanan dan berkas nasabah. Sistem tidak mengelola saldo rekening, transfer, tabungan, kredit, deposito, bunga, atau transaksi inti bank.
