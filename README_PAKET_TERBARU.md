# SIBERKAS — Paket Full Revisi Terbaru

Isi utama paket:

- Role Admin, Customer Service, dan Akuntan.
- Monitoring SLA aktif dengan notifikasi berkas hampir atau melewati target.
- Transaksi administratif terhubung ke nasabah dan berkas.
- Verifikasi oleh Akuntan, jurnal umum otomatis, buku besar, piutang, serta laporan akuntansi.
- PDF dan Excel `.xlsx` **langsung terunduh**, tanpa halaman preview.
- Arsip dan bukti transaksi baru disimpan pada storage privat.
- Dashboard responsif, sidebar compact saat disembunyikan, modal tambah/edit, dan UI Uiverse.
- File SQL siap import: `SIBERKAS_DATABASE_IMPORT.sql`.

## Jalankan lokal

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan optimize:clear
php artisan migrate --seed
php artisan serve
```

Untuk pemakaian normal cukup `php artisan serve`. Asset `public/build` sudah disertakan sehingga `npm run dev` tidak diperlukan kecuali mengubah CSS atau JavaScript.

## Akun demo

- Admin: `admin@gmail.com` / `admin123`
- CS: `cs@gmail.com` / `cs123`
- Akuntan: `akuntan@gmail.com` / `akuntan123`

Ganti password demo sebelum sistem dipakai sungguhan.
