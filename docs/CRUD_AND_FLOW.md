# CRUD & Flow Operasional — Bank X

## 1. Data Nasabah
- **Create:** CS menambahkan nasabah. Sistem memberi nomor nasabah otomatis dan mengenkripsi NIK/nomor rekening.
- **Read:** CS hanya membaca nasabah yang ditugaskan; Admin dapat monitoring seluruh data.
- **Update:** CS pemilik data dapat mengubah data nasabah. Nilai sensitif lama tidak dipaparkan kembali pada form.
- **Delete aman:** Admin melakukan nonaktifkan, bukan hapus fisik. Data serta audit trail tetap ada.

## 2. Berkas Layanan
- **Create:** CS memilih nasabah dan jenis layanan; nomor berkas serta due date SLA dibuat otomatis.
- **Read:** Detail berkas menyatukan dokumen, status SLA, transaksi, dan riwayat.
- **Update:** Berkas hanya dapat diubah ketika status `Baru` atau `Menunggu Dokumen`.
- **Delete aman:** Hanya berkas tanpa dokumen dan transaksi yang dapat dihapus. Bila proses sudah berjalan, gunakan `Tolak Berkas` agar histori tidak hilang.

## 3. Arsip Digital
- **Create:** CS mengunggah dokumen ke storage private.
- **Read:** Download memakai route berotorisasi sesuai role/penugasan.
- **Update:** Jenis dokumen atau file dapat diganti selama berkas belum selesai.
- **Delete:** Dokumen dapat dihapus sebelum berkas selesai; aksinya masuk audit trail.

## 4. Transaksi Administrasi (Maker–Checker)
- **Create:** CS membuat draft atau langsung submit transaksi dari berkas.
- **Read:** CS membaca transaksi sendiri; Admin membaca seluruh transaksi.
- **Update:** Hanya `Draft` atau `Dikembalikan` yang dapat diubah oleh pembuatnya.
- **Delete aman:** Draft dibatalkan (`Dibatalkan`), bukan dihapus fisik.
- **Approval:** Admin menyetujui atau mengembalikan; approval membuat jurnal otomatis dalam DB transaction.
- **Koreksi:** Transaksi disetujui dikoreksi melalui request; Admin menyetujui/menolak. Jika disetujui, jurnal pembalik dibuat dan draft pengganti dihasilkan.

## 5. Master Layanan & User
- Master layanan: create/read/update; delete adalah `nonaktifkan` agar berkas historis tetap valid.
- User: create/read/update; delete adalah `nonaktifkan` karena akun serta audit log tidak boleh hilang.

## Urutan flow
```text
Nasabah → Berkas → Dokumen wajib → Proses layanan
                           │
                           └─ SLA dipantau paralel

Jika biaya ada: CS draft/submit transaksi → Admin checker
                ├─ dikembalikan → CS perbaiki & submit ulang
                └─ disetujui → jurnal otomatis → arsip lengkap → berkas selesai
```
