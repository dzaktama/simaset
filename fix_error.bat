@echo off
echo Mematikan proses PHP yang nyangkut...
taskkill /f /im php.exe >nul 2>&1

echo Menghapus Cache View secara Paksa...
del /q /s storage\framework\views\*
del /q /s storage\framework\cache\*
del /q /s storage\framework\sessions\*
del /q /s bootstrap\cache\*.php

echo Membersihkan Cache lewat Artisan...
php artisan optimize:clear
php artisan view:clear
php artisan config:clear

echo SELESAI! Silakan coba refresh browser.
pause