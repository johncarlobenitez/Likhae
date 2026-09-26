# LIKHAE 100% Audit Hotfix

This package targets the remaining audit failures from the 99.4% re-audit.

## Fixes

- Removes legacy model files:
  - `app/Models/Logistics/DeliveryEvent.php`
  - `app/Models/Logistics/LogisticsProvider.php`
- Removes old `User::hasRole()` helper.
- Replaces remaining `seller_id` query key in Buyer messages with `seller_profile_id`.
- Rewrites Logistics dispatch view to use:
  - `tracking_number`
  - `current_status`
  - `rider_profile_id`
  - `assignment_type = DELIVERY`
- Removes remaining seller payout route/view references.
- Keeps the 57-table schema unchanged.
- Adds no migrations.

## Apply

Extract into your project root and allow overwrite:

```powershell
cd "C:\Users\mcarl\OneDrive\Documents\Likhae"
powershell -ExecutionPolicy Bypass -File .\scripts\apply_100_percent_hotfix.ps1
```
