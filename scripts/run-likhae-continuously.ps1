$ErrorActionPreference = 'Continue'

$projectDir = Split-Path -Parent $PSScriptRoot
$logDir = Join-Path $projectDir 'storage\logs\dev'
New-Item -ItemType Directory -Force -Path $logDir | Out-Null

Set-Location $projectDir

while ($true) {
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $logPath = Join-Path $logDir "artisan-dev-$stamp.log"

    "[$(Get-Date -Format s)] Starting Laravel production-style server with built assets." | Add-Content $logPath
    & php artisan serve --host=0.0.0.0 --port=8000 *>> $logPath
    $exitCode = $LASTEXITCODE
    "[$(Get-Date -Format s)] Laravel dev services exited with code $exitCode. Restarting in 5 seconds." | Add-Content $logPath
    Start-Sleep -Seconds 5
}
