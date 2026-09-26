# Likhae Phase 1 — P0 Data Layer

This package is designed to be extracted **directly into the Likhae project root** so every file lands in its original Laravel location (`app/Models`, `app/Http/Middleware`, `app/Policies`, `app/Providers`).

## Scope completed

- Keeps the already-migrated **57-table database unchanged**.
- Rewrites `User` to use one fixed `users.account_type`: `BUYER`, `SELLER`, `ADMIN`, `LOGISTICS`, `RIDER`.
- Removes database dependence on `roles` / `user_roles`.
- Adds a canonical Eloquent model for **all 50 Likhae application tables** in the final schema.
- Keeps small compatibility wrappers for old class names whose concepts still exist (`Seller`, `Rider`, `LogisticsProvider`, `DeliveryEvent`, `ProductReview`, old Admin class names, Seller `Message`). These wrappers now point to valid final tables.
- Removes obsolete model files whose tables/features no longer exist: Role, Wishlist, Return/Refund, ProductSpecification, SellerCampaign, SellerOrderEvent, WorkspaceNotification, Payout, LedgerEntry, AdminPreference.
- Rewrites money casts to final DECIMAL fields instead of `*_minor` model fields.
- Normalizes final uppercase statuses/account types.
- Rewrites role/account middleware to `account_type`.
- Adds canonical SellerProfile, LogisticsCenter and RiderProfile policies and keeps compatibility policy names.
- Rewrites `AppServiceProvider` sidebar/header counts to final `notifications`, conversations/messages, carts, and seller orders.
- Does **not** add `personal_access_tokens` or Sanctum tables. Flutter can consume Laravel JSON/API resources without changing the final 57-table schema. API authentication/routing decisions belong to the auth/API phase, not this schema phase.

## Apply

1. Back up the project.
2. Extract this ZIP into `C:\Users\mcarl\OneDrive\Documents\Likhae` and allow overwrite.
3. From the project root run:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\apply_phase1_p0_data_layer.ps1
```

The script deletes only the explicitly obsolete model files, lints the updated PHP files, refreshes Composer autoload/cache, and displays migration status. It does **not** run migrations.

## Important

Controllers, Requests, Services, Routes, Seeders and Blade pages are intentionally not fully rewritten in Phase 1. Some of them still reference legacy columns/features and will be handled in the next phases. The data layer is the new source of truth for those rewrites.
