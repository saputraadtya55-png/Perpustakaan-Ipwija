
@echo off
setlocal enabledelayedexpansion
title Installer - Sistem Manajemen Buku Perpustakaan IPWIJA

echo ===================================================
echo   INSTALLER - Sistem Manajemen Buku Perpustakaan IPWIJA
echo ===================================================
echo.

REM --- Baca NIM dari file .env agar nama container konsisten ---
for /f "tokens=2 delims==" %%a in ('findstr /b "NIM=" .env') do set NIM=%%a
echo NIM terdeteksi: %NIM%
echo.

REM --- 1. Pastikan file .env ada ---
echo [1/7] Memeriksa file .env...
if not exist .env (
    echo File .env tidak ditemukan. Membatalkan proses.
    pause
    exit /b 1
)

REM --- 2. Build & jalankan seluruh container ---
echo [2/7] Menjalankan Docker Compose (build + up)...
docker compose up -d --build
if errorlevel 1 (
    echo Gagal menjalankan docker compose. Periksa Docker Desktop Anda.
    pause
    exit /b 1
)

REM --- 3. Tunggu container siap ---
echo [3/7] Menunggu container siap...
timeout /t 15 /nobreak

REM --- 4. Install dependency Laravel via Composer ---
echo [4/7] Menginstall dependency Laravel (composer install)...
docker exec %NIM%_app composer install --no-interaction

REM --- 5. Generate application key ---
echo [5/7] Generate APP_KEY...
docker exec %NIM%_app php artisan key:generate

REM --- 6. Migrasi & seeding database ---
echo [6/7] Menjalankan migrasi database...
docker exec %NIM%_app php artisan migrate --seed --force

REM --- 7. Selesai ---
echo [7/7] Instalasi selesai!
echo.
echo ===================================================
echo   Aplikasi bisa diakses di   : http://localhost:4080
echo   Database (eksternal)       : 127.0.0.1:33061
echo   N8N Workflow                : http://localhost:5678
echo   Ngrok Web Interface          : http://localhost:4040
echo ===================================================
echo.
pause