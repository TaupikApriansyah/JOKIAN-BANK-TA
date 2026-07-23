$ErrorActionPreference = "Stop"

Write-Host "Memperbaiki instalasi Laravel 9..." -ForegroundColor Cyan

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    throw "PHP tidak ditemukan. Aktifkan PHP Laragon lalu buka terminal baru."
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer tidak ditemukan di PATH."
}

Write-Host "PHP:" -ForegroundColor Yellow
php -v | Select-Object -First 1
Write-Host "Composer:" -ForegroundColor Yellow
composer --version

if (Test-Path vendor) {
    Remove-Item -Recurse -Force vendor
}

Get-ChildItem bootstrap/cache -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -ne '.gitignore' } |
    Remove-Item -Force

composer clear-cache
composer install --prefer-dist --no-interaction

php artisan optimize:clear
php artisan --version
php artisan package:discover --ansi

Write-Host "Backend berhasil diperbaiki." -ForegroundColor Green
Write-Host "Jalankan: php artisan serve" -ForegroundColor Green
