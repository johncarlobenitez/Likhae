$ErrorActionPreference = 'Continue'

$projectDir = Split-Path -Parent $PSScriptRoot
$logDir = Join-Path $projectDir 'storage\logs\dev'
New-Item -ItemType Directory -Force -Path $logDir | Out-Null

Set-Location $projectDir

while ($true) {
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $logPath = Join-Path $logDir "artisan-dev-$stamp.log"

    "[$(Get-Date -Format s)] Starting Laravel development services (server, queue, Reverb, and Vite)." | Add-Content $logPath
    & php artisan dev --stream --timestamps *>> $logPath
    $exitCode = $LASTEXITCODE
    "[$(Get-Date -Format s)] Laravel development services exited with code $exitCode. Restarting in 5 seconds." | Add-Content $logPath
    Start-Sleep -Seconds 5
}
