# Checklist Route dan CRUD — Bank X

Versi ini menggunakan route eksplisit (bukan route implisit yang tersembunyi) untuk menghindari halaman **Not Found** pada tombol utama.

## URL inti

| Fitur | URL | Role |
|---|---|---|
| Dashboard | `/` | Semua user login |
| Daftar Nasabah | `/customers` | CS/Admin |
| Tambah Nasabah | `/customers/create` | CS |
| Berkas Nasabah | `/cases` | CS/Admin |
| Input Berkas Baru | `/cases/create` | CS |
| Transaksi | `/transactions` | CS/Admin |
| Monitoring SLA | `/monitoring-sla` | CS/Admin |
| Arsip Digital | `/archives` | CS/Admin |
| Verifikasi Transaksi | `/admin/transactions` | Admin |
| Laporan & Export | `/admin/reports` | Admin |

Alias bookmark lama juga tersedia:

- `/nasabah` dan `/nasabah/tambah`
- `/berkas` dan `/berkas/tambah`
- `/monitoring`
- `/arsip`

## Flow keterhubungan data

1. **CS tambah Nasabah** → data tersimpan dan langsung muncul pada pilihan **Input Berkas Baru**.
2. **CS buat Berkas** → otomatis terhubung ke nasabah, jenis layanan, CS pembuat, nomor berkas, dan SLA.
3. **CS upload Dokumen** → dokumen menempel pada berkas yang sama; status kelengkapan otomatis terbaca.
4. **CS input Transaksi dari Workspace Berkas** → transaksi otomatis membawa nomor berkas dan nasabah yang sama.
5. **Admin setujui Transaksi** → jurnal dibuat otomatis, transaksi dikunci dari edit langsung.
6. **Admin export** → file PDF/Excel langsung terunduh dan aktivitas tercatat pada audit trail.

## Kalau tombol masih mengarah ke halaman lama

Dari folder project, hentikan server lalu jalankan:

```powershell
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
php artisan serve
```

Lalu buka URL dari server baru yang ditampilkan, biasanya `http://127.0.0.1:8000`.
