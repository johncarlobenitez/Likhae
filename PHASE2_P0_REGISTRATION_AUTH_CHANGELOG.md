# Phase 2 P0 Registration/Auth — Change Log

## Added

- `app/Http/Requests/StoreRegistrationRequest.php`
- `app/Services/RegistrationWorkflowService.php`
- `app/Notifications/RegistrationDecisionNotification.php`
- `app/Http/Controllers/Logistics/RiderApplicationController.php`
- `resources/views/auth/onboarding/admin-application.blade.php`
- `scripts/apply_phase2_p0_registration_auth.ps1`

## Rewritten / aligned

- `app/Console/Commands/CreateAdmin.php`
- `app/Http/Controllers/Admin/AdminOnboardingController.php`
- `app/Http/Controllers/Auth/AuthenticationController.php`
- `app/Http/Controllers/Auth/GoogleAuthenticationController.php`
- `app/Http/Controllers/Auth/RegistrationController.php`
- `app/Http/Controllers/Logistics/ProviderRiderController.php`
- `config/auth.php`
- `database/factories/UserFactory.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/js/auth/register.js`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/onboarding/admin-queue.blade.php`
- `resources/views/Logistics/riders/application/index.blade.php`
- `resources/views/Logistics/riders/application/show.blade.php`
- `resources/views/Logistics/riders/show.blade.php`
- `routes/Admin.php`
- `routes/Seller.php`
- `routes/api.php`
- `routes/logistics.php`
- `routes/web.php`

## Removed by apply script

- `app/Models/Auth/Role.php`
- `app/Http/Controllers/Api/MobileAuthController.php`
- `app/Http/Controllers/Auth/SellerApplicationController.php`
- `app/Http/Controllers/Auth/LogisticsProviderApplicationController.php`
- `app/Http/Requests/SellerApplicationRequest.php`
- `app/Http/Requests/LogisticsProviderApplicationRequest.php`
- old seller/logistics upgrade onboarding views
- old onboarding status/rider queue views
- `database/seeders/LikhaeSellerTestAccountsSeeder.php`

## Validation performed before packaging

- Phase 2 PHP files: syntax checked.
- Registration JavaScript: `node --check` passed.
- Laravel route table: loaded successfully.
- Route count during generation: 224.
- Duplicate named routes: 0.
- Duplicate method + URI routes: 0.
- Final migration files remain 13.
- No Phase 2 migration/schema change was introduced.
