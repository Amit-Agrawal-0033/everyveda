@echo off
title EveryVeda.com - Local Server
cd /d "%~dp0"

echo ========================================================
echo        Starting EveryVeda.com E-Commerce Platform
echo ========================================================
echo.

REM 1. Check if MySQL is running, if not start it from XAMPP
netstat -ano | findstr :3306 >nul
if %ERRORLEVEL% NEQ 0 (
    echo [INFO] Starting MySQL / MariaDB on port 3306...
    start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 3 /nobreak >nul
) else (
    echo [OK] MySQL is already running on port 3306.
)

REM 2. Open browser automatically
echo [INFO] Opening EveryVeda Storefront in your browser...
start http://localhost:8000

REM 3. Launch PHP Built-in Server
echo [INFO] Starting Web Server on http://localhost:8000
echo [INFO] Admin Panel: http://localhost:8000/admin (admin@everyveda.com / Admin@12345)
echo.
echo Press Ctrl+C in this window to stop the server anytime.
echo ========================================================
echo.

"C:\xampp\php\php.exe" -S localhost:8000 -t public public/index.php
pause
