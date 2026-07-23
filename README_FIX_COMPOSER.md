# Perbaikan Composer dan Artisan

## Masalah yang diperbaiki

Proyek ini memakai Laravel 9.52.21, tetapi file `artisan` sebelumnya memakai bootstrap CLI Laravel versi baru dan memanggil `Application::handleCommand()`. Method tersebut tidak tersedia pada Laravel 9.

Folder `vendor` juga sempat terbentuk tidak lengkap karena proses `composer install` berhenti saat menjalankan `artisan package:discover`. Akibatnya, VS Code dapat menampilkan error Sanctum, polyfill PHP, atau file vendor yang hilang.

## Cara menjalankan di Windows

1. Tutup VS Code terlebih dahulu agar ekstensi Laravel Extra Intellisense tidak membaca folder `vendor` saat Composer sedang menulis file.
2. Buka PowerShell pada folder proyek.
3. Jalankan:

```powershell
Set-ExecutionPolicy -Scope Process Bypass
.\FIX_INSTALL_WINDOWS.ps1
```

4. Setelah berhasil, buka kembali VS Code.
5. Jalankan aplikasi:

```powershell
php artisan serve
```

Untuk frontend, buka terminal kedua lalu jalankan:

```powershell
npm install
npm run dev
```

## Pemeriksaan manual

Perintah berikut harus menampilkan versi Laravel tanpa fatal error:

```powershell
php artisan --version
```

Versi yang diharapkan dari `composer.lock` adalah Laravel Framework 9.52.21.
