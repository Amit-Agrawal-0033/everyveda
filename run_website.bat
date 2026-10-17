@echo off
setlocal enabledelayedexpansion
title EveryVeda.com - Local Server
cd /d "%~dp0"

echo ========================================================
echo        Starting EveryVeda.com E-Commerce Platform
echo ========================================================
echo.

REM 1. Detect PHP binary
set "PHP_BIN="
if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
) else if exist "D:\xampp\php\php.exe" (
    set "PHP_BIN=D:\xampp\php\php.exe"
) else (
    where php >nul 2>nul
    if !ERRORLEVEL! EQU 0 (
        set "PHP_BIN=php"
    )
)

if "%PHP_BIN%"=="" (
    echo [ERROR] PHP was not found on this computer!
    echo Please install XAMPP from https://www.apachefriends.org/ or add PHP to your Windows PATH.
    echo.
    pause
    exit /b 1
)
echo [OK] Using PHP: %PHP_BIN%

REM 2. Detect MySQL binary
set "MYSQL_BIN="
set "MY_INI="
if exist "C:\xampp\mysql\bin\mysqld.exe" (
    set "MYSQL_BIN=C:\xampp\mysql\bin\mysqld.exe"
    set "MY_INI=C:\xampp\mysql\bin\my.ini"
) else if exist "D:\xampp\mysql\bin\mysqld.exe" (
    set "MYSQL_BIN=D:\xampp\mysql\bin\mysqld.exe"
    set "MY_INI=D:\xampp\mysql\bin\my.ini"
)

REM 3. Check if MySQL is running on port 3306
netstat -ano | findstr :3306 >nul
if %ERRORLEVEL% NEQ 0 (
    if not "%MYSQL_BIN%"=="" (
        echo [INFO] Starting MySQL / MariaDB on port 3306...
        start "" /B "%MYSQL_BIN%" --defaults-file="%MY_INI%" --standalone
        timeout /t 4 /nobreak >nul
    ) else (
        echo [WARNING] MySQL service was not found in standard paths.
        echo Please ensure MySQL is running on port 3306 via XAMPP Control Panel.
        echo.
    )
) else (
    echo [OK] MySQL is running on port 3306.
)

REM 4. Ensure .env exists
if not exist ".env" (
    if exist ".env.example" (
        echo [INFO] Creating .env from .env.example...
        copy ".env.example" ".env" >nul
    )
)

REM 5. Check if database everyveda_db exists and has tables
echo [INFO] Checking EveryVeda database status...
"%PHP_BIN%" -r "$h=getenv('DB_HOST')?:'127.0.0.1'; $p=(int)(getenv('DB_PORT')?:3306); $u=getenv('DB_USER')?:'root'; $pw=getenv('DB_PASS')?:''; try { $pdo = new PDO(\"mysql:host={$h};port={$p};dbname=everyveda_db\", $u, $pw); $c = (int)$pdo->query(\"SHOW TABLES\")->rowCount(); exit($c >= 10 ? 0 : 1); } catch (Exception $e) { exit(1); }" >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [SETUP] Initializing database and authentic Ayurvedic seed data...
    "%PHP_BIN%" database\migrate.php
    echo.
) else (
    echo [OK] Database everyveda_db is ready.
)

REM 6. Open browser automatically
echo.
echo [INFO] Opening EveryVeda Storefront in your browser...
start http://localhost:8000

REM 7. Launch PHP Built-in Server
echo [INFO] Web Server listening at: http://localhost:8000
echo [INFO] Admin Control Panel:    http://localhost:8000/admin
echo [INFO] Default Admin Login:    admin@everyveda.com / Admin@12345
echo.
echo ========================================================
echo Website is LIVE! Keep this window open while testing.
echo Press Ctrl+C in this window to stop the server anytime.
echo ========================================================
echo.

"%PHP_BIN%" -S localhost:8000 -t public public/index.php
pause
