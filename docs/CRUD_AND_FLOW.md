# CRUD dan Flow Operasional — Bank X

## 1. Nasabah (CS / Maker)

| Aksi | Hasil |
|---|---|
| Tambah | CS membuat data nasabah. Nomor nasabah dibuat otomatis dan data sensitif dienkripsi. |
| Lihat | CS hanya melihat nasabah yang ditugaskan kepadanya; Admin dapat memonitor semua nasabah. |
| Ubah | Hanya CS pemilik data yang dapat mengubah nasabah aktif. |
| Hapus aman | Admin melakukan nonaktifkan, bukan hapus fisik, untuk menjaga histori berkas dan audit. |

## 2. Berkas Layanan (CS / Maker)

1. CS memilih **nasabah aktif** dan **jenis layanan**.
2. Sistem membuat nomor berkas dan batas SLA berdasarkan master layanan.
3. CS unggah dokumen wajib; status dapat berubah dari `Menunggu Dokumen` ke `Baru` setelah lengkap.
4. CS memulai proses; status berubah menjadi `Diproses`.
5. Bila tidak dapat dilanjutkan, CS menolak berkas dengan alasan. Bila selesai, sistem mengunci SLA sebagai `Selesai`.
6. Draft kosong hanya boleh dihapus sebelum memiliki dokumen atau transaksi.

## 3. Dokumen / Arsip (CS / Maker)

- Create: unggah dokumen dari workspace berkas.
- Read: akses mengikuti penugasan berkas.
- Update: ubah jenis dokumen atau file sebelum berkas selesai.
- Delete: hanya sebelum berkas selesai; aksi dicatat dalam audit trail.
- Download: selalu dicatat ke audit trail.

## 4. Transaksi Administrasi (Maker–Checker)

1. CS membuat draft transaksi dari **workspace berkas**, sehingga ID berkas dan nasabah diisi otomatis oleh backend.
2. CS menyimpan draft atau mengajukan verifikasi.
3. Admin menyetujui / mengembalikan / menolak.
4. Saat disetujui, jurnal debit–kredit dibuat otomatis dan transaksi tidak lagi dapat diedit atau dihapus langsung.
5. Kesalahan pada transaksi approved masuk ke alur koreksi dan jurnal pembalik.
6. Aksi delete untuk transaksi adalah **batal status**, bukan menghapus record.

## 5. Keterhubungan Modul

```text
Nasabah
  └─ Berkas Layanan
      ├─ Dokumen Arsip
      ├─ Monitoring SLA
      └─ Transaksi Administrasi
          └─ Persetujuan Admin → Jurnal → Rekonsiliasi → PDF/Excel
```

## 6. Role

- **CS / Maker:** Nasabah, Berkas, Dokumen, Draft Transaksi, Pengajuan Koreksi.
- **Admin / Checker:** Verifikasi, Master Layanan, Rekonsiliasi, Audit, User, Laporan dan Export.
