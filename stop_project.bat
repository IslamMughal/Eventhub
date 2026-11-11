@echo off
setlocal
cd /d "%~dp0"
title Stop Event Hub

echo ========================================================
echo               Stopping Event Hub Servers
echo ========================================================

REM Stop PHP server
taskkill /f /im php.exe >nul 2>&1
echo [+] Stopped PHP web server.

REM Stop MariaDB server
taskkill /f /im mysqld.exe >nul 2>&1
echo [+] Stopped MariaDB database server.

echo.
echo All services stopped.
echo ========================================================
timeout /t 2 >nul

