@echo off
cd /d "%~dp0"

echo Menghubungkan port HP ke Laptop via USB...
call "%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe" reverse tcp:8000 tcp:8000

echo Menjalankan aplikasi...
call flutter run --dart-define=API_BASE_URL=http://localhost:8000
pause
