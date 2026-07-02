# Import langsung via phpMyAdmin

1. Aktifkan MySQL pada XAMPP.
2. Buka `http://localhost/phpmyadmin`.
3. Klik menu **Import** lalu pilih file `bank_ta_full.sql`.
4. Klik **Import / Go** sampai muncul pesan sukses.
5. Salin `.env.example` menjadi `.env`, lalu jalankan `php artisan key:generate`.
6. **Jangan** jalankan `php artisan migrate` atau `php artisan migrate:fresh --seed` setelah import SQL, karena skema dan data demo sudah tersedia.
7. Jalankan `php artisan serve`.

Akun demo:
- CS: `CS-001` / `password123`
- Admin: `ADM-001` / `password123`

Catatan: SQL demo sengaja tidak menyimpan NIK dan nomor rekening. Nilai sensitif sebaiknya dimasukkan melalui aplikasi setelah `APP_KEY` dibuat agar Laravel dapat mengenkripsinya.
