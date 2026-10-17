@echo off
title FraudGuard Sentinel - Server Launcher
cd /d "%~dp0"

echo ================================================================
echo                   FRAUDGUARD - LOCAL TRIAL SERVER
echo     Real-Time Digital Fraud Reporting & SOC Incident Monitoring
echo ================================================================
echo.

set PHP_EXE="..\php\php.exe"
if not exist %PHP_EXE% (
    set PHP_EXE="php"
)

echo [1/2] Checking Database and Initializing Schema...
%PHP_EXE% database/seed.php
echo.
echo [2/2] Starting PHP Web Server on http://127.0.0.1:8000 ...
echo [INFO] Press Ctrl+C in this window anytime to stop the server.
echo.
start http://127.0.0.1:8000/dashboard.php
%PHP_EXE% -S 127.0.0.1:8000
pause
