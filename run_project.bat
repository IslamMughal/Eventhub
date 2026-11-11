@echo off
setlocal
cd /d "%~dp0"
title Event Hub Launcher

set "MARIADB_BIN=C:\Users\islam\mariadb\bin"
set "PHP_BIN=C:\Users\islam\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

echo ========================================================
echo               Starting Event Hub Project
echo ========================================================

REM 1. Check if MariaDB / MySQL is running on port 3306
netstat -ano | findstr :3306 >nul
if %errorlevel% neq 0 (
    echo [1/3] Starting MariaDB database server...
    start "MariaDB Server" /b "%MARIADB_BIN%\mysqld.exe" --console
    timeout /t 2 /nobreak >nul
) else (
    echo [1/3] Database server is already running on port 3306.
)

REM 2. Check if PHP server is running on port 8000
netstat -ano | findstr :8000 >nul
if %errorlevel% neq 0 (
    echo [2/3] Starting PHP web server at http://localhost:8000/ ...
    start "PHP Web Server" /b "%PHP_BIN%" -S localhost:8000
    timeout /t 1 /nobreak >nul
) else (
    echo [2/3] PHP web server is already running on port 8000.
)

REM 3. Open browser
echo [3/3] Opening browser at http://localhost:8000/ ...
start http://localhost:8000/

echo.
echo ========================================================
echo  Event Hub is running!
echo  URL: http://localhost:8000/
echo.
echo  Demo Credentials:
echo   - Admin:      admin@citypulse.pk / admin123
echo   - Moderator:  mod@citypulse.pk   / mod123
echo   - User:       sara@citypulse.pk  / user123
echo.
echo  To stop the servers, run stop_project.bat
echo ========================================================
echo.
pause

