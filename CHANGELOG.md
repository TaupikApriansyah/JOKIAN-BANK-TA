# Changelog

## 2.1.0 — CRUD Pop-up Interface

- Menambahkan komponen modal/pop-up reusable dengan dukungan keyboard Escape, backdrop, focus awal, dan tampilan responsif.
- Mengubah CRUD Nasabah, Berkas, Master Layanan, dan Manajemen User menjadi pop-up.
- Mengubah upload/edit/hapus Dokumen serta tambah/detail/edit/batal Transaksi pada workspace menjadi pop-up.
- Menambahkan pop-up konfirmasi untuk status, penolakan, pembatalan, dan penghapusan draft.
- Membuka kembali pop-up yang tepat saat validasi gagal dan menampilkan ringkasan error di dalam modal.
- Mempertahankan route create/edit lama sebagai fallback kompatibilitas.
- Memperbaiki update nasabah agar NIK/rekening terenkripsi tidak terhapus ketika input perubahan dikosongkan.
- Memperbaiki update berkas agar jenis layanan lama yang sudah nonaktif tetap dapat dipertahankan.
- Menjaga pengguna tetap kembali ke workspace setelah mengubah atau membatalkan transaksi dari pop-up.

## 2.0.0 — Accountant Role Separation

- Menambahkan role `accountant` dan akun demo `ACC-001`.
- Memisahkan menu Akuntan dari Admin/Checker.
- Mengubah approval transaksi agar menghasilkan draft jurnal.
- Menambahkan pemeriksaan dan posting jurnal oleh Akuntan.
- Menambahkan dashboard Akuntan, daftar/detail jurnal, buku besar, rekonsiliasi, dan laporan.
- Memindahkan rekonsiliasi dan laporan akuntansi dari Admin ke Akuntan.
- Menambahkan audit trail untuk posting jurnal dan export laporan.
- Menambahkan migrasi kompatibilitas untuk database versi lama.
- Memperbarui SQL import, dokumentasi instalasi, dan pengujian role.
- Memperbaiki directive Blade yang berdempetan pada detail transaksi.
- Memperbaiki perhitungan rekonsiliasi agar jurnal pembalik mengurangi total sistem.
- Memperbaiki metadata worksheet Excel dan exception generator export.
- Menambahkan urutan posting wajib: jurnal asal → pembalik → transaksi pengganti.
