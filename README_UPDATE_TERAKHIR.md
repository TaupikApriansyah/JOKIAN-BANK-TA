# Revisi Terakhir SIBERKAS

## Export langsung

- Tombol **Unduh PDF** pada laporan Admin, Akuntan, dan Arsip langsung mengunduh file PDF.
- Tombol **Unduh Excel** langsung mengunduh file `.xlsx`.
- Tidak ada lagi halaman print/preview yang membuka tab baru.

## Database SQL

- File `SIBERKAS_DATABASE_IMPORT.sql` sudah tersedia di root project.
- File tersebut berisi struktur tabel final, role Admin/CS/Akuntan, daftar akun akuntansi, dan tabel sesi hosting.

## Keamanan

- Upload arsip dan bukti transaksi baru sekarang disimpan privat pada folder `storage/app/private`.
- Tidak ada link file publik langsung untuk upload baru.
- Download file selalu lewat cek akses sesuai role.
- Login mempunyai pembatasan percobaan.
- Validasi data, file, jumlah nominal, tanggal, dan ownership diperketat.
- Hosting menggunakan `.env.hosting.example` dan panduan `README_HOSTING_DAN_KEAMANAN.md`.
