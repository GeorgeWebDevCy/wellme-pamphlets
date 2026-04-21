param(
    [string]$OutputDir = "dist",
    [string]$ZipName = "wellme-pamphlets.zip"
)

$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$outputPath = Join-Path $projectRoot $OutputDir
$zipPath = Join-Path $outputPath $ZipName
$stagingRoot = Join-Path $projectRoot ".build"
$packageRoot = Join-Path $stagingRoot "wellme-pamphlets"

$includePaths = @(
    "admin",
    "includes",
    "public",
    "vendor",
    "index.php",
    "README.md",
    "composer.json",
    "composer.lock",
    "uninstall.php",
    "wellme-pamphlets.php"
)

if (Test-Path $stagingRoot) {
    Remove-Item -LiteralPath $stagingRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $packageRoot -Force | Out-Null
New-Item -ItemType Directory -Path $outputPath -Force | Out-Null

foreach ($relativePath in $includePaths) {
    $sourcePath = Join-Path $projectRoot $relativePath

    if (-not (Test-Path $sourcePath)) {
        throw "Missing required path: $relativePath"
    }

    Copy-Item -LiteralPath $sourcePath -Destination $packageRoot -Recurse -Force
}

if (Test-Path $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

$packageContents = Join-Path $packageRoot "*"
Compress-Archive -Path $packageContents -DestinationPath $zipPath -CompressionLevel Optimal

Remove-Item -LiteralPath $stagingRoot -Recurse -Force

Write-Host "Created plugin package: $zipPath"
