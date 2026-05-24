$ErrorActionPreference = "Stop"

$source = "C:\laragon\www\shopla"
$destination = "\\192.168.0.11\sites\shopla"

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupDir = Join-Path $destination "_deploy-backups\$timestamp"

Write-Host ""
Write-Host "Shopla deploy local" -ForegroundColor Magenta
Write-Host "Origem:  $source"
Write-Host "Destino: $destination"
Write-Host ""

if (-not (Test-Path $source)) {
    throw "Pasta de origem nao encontrada: $source"
}

if (-not (Test-Path $destination)) {
    throw "Pasta de destino nao encontrada: $destination"
}

New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$protectedFiles = @(
    ".env",
    ".env.production"
)

foreach ($file in $protectedFiles) {
    $path = Join-Path $destination $file

    if (Test-Path $path) {
        Copy-Item -LiteralPath $path -Destination (Join-Path $backupDir $file) -Force
        Write-Host "Backup criado: $file" -ForegroundColor Yellow
    }
}

$excludeDirs = @(
    ".git",
    ".idea",
    ".vscode",
    "node_modules",
    "vendor",
    "storage\logs",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\views",
    "bootstrap\cache"
)

$excludeFiles = @(
    ".env",
    ".env.*",
    "*.log",
    "shopla-update.tar.gz"
)

$robocopyArgs = @(
    $source,
    $destination,
    "/E",
    "/R:2",
    "/W:2",
    "/FFT",
    "/NP",
    "/XD"
) + $excludeDirs + @(
    "/XF"
) + $excludeFiles

Write-Host ""
Write-Host "Copiando arquivos..." -ForegroundColor Cyan
& robocopy @robocopyArgs
$code = $LASTEXITCODE

if ($code -ge 8) {
    throw "Robocopy falhou com codigo $code"
}

foreach ($file in $protectedFiles) {
    $backupPath = Join-Path $backupDir $file
    $targetPath = Join-Path $destination $file

    if (Test-Path $backupPath) {
        Copy-Item -LiteralPath $backupPath -Destination $targetPath -Force
        Write-Host "Arquivo protegido restaurado: $file" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Arquivos copiados com seguranca." -ForegroundColor Green
Write-Host "Agora entre no servidor e rode:"
Write-Host ""
Write-Host "cd /opt/sites/shopla" -ForegroundColor White
Write-Host "docker compose --env-file .env.production -f docker-compose.prod.yml build app nginx" -ForegroundColor White
Write-Host "docker compose --env-file .env.production -f docker-compose.prod.yml up -d --force-recreate app nginx worker scheduler" -ForegroundColor White
Write-Host "docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan migrate --force" -ForegroundColor White
Write-Host "docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan optimize:clear" -ForegroundColor White
Write-Host "docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan config:cache" -ForegroundColor White
Write-Host ""
