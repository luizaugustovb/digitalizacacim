param(
    [ValidateSet('win-x64', 'win-x86')]
    [string]$Runtime = 'win-x64'
)

$ErrorActionPreference = 'Stop'

dotnet publish "$PSScriptRoot\DigitalizacaoCacim.ScannerAgent.csproj" `
    --configuration Release `
    --runtime $Runtime `
    --output "$PSScriptRoot\publish\$Runtime"
