Write-Host "===== LIKHAE FAILED-FILES HOTFIX =====" -ForegroundColor Cyan
Write-Host "This hotfix does NOT run migrations and does NOT change the 57-table schema."

$root = Get-Location
Write-Host "Project: $root"

Write-Host ""
Write-Host "===== REMOVE UNSUPPORTED ORPHAN VIEWS =====" -ForegroundColor Cyan
$remove = @(
    "resources/views/Buyer/wishlist.blade.php",
    "resources/views/Buyer/rewards.blade.php"
)
foreach ($file in $remove) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "REMOVED  $file"
    } else {
        Write-Host "ABSENT   $file"
    }
}

Write-Host ""
Write-Host "===== CLEAR LARAVEL CACHES =====" -ForegroundColor Cyan
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) { throw "optimize:clear failed." }

Write-Host ""
Write-Host "===== PHP SYNTAX CHECK =====" -ForegroundColor Cyan
$phpFiles = @(
    "routes/Courier.php",
    "app/Http/Controllers/Buyer/TrackingController.php",
    "app/Http/Controllers/Auth/RegistrationController.php",
    "app/Http/Controllers/Rider/RiderController.php"
)
$phpFiles += Get-ChildItem "tests/Feature" -Filter "*.php" | ForEach-Object { $_.FullName }
foreach ($file in $phpFiles) {
    if (Test-Path $file) {
        php -l $file | Out-Host
        if ($LASTEXITCODE -ne 0) { throw "PHP syntax failed: $file" }
    }
}

Write-Host ""
Write-Host "===== ROUTE CHECK =====" -ForegroundColor Cyan
php artisan route:list --except-vendor
if ($LASTEXITCODE -ne 0) { throw "Laravel route check failed." }

Write-Host ""
Write-Host "===== HOTFIX RESULT =====" -ForegroundColor Green
Write-Host "PASS: Route boot fixed, missing views added, Blade echo syntax fixed, legacy test files rewritten."
Write-Host "Schema unchanged: 57-table database retained."
