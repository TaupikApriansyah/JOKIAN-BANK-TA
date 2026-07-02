@echo off
setlocal
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan route:list
pause
