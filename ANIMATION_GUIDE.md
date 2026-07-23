# Bank X — Animation Guide

Animasi dibuat untuk membantu pemahaman proses kerja, bukan sekadar dekorasi.

## Yang Ditambahkan
- **Page reveal:** kartu, tabel, dan blok konten tampil bertahap ketika halaman dimuat atau discroll.
- **Dashboard counters:** angka metrik utama memiliki efek count-up ringan.
- **Chart reveal:** grafik muncul dengan transisi halus dari bawah.
- **Sidebar feedback:** menu bergeser sedikit dan icon bergerak lembut saat hover; menu aktif memiliki masuk yang halus.
- **Buttons:** tombol utama memiliki hover lift, press feedback, dan shimmer ringan.
- **Alert:** notifikasi operasional penting memakai glow sangat lembut agar lebih mudah terlihat.
- **Forms:** saat data dikirim muncul loading overlay agar pengguna tidak klik berulang kali.
- **Flash messages:** pesan sukses/error masuk dan keluar dengan transisi halus.
- **Login:** background orb bergerak pelan, feature cards muncul bertahap, dan panel login masuk dengan transisi premium.

## Accessibility
Semua animasi menghormati browser setting `prefers-reduced-motion`. Bila perangkat/user mengurangi motion, animasi dipersingkat/dinonaktifkan otomatis.

## File Utama
- `public/css/motion.css`
- `public/js/motion.js`
