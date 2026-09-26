$ErrorActionPreference = 'Stop'

Write-Host "===== LIKHAE PHASE 2 ROUTE HOTFIX =====" -ForegroundColor Cyan
Write-Host "Adds the missing Api/ProductApiController.php required by routes/api.php."
Write-Host "This does NOT run migrations and does NOT change the 57-table schema."

Write-Host ""
Write-Host "===== PHP SYNTAX CHECK =====" -ForegroundColor Cyan
php -l ".\app\Http\Controllers\Api\ProductApiController.php"
if ($LASTEXITCODE -ne 0) { throw 'ProductApiController syntax check failed.' }

Write-Host ""
Write-Host "===== CLEAR LARAVEL CACHES =====" -ForegroundColor Cyan
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) { throw 'optimize:clear failed.' }

Write-Host ""
Write-Host "===== ROUTE CHECK =====" -ForegroundColor Cyan
php artisan route:list --except-vendor
if ($LASTEXITCODE -ne 0) { throw 'Laravel route check failed.' }

Write-Host ""
Write-Host "===== MIGRATION STATUS =====" -ForegroundColor Cyan
php artisan migrate:status
if ($LASTEXITCODE -ne 0) { throw 'migrate:status failed.' }

Write-Host ""
Write-Host "PHASE 2 ROUTE HOTFIX APPLIED." -ForegroundColor Green
