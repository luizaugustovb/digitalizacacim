param(
    [string]$InstallDirectory = "$env:LOCALAPPDATA\DigitalizacaoCACIM\ScannerAgent"
)

$ErrorActionPreference = 'Stop'
$source = Join-Path $PSScriptRoot 'publish\DigitalizacaoCacim.ScannerAgent.exe'

if (-not (Test-Path -LiteralPath $source)) {
    throw "Executável não encontrado em $source. Execute primeiro o script publish-agent.ps1."
}

New-Item -ItemType Directory -Force -Path $InstallDirectory | Out-Null
$destination = Join-Path $InstallDirectory 'DigitalizacaoCacim.ScannerAgent.exe'
Copy-Item -LiteralPath $source -Destination $destination -Force

& $destination --install
