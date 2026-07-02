# Bank X — Customer Service Administration System

Prototipe **Laravel 12 + MySQL/MariaDB** untuk administrasi layanan nasabah internal bank. Sistem menerapkan pemisahan peran **CS sebagai Maker** dan **Admin/Supervisor sebagai Checker**.

> Ini adalah prototipe TA untuk proses administrasi layanan. Sistem ini bukan core banking dan tidak terintegrasi dengan rekening atau General Ledger produksi.

---

## Modul yang tersedia

| Modul | CS / Maker | Admin / Checker |
|---|---|---|
| Login dan RBAC | Login sesuai akun | Login sesuai akun |
| Data Nasabah | Tambah dan melihat nasabah yang ditugaskan | Monitoring seluruh nasabah |
| Berkas Layanan | Input berkas, upload dokumen, proses dan tutup berkas | Monitoring seluruh berkas |
| Monitoring SLA | Menerima notifikasi SLA miliknya | Monitoring dan menerima notifikasi seluruh SLA |
| Transaksi Administrasi | Draft, ubah, submit transaksi | Verifikasi / kembalikan transaksi |
| Jurnal Pendukung | Melihat status transaksi | Jurnal otomatis terbentuk saat approve |
| Koreksi Transaksi | Ajukan koreksi transaksi approved | Setujui/tolak, jurnal pembalik, draft pengganti |
| Arsip Digital | Upload dan unduh arsip berkas sendiri | Monitoring dan unduh seluruh arsip sesuai otorisasi |
| Rekonsiliasi Kas | — | Rekonsiliasi harian penerimaan sistem vs kas/setoran |
| Laporan & Export | — | Excel (.xlsx) dan PDF langsung terunduh sebagai tabel |
| Audit Trail | — | Monitoring aktivitas user, IP, device, before-after value |
| Manajemen User | — | Buat akun dan aktif/nonaktifkan akun |

---

## Alur proses inti

```text
CS input nasabah + berkas
        ↓
Sistem membuat nomor berkas dan batas SLA
        ↓
CS upload dokumen awal
        ↓
Dokumen lengkap?
├── Tidak → Menunggu Dokumen → CS melengkapi dokumen
└── Ya → CS memproses layanan
             ↓
     [SLA dipantau paralel setiap 5 menit]
             ↓
Ada biaya administrasi?
├── Tidak → Arsip final → Berkas selesai
└── Ya → CS membuat/submit transaksi
           ↓
       Admin verifikasi (Maker–Checker)
       ├── Dikembalikan → CS ubah lalu ajukan ulang
       └── Disetujui → Jurnal otomatis + bukti transaksi
                         ↓
                   Arsip final → Berkas selesai
```

### Alur koreksi transaksi approved

```text
CS ajukan koreksi
        ↓
Admin verifikasi
├── Ditolak → transaksi lama tetap berlaku
└── Disetujui
      ↓
Sistem membuat jurnal pembalik
      ↓
Transaksi lama berstatus Dikoreksi
      ↓
Sistem membuat draft transaksi pengganti
      ↓
CS submit ulang → Admin verifikasi ulang
```

---

## Prasyarat

- PHP **8.2+** dengan extension: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, dan `ctype`.
- Composer 2.
- MySQL 8+ atau MariaDB 10.4+.
- XAMPP boleh dipakai untuk MySQL/MariaDB lokal.

---

## Setup MySQL / MariaDB

1. Jalankan MySQL dari XAMPP.
2. Buka phpMyAdmin lalu import file **`database/mysql/bank_ta_full.sql`** untuk skema + data demo siap pakai, atau bila ingin memakai migration Laravel, import `database/mysql/01_create_database.sql`, lalu jalankan:

```sql
CREATE DATABASE bank_ta
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

3. Salin konfigurasi environment:

```bash
cp .env.example .env
```

4. Sesuaikan bagian database pada `.env` bila MySQL kamu memakai password atau port berbeda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bank_ta
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

5. Install dependency dan siapkan aplikasi:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

6. Buka aplikasi di `http://127.0.0.1:8000`.

> Untuk SLA otomatis, jalankan scheduler pada terminal kedua:

```bash
php artisan schedule:work
```

Alternatif manual untuk menguji pembaruan SLA:

```bash
php artisan bank:sla-refresh
```

---

## Akun demo

| Role | ID Pegawai / Login | Password |
|---|---|---|
| CS / Maker | `CS-001` atau `rina@bankx.test` | `password123` |
| Admin / Checker | `ADM-001` atau `admin@bankx.test` | `password123` |

Ganti password akun demo setelah login pertama bila proyek digunakan di luar lingkungan demo.

---

## Struktur folder utama

```text
app/
├── Console/Commands/         # Scheduler pembaruan SLA
├── Enums/                    # Role dan status domain
├── Http/
│   ├── Controllers/          # Controller per domain / use case
│   ├── Middleware/           # RBAC server-side
│   └── Requests/             # Validasi form terpisah
├── Models/                   # Eloquent models
├── Providers/                # Rate limit login + shared view data
└── Services/                 # SLA, maker-checker, nomor referensi, audit

database/
├── migrations/               # Skema MySQL/MariaDB
├── mysql/                    # SQL pembuatan database
└── seeders/                  # Data demo

resources/views/
├── admin/                    # Approval, koreksi, rekonsiliasi, laporan, audit, user
├── archives/
├── auth/
├── cases/
├── customers/
├── notifications/
├── sla/
└── transactions/
```

---

## Kontrol keamanan yang diterapkan

