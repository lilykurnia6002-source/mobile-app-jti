@echo off
cd /d "%~dp0"

echo =====================================================
echo    PMM AutoCare - Menjalankan Aplikasi di HP Fisik
echo =====================================================
echo.

set "ADB_CMD="
where adb >nul 2>nul
if %errorlevel% equ 0 (
    set "ADB_CMD=adb"
) else if exist "%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe" (
    set "ADB_CMD=%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe"
)

if "%ADB_CMD%"=="" (
    echo [PERINGATAN] adb.exe tidak ditemukan di PATH maupun Android SDK.
    echo Pastikan Android SDK platform-tools sudah terpasang di laptop.
    echo.
    goto run_flutter
)

echo [1/2] Menghubungkan port HP ke Laptop via USB [adb reverse]...
"%ADB_CMD%" reverse tcp:8000 tcp:8000
if %errorlevel% equ 0 goto adb_ok

echo.
echo [PERHATIAN] Gagal menjalankan adb reverse!
echo Pastikan:
echo   1. HP sudah terhubung ke laptop via kabel USB data.
echo   2. Opsi Pengembang dan USB Debugging sudah AKTIF di HP.
echo   3. Layar HP menyala dan popup izin USB debugging sudah disetujui.
echo.
goto run_flutter

:adb_ok
echo Port 8000 berhasil di-forward ke HP.
echo.

:run_flutter
echo [2/2] Menjalankan aplikasi Flutter...
echo Catatan: Pastikan backend Laravel [php artisan serve] sudah jalan di port 8000.
echo.
call flutter run --dart-define=API_BASE_URL=http://localhost:8000
pause


