# Install Tesseract OCR for Image-Based PDFs
# Run this in PowerShell as Administrator

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Installing Tesseract OCR" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if already installed
if (Test-Path "C:\Program Files\Tesseract-OCR\tesseract.exe") {
    Write-Host "✓ Tesseract is already installed!" -ForegroundColor Green
    & "C:\Program Files\Tesseract-OCR\tesseract.exe" --version
    exit
}

Write-Host "Downloading Tesseract OCR..." -ForegroundColor Yellow

# Download Tesseract installer
$installerUrl = "https://github.com/UB-Mannheim/tesseract/releases/download/v5.3.3.20231120/tesseract-ocr-w64-setup-5.3.3.20231120.exe"
$installerPath = "$env:TEMP\tesseract-installer.exe"

try {
    Invoke-WebRequest -Uri $installerUrl -OutFile $installerPath -UseBasicParsing
    Write-Host "✓ Download complete!" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "Installing Tesseract..." -ForegroundColor Yellow
    Write-Host "Please follow the installer prompts..." -ForegroundColor Cyan
    
    # Run installer
    Start-Process -FilePath $installerPath -ArgumentList "/S" -Wait
    
    Write-Host ""
    Write-Host "✓ Tesseract installed successfully!" -ForegroundColor Green
    
    # Verify installation
    if (Test-Path "C:\Program Files\Tesseract-OCR\tesseract.exe") {
        Write-Host "✓ Tesseract verified at: C:\Program Files\Tesseract-OCR\" -ForegroundColor Green
        & "C:\Program Files\Tesseract-OCR\tesseract.exe" --version
    }
    
} catch {
    Write-Host "✗ Installation failed: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please download manually from:" -ForegroundColor Yellow
    Write-Host "https://github.com/UB-Mannheim/tesseract/releases" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
