# ============================================================
# Script rápido para ejecutar LogiSystem
# ============================================================
# Uso: .\run.ps1 [puerto]
# Ejemplo: .\run.ps1 8080

param([int]$Port = 8080)

# Cambiar a directorio del proyecto
$projectPath = Join-Path $PSScriptRoot "php_project"
Set-Location $projectPath

Write-Host "╔════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║         LogiSystem - Emergent-HL           ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""
Write-Host "[✓] Iniciando servidor en http://localhost:$Port" -ForegroundColor Green
Write-Host "[i] Setup: http://localhost:$Port/setup.php?token=logistics_setup_2024" -ForegroundColor Yellow
Write-Host "[i] Presiona CTRL+C para detener" -ForegroundColor Yellow
Write-Host ""

# Iniciar servidor PHP
php -S localhost:$Port