- Role berasal dari database, bukan dropdown/checkbox di login.
- CS hanya dapat mengakses nasabah dan berkas yang ditugaskan kepadanya.
- Endpoint write CS diproteksi middleware `role:cs`; endpoint approval Admin diproteksi `role:admin`.
- Maker tidak dapat menyetujui transaksi atau koreksi transaksinya sendiri.
- Transaction approval dan pembuatan jurnal memakai `DB::transaction()` serta `lockForUpdate()`.
- Transaksi approved tidak dapat dihapus atau diedit langsung.
- Koreksi approved membuat jurnal pembalik dan record pengganti; histori lama tetap ada.
- NIK dan nomor rekening dienkripsi dengan Laravel `Crypt`, lalu dimasking di UI.
- Arsip disimpan pada local private disk dan diunduh lewat route berotorisasi.
- Login dibatasi 5 percobaan per menit per kombinasi login/IP.
- Session database dienkripsi dan durasinya 15 menit pada konfigurasi demo.
- Audit trail merekam pengguna, role, modul, aksi, objek, before/after value, IP, user agent, serta timestamp.
- Export laporan dan download arsip juga tercatat di audit trail.

---

## Catatan pengembangan produksi

Untuk implementasi produksi bank, tetap diperlukan hardening tambahan: MFA, password policy, HTTPS paksa, WAF, antivirus scanning upload, SIEM, backup terenkripsi, key rotation, penetration test, disaster recovery, integrasi ke SOP retensi dokumen, serta review kepatuhan internal/regulator.

---

## File yang jangan di-upload ke GitHub / dibagikan

```text
.env
vendor/
node_modules/
storage/logs/
storage/app/private/
```

## UI Design
Aplikasi menggunakan tampilan dashboard modern bergaya *FinSet Analytics*: sidebar terang, kartu bersudut lembut, aksen ungu, visualisasi Chart.js, dan tabel operasional yang ringan. Seluruh konten telah disesuaikan untuk operasional Bank X (bukan aplikasi keuangan personal).

## Tema UI Cream–Orange

Versi ini menggunakan palet cream–orange dan pola dekoratif Uiverse pada halaman login serta strip header. Pola dipakai sebagai elemen branding; area tabel, status SLA, dan form tetap memakai latar terang agar informasi operasional tetap mudah dibaca.


## UI Kit V6 — Uiverse Components
Aplikasi memakai komponen interaktif yang telah disesuaikan dengan palet cream–orange Bank X:

- Kartu ringkasan 3D (hover halus) pada dashboard dan laporan.
- Tombol aksi dengan efek reveal; tombol unduh khusus dan tombol aksi destruktif khusus.
- Upload file drag-and-drop untuk arsip/dokumen transaksi.
- Input operasional dengan fokus kontras tinggi.
- Checkbox `Ingat perangkat ini` dengan animasi splash.

Pengaturan visual utama berada di `public/css/uiverse-components.css`. Semua animasi menghormati `prefers-reduced-motion`.


---

## Revisi Final UI, CRUD, Export dan SQL

- Palet UI memakai **navy–blue** yang lebih tenang; tema orange telah dihapus.
- Referensi visual yang diberikan digunakan sebagai hero visual halaman login; bukan sebagai data nasabah.
- Sidebar dapat diciutkan di desktop serta menjadi drawer yang dapat dibuka/tutup di mobile.
- Seluruh form menggunakan input kontras dengan focus state yang nyaman, tanpa efek glitch/warna gelap yang mengganggu proses input.
- CRUD utama tersedia untuk Nasabah, Berkas, Arsip Dokumen, Transaksi Draft, Master Jenis Layanan, dan User.
- Penghapusan data sensitif dilakukan melalui cancel/deactivate agar histori transaksi dan audit trail tidak hilang.
- Export laporan transaksi langsung menjadi **file Excel (.xlsx)** atau **file PDF tabel**—tanpa halaman preview dan tanpa dialog cetak browser.
- File import phpMyAdmin lengkap: `database/mysql/bank_ta_full.sql`.

Lihat `docs/CRUD_AND_FLOW.md` untuk alur CRUD dan flow maker–checker yang dipakai aplikasi.

---

## Patch V9 — Perbaikan Link Not Found dan Keterhubungan CRUD

Versi V9 memperbaiki penyebab utama halaman `Not Found` pada tombol seperti **Tambah Nasabah** dan **Input Berkas Baru**. Route statis berikut sekarang dideklarasikan **sebelum** route detail berparameter:

- `/customers/create` sebelum `/customers/{customer}`
- `/cases/create` sebelum `/cases/{serviceCase}`

Dengan urutan ini, kata `create` tidak lagi mungkin dibaca sebagai ID data.

### Setelah mengganti folder project

Jaga file `.env` milikmu, lalu dari folder project jalankan:

```powershell
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan serve
```

Atau klik dua kali `refresh_routes_windows.bat` pada Windows.

### Cek cepat

```powershell
php artisan route:list --path=customers
php artisan route:list --path=cases
```

Harus terlihat minimal URL berikut:

```text
GET|HEAD  customers/create
GET|HEAD  cases/create
POST      customers
POST      cases
```

### Alur data yang sudah tersambung

`Nasabah → Berkas Layanan → Dokumen + SLA → Transaksi → Approval Admin → Jurnal → Arsip / Laporan`

- Saat membuat **Berkas**, daftar nasabah hanya memuat nasabah aktif yang ditangani CS login.
- Saat membuka detail **Nasabah**, tombol `Buat Berkas` langsung memilih nasabah itu pada formulir berkas.
- Saat membuat **Transaksi** di workspace berkas, nomor berkas dan nasabah ditautkan otomatis oleh backend.
- Dokumen, transaksi, dan jurnal tetap memiliki histori; data finansial tidak dihapus fisik.
