# Resume Analyzer - Setup and Run Script
# Run this in PowerShell

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Resume Analyzer - Setup & Run" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check XAMPP
Write-Host "[1/4] Checking XAMPP..." -ForegroundColor Yellow
if (Test-Path "C:\xampp\htdocs") {
    Write-Host "✓ XAMPP found" -ForegroundColor Green
} else {
    Write-Host "✗ XAMPP not found!" -ForegroundColor Red
    exit
}

# Step 2: Deploy files
Write-Host ""
Write-Host "[2/4] Deploying to XAMPP..." -ForegroundColor Yellow
Copy-Item -Path "d:\resume-analyzer\frontend" -Destination "C:\xampp\htdocs\resume-analyzer\frontend" -Recurse -Force
Copy-Item -Path "d:\resume-analyzer\backend" -Destination "C:\xampp\htdocs\resume-analyzer\backend" -Recurse -Force
Write-Host "✓ Files deployed" -ForegroundColor Green

# Step 3: Open phpMyAdmin
Write-Host ""
Write-Host "[3/4] Opening phpMyAdmin..." -ForegroundColor Yellow
Start-Process "http://localhost/phpmyadmin"
Write-Host "→ Import database/schema.sql if not done" -ForegroundColor Cyan

# Step 4: Open app
Write-Host ""
Write-Host "[4/4] Opening Resume Analyzer..." -ForegroundColor Yellow
Start-Sleep -Seconds 2
Start-Process "http://localhost/resume-analyzer/frontend/upload.html"

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Ready!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "URLs:" -ForegroundColor Cyan
Write-Host "  • Upload: http://localhost/resume-analyzer/frontend/upload.html" -ForegroundColor White
Write-Host "  • Login: http://localhost/resume-analyzer/frontend/login.html" -ForegroundColor White
Write-Host "  • Backend: http://localhost/resume-analyzer/backend/login.php" -ForegroundColor White
Write-Host ""
Write-Host "Test Login: username='testuser' password='test123'" -ForegroundColor Yellow
Write-Host ""
