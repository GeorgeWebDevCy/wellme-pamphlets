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
    "composer.json",
    "composer.lock",
    "uninstall.php",
    "wellme-pamphlets.php"
)

$optionalPaths = @(
    "README.md",
    "DOCUMENTATION.md"
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

foreach ($relativePath in $optionalPaths) {
    $sourcePath = Join-Path $projectRoot $relativePath

    if (Test-Path $sourcePath) {
        Copy-Item -LiteralPath $sourcePath -Destination $packageRoot -Recurse -Force
    }
}

if (Test-Path $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$archive = [System.IO.Compression.ZipFile]::Open(
    $zipPath,
    [System.IO.Compression.ZipArchiveMode]::Create
)

try {
    Get-ChildItem -LiteralPath $packageRoot -Recurse -File |
        Sort-Object FullName |
        ForEach-Object {
            $relativePath = $_.FullName.Substring($packageRoot.Length).TrimStart('\', '/')
            $entryName = "wellme-pamphlets/" + ($relativePath -replace '\\', '/')

            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $archive,
                $_.FullName,
                $entryName,
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
        }
}
finally {
    $archive.Dispose()
}

Remove-Item -LiteralPath $stagingRoot -Recurse -Force

Write-Host "Created plugin package: $zipPath"
