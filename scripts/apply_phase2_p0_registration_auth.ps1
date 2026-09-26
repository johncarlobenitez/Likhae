$ErrorActionPreference = 'Stop'

$root = (Get-Location).Path
if (-not (Test-Path (Join-Path $root 'artisan'))) {
    throw 'Run this script from the LIKHAE Laravel project root (the folder containing artisan).'
}

Write-Host '===== LIKHAE PHASE 2 — P0 REGISTRATION / AUTH =====' -ForegroundColor Cyan
Write-Host ('Project: ' + $root)
Write-Host 'This phase does NOT run migrations and does NOT change the final 57-table schema.' -ForegroundColor Yellow

$obsolete = @(
    'app/Http/Controllers/Api/MobileAuthController.php',
    'app/Models/Auth/Role.php',
    'app/Http/Controllers/Auth/SellerApplicationController.php',
    'app/Http/Controllers/Auth/LogisticsProviderApplicationController.php',
    'app/Http/Requests/SellerApplicationRequest.php',
    'app/Http/Requests/LogisticsProviderApplicationRequest.php',
    'resources/views/auth/onboarding/seller-apply.blade.php',
    'resources/views/auth/onboarding/partner-apply.blade.php',
    'resources/views/auth/onboarding/status.blade.php',
    'resources/views/auth/onboarding/riders.blade.php',
    'database/seeders/LikhaeSellerTestAccountsSeeder.php'
)

Write-Host ''
Write-Host '===== REMOVE OBSOLETE REGISTRATION / TOKEN-AUTH FILES =====' -ForegroundColor Cyan
foreach ($relative in $obsolete) {
    $path = Join-Path $root $relative
    if (Test-Path $path) {
        Remove-Item $path -Force
        Write-Host ('REMOVED  ' + $relative)
    } else {
        Write-Host ('SKIP     ' + $relative)
    }
}

Write-Host ''
Write-Host '===== CLEAR LARAVEL CACHES =====' -ForegroundColor Cyan
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) { throw 'php artisan optimize:clear failed.' }

$phpFiles = @(
    'app/Console/Commands/CreateAdmin.php',
    'app/Http/Controllers/Admin/AdminOnboardingController.php',
    'app/Http/Controllers/Auth/AuthenticationController.php',
    'app/Http/Controllers/Auth/GoogleAuthenticationController.php',
    'app/Http/Controllers/Auth/RegistrationController.php',
    'app/Http/Controllers/Logistics/ProviderRiderController.php',
    'app/Http/Controllers/Logistics/RiderApplicationController.php',
    'app/Http/Requests/StoreRegistrationRequest.php',
    'app/Services/RegistrationWorkflowService.php',
    'app/Notifications/RegistrationDecisionNotification.php',
    'config/auth.php',
    'database/factories/UserFactory.php',
    'database/seeders/DatabaseSeeder.php',
    'routes/Admin.php',
    'routes/Seller.php',
    'routes/api.php',
    'routes/logistics.php',
    'routes/web.php'
)

Write-Host ''
Write-Host '===== PHP SYNTAX CHECK =====' -ForegroundColor Cyan
foreach ($relative in $phpFiles) {
    $path = Join-Path $root $relative
    if (-not (Test-Path $path)) { throw ('Missing required Phase 2 file: ' + $relative) }
    php -l $path
    if ($LASTEXITCODE -ne 0) { throw ('PHP syntax check failed: ' + $relative) }
}

Write-Host ''
Write-Host '===== ROUTE CHECK =====' -ForegroundColor Cyan
php artisan route:list --except-vendor
if ($LASTEXITCODE -ne 0) { throw 'Laravel route check failed.' }

Write-Host ''
Write-Host '===== FINAL MIGRATION FILE COUNT =====' -ForegroundColor Cyan
$migrationCount = (Get-ChildItem '.\database\migrations\*.php' -File).Count
Write-Host ('Migration files: ' + $migrationCount)
if ($migrationCount -ne 13) {
    throw ('Expected the finalized 13 migration files but found ' + $migrationCount + '. Do not migrate until this is reconciled.')
}

Write-Host ''
Write-Host '===== FINAL DATABASE TABLE COUNT =====' -ForegroundColor Cyan
$verifyPath = Join-Path $root 'storage/framework/phase2_verify_database.php'
$verifyPhp = @'
<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
echo count($tables) . PHP_EOL;
'@
Set-Content -Path $verifyPath -Value $verifyPhp -Encoding UTF8
try {
    $tableCountRaw = php $verifyPath
    if ($LASTEXITCODE -ne 0) { throw 'Unable to verify database table count.' }
    $tableCount = [int](($tableCountRaw | Select-Object -Last 1).Trim())
    Write-Host ('Database tables: ' + $tableCount)
    if ($tableCount -ne 57) {
        throw ('Expected 57 final tables but found ' + $tableCount + '. Phase 2 will not change the database automatically.')
    }
}
finally {
    if (Test-Path $verifyPath) { Remove-Item $verifyPath -Force }
}

Write-Host ''
Write-Host '===== FRONTEND SOURCE CHECK =====' -ForegroundColor Cyan
if (Get-Command node -ErrorAction SilentlyContinue) {
    node --check '.\resources\js\auth\register.js'
    if ($LASTEXITCODE -ne 0) { throw 'Registration JavaScript syntax check failed.' }
} else {
    Write-Host 'Node.js not found; skipped JS syntax check.' -ForegroundColor Yellow
}

Write-Host ''
Write-Host 'PHASE 2 APPLY: PASS' -ForegroundColor Green
Write-Host 'Final database remains 57 tables.' -ForegroundColor Green
Write-Host 'No migrate:fresh was executed.' -ForegroundColor Green
Write-Host ''
Write-Host 'If you need the first administrator account:' -ForegroundColor Cyan
Write-Host '  php artisan app:create-admin'
Write-Host ''
Write-Host 'Seed only safe baseline categories/settings if needed:' -ForegroundColor Cyan
Write-Host '  php artisan db:seed'
Write-Host ''
Write-Host 'Because resources/js/auth/register.js changed, build frontend assets when using a production build:' -ForegroundColor Cyan
Write-Host '  npm install'
Write-Host '  npm run build'
