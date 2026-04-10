# ============================================================
# Script para ejecutar LogiSystem (Emergent-HL)
# ============================================================
# Este script inicia un servidor web local para el proyecto PHP
# y abre el navegador automáticamente

param(
    [int]$Port = 8080,
    [string]$Host = "localhost",
    [switch]$NoOpen
)

$ErrorActionPreference = "Stop"

# ── Función para mostrar mensajes formateados ──
function Write-Header {
    Write-Host "`n╔════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║         LogiSystem - Emergent-HL           ║" -ForegroundColor Cyan
    Write-Host "║     Plataforma de Gestión Logística        ║" -ForegroundColor Cyan
    Write-Host "╚════════════════════════════════════════════╝`n" -ForegroundColor Cyan
}

function Write-Info {
    Write-Host "[INFO] $args" -ForegroundColor Green
}

function Write-Error {
    Write-Host "[ERROR] $args" -ForegroundColor Red
}

function Write-Success {
    Write-Host "[✓] $args" -ForegroundColor Green
}

# ── Función para verificar PHP ──
function Test-PHP {
    try {
        $phpVersion = php -v
        if ($LASTEXITCODE -eq 0) {
            Write-Success "PHP encontrado: $($phpVersion.Split([Environment]::NewLine)[0])"
            return $true
        }
    }
    catch {
        return $false
    }
    return $false
}

# ── Función para verificar puerto disponible ──
function Test-PortAvailable {
    param([int]$Port)
    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect("localhost", $Port)
        $tcpClient.Close()
        return $false  # Puerto está en uso
    }
    catch {
        return $true   # Puerto está disponible
    }
}

# ── Función para obtener puerto disponible ──
function Get-AvailablePort {
    param([int]$StartPort = 8080)
    $port = $StartPort
    while ($port -lt 9000) {
        if (Test-PortAvailable $port) {
            return $port
        }
        $port++
    }
    return $null
}

# ── Inicio del script ──
Write-Header

# Verificar PHP
Write-Info "Verificando instalación de PHP..."
if (-not (Test-PHP)) {
    Write-Error "PHP no está instalado o no está en el PATH"
    Write-Host "`nAsegúrate de tener PHP instalado. Descárgalo de: https://www.php.net/downloads" -ForegroundColor Yellow
    exit 1
}

# Cambiar a directorio del proyecto
$projectPath = Join-Path $PSScriptRoot "php_project"
if (-not (Test-Path $projectPath)) {
    Write-Error "Directorio del proyecto no encontrado: $projectPath"
    exit 1
}

Set-Location $projectPath
Write-Success "Directorio del proyecto: $projectPath"

# Verificar puerto disponible
Write-Info "Verificando disponibilidad del puerto $Port..."
if (-not (Test-PortAvailable $Port)) {
    Write-Host "Puerto $Port está en uso. Buscando puerto disponible..." -ForegroundColor Yellow
    $Port = Get-AvailablePort $Port
    if ($null -eq $Port) {
        Write-Error "No se encontró puerto disponible"
        exit 1
    }
    Write-Info "Usando puerto $Port en su lugar"
}

$Url = "http://$Host`:$Port"
Write-Success "URL del servidor: $Url"

# Ver instrucciones importantes
Write-Host "`n┌─ CONFIGURACIÓN REQUERIDA ─────────────────────────────┐" -ForegroundColor Yellow
Write-Host "│" -ForegroundColor Yellow
Write-Host "│  1. Edita config/database.php con tus credenciales" -ForegroundColor Yellow
Write-Host "│  2. Ejecuta el script de setup en tu navegador:" -ForegroundColor Yellow
Write-Host "│     $Url/setup.php?token=logistics_setup_2024" -ForegroundColor Cyan
Write-Host "│  3. Sigue las instrucciones en pantalla" -ForegroundColor Yellow
Write-Host "│" -ForegroundColor Yellow
Write-Host "└───────────────────────────────────────────────────────┘" -ForegroundColor Yellow

# Iniciar servidor
Write-Host "`nIniciando servidor PHP..." -ForegroundColor Cyan
Write-Host "Presiona CTRL+C para detener el servidor" -ForegroundColor Yellow

if (-not $NoOpen) {
    Write-Host "Abriendo navegador en 3 segundos..." -ForegroundColor Cyan
    Start-Sleep -Seconds 3
    Start-Process $Url
}

Write-Host "`n" -ForegroundColor Green
# Iniciar servidor PHP integrado
php -S "$Host`:$Port" 2>&1
