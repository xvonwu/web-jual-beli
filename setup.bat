@echo off
REM =====================================================================
REM  Setup otomatis web PakHar Game (Laravel + MySQL XAMPP)
REM  Jalankan: klik dua kali setup.bat, atau ketik "setup" di CMD folder ini
REM  Syarat: XAMPP (Apache + MySQL sudah START) dan Composer terpasang
REM =====================================================================
cd /d "%~dp0"
setlocal

set "PHP=php"
where php >nul 2>nul || set "PHP=C:\xampp\php\php.exe"
set "MYSQL=C:\xampp\mysql\bin\mysql.exe"

echo.
echo [1/5] Cek PHP...
"%PHP%" -v || (echo PHP tidak ditemukan. Pastikan XAMPP terpasang di C:\xampp & pause & exit /b 1)
"%PHP%" -m | findstr /i "zip" >nul || echo   ! Ekstensi zip belum aktif. Buka C:\xampp\php\php.ini lalu hapus ; di depan "extension=zip"
"%PHP%" -m | findstr /i "fileinfo" >nul || echo   ! Ekstensi fileinfo belum aktif. Buka C:\xampp\php\php.ini lalu hapus ; di depan "extension=fileinfo"

echo.
echo [2/5] Install library Laravel (composer install)...
where composer >nul 2>nul || (echo Composer belum terpasang. Download dari https://getcomposer.org/Composer-Setup.exe & pause & exit /b 1)
call composer install --no-interaction || (echo Composer gagal. Lihat pesan error di atas. & pause & exit /b 1)

echo.
echo [3/5] Siapkan file .env...
if not exist .env copy .env.example .env >nul
findstr /r /c:"^APP_KEY=base64" .env >nul || "%PHP%" artisan key:generate

echo.
echo [4/5] Buat database "web_ps" di MySQL XAMPP...
if exist "%MYSQL%" (
    "%MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS web_ps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || echo   ! Gagal. Pastikan MySQL di XAMPP Control Panel sudah START, atau buat database web_ps lewat phpMyAdmin.
) else (
    echo   ! mysql.exe tidak ditemukan. Buat database "web_ps" manual di http://localhost/phpmyadmin
)

echo.
echo [5/5] Buat tabel + isi data contoh (migrate --seed)...
"%PHP%" artisan migrate:fresh --seed --force || (echo Migrasi gagal. Cek MySQL XAMPP sudah START dan database web_ps ada. & pause & exit /b 1)

echo.
echo =====================================================================
echo  SELESAI!  Menjalankan server di http://127.0.0.1:8000
echo  Admin : admin@pakhar.test / password
echo  User  : user@pakhar.test  / password
echo  Database bisa dilihat di http://localhost/phpmyadmin  (database: web_ps)
echo  Tekan Ctrl+C untuk menghentikan server.
echo =====================================================================
"%PHP%" artisan serve
