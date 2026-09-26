$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

Write-Host "===== LIKHAE 100% AUDIT HOTFIX =====" -ForegroundColor Cyan
Write-Host "This hotfix does not run migrations and does not change the 57-table schema."

Write-Host ""
Write-Host "===== REMOVE LEGACY MODEL FILES =====" -ForegroundColor Cyan
$delete = @(
  'app\Models\Logistics\DeliveryEvent.php',
  'app\Models\Logistics\LogisticsProvider.php'
)
foreach ($file in $delete) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "REMOVED  $file"
    } else {
        Write-Host "ABSENT   $file"
    }
}

Write-Host ""
Write-Host "===== CLEAR CACHES =====" -ForegroundColor Cyan
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) { throw 'optimize:clear failed.' }

Write-Host ""
Write-Host "===== PHP SYNTAX CHECK TARGET FILES =====" -ForegroundColor Cyan
$files = @(
  'app\Models\User.php',
  'app\Http\Controllers\Seller\SellerAccountController.php',
  'app\Http\Controllers\Seller\SellerOperationsController.php',
  'routes\Seller.php'
)
foreach ($file in $files) {
    php -l $file
    if ($LASTEXITCODE -ne 0) { throw "PHP syntax failed: $file" }
}

Write-Host ""
Write-Host "===== ROUTE CHECK =====" -ForegroundColor Cyan
php artisan route:list --except-vendor
if ($LASTEXITCODE -ne 0) { throw 'route:list failed.' }

Write-Host ""
Write-Host "===== LEGACY STRING CHECK =====" -ForegroundColor Cyan
$checks = @(
  @{ Path='app\Models\User.php'; Pattern='hasRole' },
  @{ Path='resources\views\Buyer\messages.blade.php'; Pattern='seller_id' },
  @{ Path='resources\views\Logistics\dispatch.blade.php'; Pattern='tracking_code' },
  @{ Path='resources\views\Seller\account.blade.php'; Pattern='payouts' },
  @{ Path='routes\Seller.php'; Pattern='payouts' }
)
foreach ($check in $checks) {
    $hit = Select-String -Path $check.Path -Pattern $check.Pattern -SimpleMatch -Quiet
    if ($hit) { throw "Legacy string still found: $($check.Pattern) in $($check.Path)" }
    Write-Host "PASS    $($check.Path) has no '$($check.Pattern)'"
}

Write-Host ""
Write-Host "100% HOTFIX APPLIED. Re-run the audit on the updated project." -ForegroundColor Green
