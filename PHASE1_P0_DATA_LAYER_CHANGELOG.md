# Phase 1 P0 Data Layer — Completion Checklist

## Completed

- [x] Final 57-table migrations are untouched.
- [x] 50 canonical application-table models are present (one for every non-framework Likhae table).
- [x] `User` now uses fixed `account_type`, not role pivots.
- [x] No Phase 1 model/middleware/provider/policy code contains legacy `*_minor` money fields.
- [x] No Phase 1 model/middleware/provider/policy code uses old `seller_id`, `buyer_id`, `logistics_provider_id`, `user_roles`, `workspace_notifications`, or lowercase active/approved status comparisons.
- [x] Dynamic product structure is modeled through Product → Options → Values and Product → Variants → Variant Option Values.
- [x] Pickup and delivery are modeled through `rider_assignments.assignment_type` (`PICKUP` / `DELIVERY`).
- [x] Middleware is fixed-account-type aware.
- [x] Canonical policies added for SellerProfile, LogisticsCenter and RiderProfile.
- [x] Shipment policy uses final buyer/seller/logistics/rider relationships.
- [x] AppServiceProvider UI counts now use final carts, seller orders, conversation messages and notifications.
- [x] No Sanctum/personal access token table was added.

## Legacy models removed by apply script

- `app/Models/Admin/AdminPreference.php`
- `app/Models/Admin/LedgerEntry.php`
- `app/Models/Auth/Role.php`
- `app/Models/Buyer/Refund.php`
- `app/Models/Buyer/ReturnRequest.php`
- `app/Models/Buyer/WishlistItem.php`
- `app/Models/Seller/Payout.php`
- `app/Models/Seller/ProductSpecification.php`
- `app/Models/Seller/SellerCampaign.php`
- `app/Models/Seller/SellerOrderEvent.php`
- `app/Models/Seller/WorkspaceNotification.php`

## Transitional compatibility wrappers kept

These classes remain only to prevent immediate class-loading failures while controllers are migrated in later phases. They all point to valid final-schema tables:

- `Seller` → `SellerProfile`
- `Rider` → `RiderProfile`
- `LogisticsProvider` → `LogisticsCenter`
- `DeliveryEvent` → `ShipmentEvent`
- `ProductReview` → `Review`
- `Seller\Message` → `Communication\Message`
- `AdminAnnouncement` → `Announcement`
- `AdminPolicy` → `PlatformPolicy`
- `AdminAuditLog` → `AuditLog`

## Validation performed before packaging

- PHP lint: **72 updated PHP files passed**.
- Canonical model load/table-name test: **50/50 passed**.
- Model `$fillable` fields were checked against the final migration columns: **50/50 matched**.
- Legacy money/FK/status grep across the Phase 1 PHP package: **0 matches** for the targeted old fields.

Full Laravel Artisan boot testing in the build container is limited because that container lacks PHP's DOM extension (`DOMDocument`). This is an environment limitation of the build container, not a syntax/model mapping failure. The included PowerShell apply script runs validation in your actual XAMPP/Laravel environment.
