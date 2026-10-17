# FraudGuard Sentinel - PowerShell Server Launcher
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptDir

$phpExe = Join-Path (Split-Path -Parent $scriptDir) "php\php.exe"
if (-not (Test-Path $phpExe)) {
    $phpExe = "php"
}

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "               FRAUDGUARD - LOCAL TRIAL SERVER" -ForegroundColor White
Write-Host "     Real-Time Digital Fraud Reporting & SOC Incident Monitoring" -ForegroundColor Yellow
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/2] Verifying Database & Schema..." -ForegroundColor Gray
& $phpExe database/seed.php

Write-Host ""
Write-Host "[2/2] Starting PHP Web Server at http://127.0.0.1:8000 ..." -ForegroundColor Green
Write-Host "Opening dashboard in your default browser..." -ForegroundColor Gray
Start-Process "http://127.0.0.1:8000/dashboard.php"
& $phpExe -S 127.0.0.1:8000
