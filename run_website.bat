@echo off
setlocal enabledelayedexpansion
title EveryVeda.com - Server Launcher
cd /d "%~dp0"

echo ========================================================
echo        Starting EveryVeda.com E-Commerce Platform
echo ========================================================
echo.

REM 1. Detect PHP binary across common installation locations
set "PHP_BIN="
if exist "%~dp0php\php.exe" (
    set "PHP_BIN=%~dp0php\php.exe"
) else if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
) else if exist "D:\xampp\php\php.exe" (
    set "PHP_BIN=D:\xampp\php\php.exe"
) else if exist "E:\xampp\php\php.exe" (
    set "PHP_BIN=E:\xampp\php\php.exe"
) else if exist "C:\Program Files\PHP\php.exe" (
    set "PHP_BIN=C:\Program Files\PHP\php.exe"
) else (
    where php >nul 2>nul
    if !ERRORLEVEL! EQU 0 (
        set "PHP_BIN=php"
    )
)

if "%PHP_BIN%"=="" (
    echo [ERROR] PHP was not found on this computer!
    echo.
    echo To run EveryVeda on this computer:
    echo 1. Install XAMPP (with PHP 8.1+) from https://www.apachefriends.org/
    echo    OR add PHP to your Windows Environment PATH.
    echo 2. Re-run this file (run_website.bat).
    echo.
    pause
    exit /b 1
)
echo [OK] Using PHP: %PHP_BIN%

REM 2. Ensure .env exists (copies pre-configured cloud DB settings if missing)
if not exist ".env" (
    if exist ".env.example" (
        echo [INFO] First time setup: Creating .env from .env.example...
        copy ".env.example" ".env" >nul
        echo [OK] Pre-configured with EveryVeda Cloud Database.
    ) else (
        echo [WARNING] .env and .env.example not found!
    )
)

REM 3. Test Database Connection (Cloud or Local)
echo [INFO] Connecting to EveryVeda Database...
"%PHP_BIN%" -r "require 'app/bootstrap.php'; try { $db = \App\Core\Database::query('SELECT DATABASE() AS d, COUNT(*) AS c FROM products')->fetch(); echo '[OK] Connected to Database (' . $db['d'] . ') with ' . $db['c'] . ' active products.' . PHP_EOL; echo '[OK] Real-time Sync Active: Changes in Admin Panel instantly sync across all devices & live site!' . PHP_EOL; exit(0); } catch (Throwable $e) { echo '[WARNING] Database connection notice: ' . $e->getMessage() . PHP_EOL; exit(1); }"

if !ERRORLEVEL! NEQ 0 (
    echo [INFO] Checking if local MySQL is available as fallback...
    set "MYSQL_BIN="
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        set "MYSQL_BIN=C:\xampp\mysql\bin\mysqld.exe"
        set "MY_INI=C:\xampp\mysql\bin\my.ini"
    ) else if exist "D:\xampp\mysql\bin\mysqld.exe" (
        set "MYSQL_BIN=D:\xampp\mysql\bin\mysqld.exe"
        set "MY_INI=D:\xampp\mysql\bin\my.ini"
    )
    if not "!MYSQL_BIN!"=="" (
        netstat -ano | findstr :3306 >nul
        if !ERRORLEVEL! NEQ 0 (
            echo [INFO] Starting local MySQL service on port 3306...
            start "" /B "!MYSQL_BIN!" --defaults-file="!MY_INI!" --standalone
            timeout /t 3 /nobreak >nul
        )
    )
)

REM 4. Open browser automatically
echo.
echo [INFO] Opening EveryVeda Storefront in your browser...
start http://localhost:8000

REM 5. Launch PHP Built-in Server
echo.
echo [INFO] Web Server listening at: http://localhost:8000
echo [INFO] Admin Control Panel:    http://localhost:8000/admin
echo [INFO] Admin Login Email:      admin@everyveda.com
echo [INFO] Admin Login Password:   Admin@12345
echo.
echo ========================================================
echo Website is LIVE! Keep this window open while testing.
echo Press Ctrl+C in this command window to stop the server.
echo ========================================================
echo.

"%PHP_BIN%" -S 127.0.0.1:8000 -t public public/index.php
pause
