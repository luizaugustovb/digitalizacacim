param(
    [string]$InstallDirectory = "$env:LOCALAPPDATA\DigitalizacaoCACIM\ScannerAgent"
)

$ErrorActionPreference = 'Stop'
$source = Join-Path $PSScriptRoot 'publish\DigitalizacaoCacim.ScannerAgent.exe'

if (-not (Test-Path -LiteralPath $source)) {
    throw "Executável não encontrado em $source. Execute primeiro o script publish-agent.ps1."
}

$runningAgent = Get-Process -Name 'DigitalizacaoCacim.ScannerAgent' -ErrorAction SilentlyContinue
if ($runningAgent) {
    $processIds = ($runningAgent.Id -join ', ')
    throw "Feche o Agente de Digitalização CACIM e execute este instalador novamente. Processo(s) em execução: $processIds."
}

New-Item -ItemType Directory -Force -Path $InstallDirectory | Out-Null
$destination = Join-Path $InstallDirectory 'DigitalizacaoCacim.ScannerAgent.exe'
Copy-Item -LiteralPath $source -Destination $destination -Force

& $destination --install
