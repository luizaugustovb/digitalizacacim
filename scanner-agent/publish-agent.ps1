$ErrorActionPreference = 'Stop'

dotnet publish "$PSScriptRoot\DigitalizacaoCacim.ScannerAgent.csproj" `
    --configuration Release `
    --runtime win-x64 `
    --output "$PSScriptRoot\publish"
