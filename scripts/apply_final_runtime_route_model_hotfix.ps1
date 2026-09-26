Write-Host "===== LIKHAE FINAL RUNTIME ROUTE/MODEL HOTFIX CHECK =====" -ForegroundColor Cyan
Write-Host "This script does not run migrations and does not change the 57-table schema."
Write-Host ""

Write-Host "===== CLEAR CACHE =====" -ForegroundColor Cyan
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) { throw "optimize:clear failed" }

Write-Host ""
Write-Host "===== PHP SYNTAX CHECK =====" -ForegroundColor Cyan
$paths = @(
    "app/Http/Controllers/Seller/SellerAccountController.php",
    "resources/views/components/buyer/sidebar.blade.php",
    "resources/views/components/buyer/order-card.blade.php",
    "resources/views/components/seller/header.blade.php",
    "resources/views/components/seller/sidebar.blade.php"
)
foreach ($path in $paths) {
    php -l $path
    if ($LASTEXITCODE -ne 0) { throw "PHP lint failed: $path" }
}

Write-Host ""
Write-Host "===== ROUTE BOOT CHECK =====" -ForegroundColor Cyan
php artisan route:list --except-vendor
if ($LASTEXITCODE -ne 0) { throw "route:list failed" }

Write-Host ""
Write-Host "===== CRITICAL ROUTE NAME CHECK =====" -ForegroundColor Cyan
$routes = php artisan route:list --json --except-vendor | ConvertFrom-Json
$routeNames = @($routes | ForEach-Object { $_.name } | Where-Object { $_ })
$required = @("buyer.home", "seller.dashboard", "logistics.dashboard", "rider.dashboard", "admin.dashboard")
foreach ($name in $required) {
    if ($routeNames -contains $name) {
        Write-Host "PASS route: $name" -ForegroundColor Green
    } else {
        throw "Missing required route: $name"
    }
}

Write-Host ""
Write-Host "===== MISSING ROUTE REFERENCE CHECK =====" -ForegroundColor Cyan
$badRouteRefs = Select-String -Path "app/**/*.php","resources/views/**/*.php","routes/**/*.php" -Pattern "buyer\.rewards|buyer\.orders\.return|buyer\.orders\.review['\"]|tracking_code|pickup_rider_id|delivery_rider_id|logistics_provider_id|role_user|user_roles|\bhasRole\b|\bgrant\(" -AllMatches -ErrorAction SilentlyContinue
if ($badRouteRefs) {
    $badRouteRefs | Format-Table Path, LineNumber, Line -AutoSize
    throw "Critical legacy route/schema references remain."
}
Write-Host "PASS: no critical legacy route/schema references found." -ForegroundColor Green

Write-Host ""
Write-Host "FINAL HOTFIX CHECK: PASS" -ForegroundColor Green
