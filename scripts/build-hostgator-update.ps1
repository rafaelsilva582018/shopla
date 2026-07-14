[CmdletBinding()]
param(
    [string]$OutputDirectory
)

$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path

if ([string]::IsNullOrWhiteSpace($OutputDirectory)) {
    $OutputDirectory = Join-Path $projectRoot 'dist'
} elseif (-not [System.IO.Path]::IsPathRooted($OutputDirectory)) {
    $OutputDirectory = Join-Path $projectRoot $OutputDirectory
}

$OutputDirectory = [System.IO.Path]::GetFullPath($OutputDirectory)
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$releaseDirectory = Join-Path $OutputDirectory "hostgator-update-$stamp"
$stagingDirectory = Join-Path $releaseDirectory '_staging'
$appStage = Join-Path $stagingDirectory 'app'
$publicStage = Join-Path $stagingDirectory 'public_html'

function Invoke-CheckedCommand {
    param(
        [Parameter(Mandatory)]
        [string]$Command,

        [Parameter(ValueFromRemainingArguments)]
        [string[]]$Arguments
    )

    & $Command @Arguments

    if ($LASTEXITCODE -ne 0) {
        throw "O comando '$Command $($Arguments -join ' ')' terminou com o codigo $LASTEXITCODE."
    }
}

function Assert-SafeStagingPath {
    param([Parameter(Mandatory)][string]$Path)

    $resolvedOutput = [System.IO.Path]::GetFullPath($OutputDirectory).TrimEnd('\') + '\'
    $resolvedPath = [System.IO.Path]::GetFullPath($Path)

    if (-not $resolvedPath.StartsWith($resolvedOutput, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Caminho de staging fora do diretorio de saida: $resolvedPath"
    }
}

function New-NormalizedZip {
    param(
        [Parameter(Mandatory)][string]$SourceDirectory,
        [Parameter(Mandatory)][string]$DestinationPath
    )

    $sourceRoot = [System.IO.Path]::GetFullPath($SourceDirectory).TrimEnd('\')
    $archive = [System.IO.Compression.ZipFile]::Open(
        $DestinationPath,
        [System.IO.Compression.ZipArchiveMode]::Create
    )

    try {
        Get-ChildItem -LiteralPath $sourceRoot -File -Recurse | ForEach-Object {
            # Windows PowerShell 5.1 uses .NET Framework, which does not expose
            # System.IO.Path.GetRelativePath. Every item is a descendant of
            # $sourceRoot, so remove the root plus its directory separator.
            $entryName = $_.FullName.Substring($sourceRoot.Length + 1).Replace('\', '/')
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $archive,
                $_.FullName,
                $entryName,
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
        }
    } finally {
        $archive.Dispose()
    }
}

New-Item -ItemType Directory -Force -Path $appStage, $publicStage | Out-Null

Push-Location $projectRoot

$primaryError = $null

try {
    Invoke-CheckedCommand -Command 'npm.cmd' -Arguments @('ci', '--no-fund')
    Invoke-CheckedCommand -Command 'npm.cmd' -Arguments @('run', 'build')
    Invoke-CheckedCommand -Command 'composer' -Arguments @('install', '--no-dev', '--prefer-dist', '--optimize-autoloader', '--no-interaction')

    New-Item -ItemType Directory -Force -Path (Join-Path $appStage 'config') | Out-Null
    New-Item -ItemType Directory -Force -Path (Join-Path $appStage 'bootstrap\cache') | Out-Null

    Copy-Item -LiteralPath (Join-Path $projectRoot 'vendor') -Destination (Join-Path $appStage 'vendor') -Recurse
    Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.json') -Destination $appStage
    Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.lock') -Destination $appStage
    Copy-Item -LiteralPath (Join-Path $projectRoot 'config\filesystems.php') -Destination (Join-Path $appStage 'config\filesystems.php')
    Copy-Item -LiteralPath (Join-Path $projectRoot 'bootstrap\cache\packages.php') -Destination (Join-Path $appStage 'bootstrap\cache\packages.php')
    Copy-Item -LiteralPath (Join-Path $projectRoot 'bootstrap\cache\services.php') -Destination (Join-Path $appStage 'bootstrap\cache\services.php')
    Copy-Item -LiteralPath (Join-Path $projectRoot 'public\build') -Destination (Join-Path $publicStage 'build') -Recurse

    $appZip = Join-Path $releaseDirectory "shopla-app-update-$stamp.zip"
    $publicZip = Join-Path $releaseDirectory "shopla-public-build-$stamp.zip"

    New-NormalizedZip -SourceDirectory $appStage -DestinationPath $appZip
    New-NormalizedZip -SourceDirectory $publicStage -DestinationPath $publicZip

    $hashLines = Get-FileHash -Algorithm SHA256 -LiteralPath $appZip, $publicZip |
        ForEach-Object { "$($_.Hash.ToLowerInvariant())  $([System.IO.Path]::GetFileName($_.Path))" }
    Set-Content -LiteralPath (Join-Path $releaseDirectory 'SHA256SUMS.txt') -Value $hashLines -Encoding utf8

    $instructions = @"
1. Faca backup do MySQL, de /home4/USUARIO/shopla e de public_html/storage.
2. Envie shopla-app-update-$stamp.zip para /home4/USUARIO/shopla.
3. Renomeie vendor para vendor-before-update e extraia o ZIP da aplicacao.
4. Envie shopla-public-build-$stamp.zip para /home4/USUARIO/public_html.
5. Renomeie build para build-before-update e extraia o ZIP publico.
6. Nao substitua .env, public_html/index.php, .htaccess, .well-known ou public_html/storage.
7. Confira permissoes: pastas 0755, arquivos 0644 e .env 0600.
8. Teste inicio, login, Google, imagens, e-mail e Asaas antes de apagar os backups temporarios.
"@
    Set-Content -LiteralPath (Join-Path $releaseDirectory 'INSTRUCOES.txt') -Value $instructions -Encoding utf8
} catch {
    $primaryError = $_
} finally {
    try {
        Invoke-CheckedCommand -Command 'composer' -Arguments @('install', '--prefer-dist', '--no-interaction')
    } catch {
        if ($null -eq $primaryError) {
            $primaryError = $_
        } else {
            Write-Warning "Falha ao restaurar dependencias de desenvolvimento: $($_.Exception.Message)"
        }
    }

    Pop-Location

    if (Test-Path -LiteralPath $stagingDirectory) {
        Assert-SafeStagingPath $stagingDirectory
        Remove-Item -LiteralPath $stagingDirectory -Recurse -Force
    }
}

if ($null -ne $primaryError) {
    throw $primaryError
}

Write-Host "Pacotes criados em: $releaseDirectory"
Get-ChildItem -LiteralPath $releaseDirectory | Select-Object Name, Length, LastWriteTime
