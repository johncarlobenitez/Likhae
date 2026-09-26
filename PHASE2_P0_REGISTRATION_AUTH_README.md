# LIKHAE Phase 2 — P0 Registration & Authentication

This package is an **incremental Phase 2 update** for the finalized LIKHAE application. Apply **Phase 1 — P0 Data Layer** first.

## Schema rule

- Final database remains **57 tables**.
- No migration is added, removed, or modified by this Phase 2 package.
- Do **not** run `php artisan migrate:fresh` for this phase.
- One account has one fixed `users.account_type`: `BUYER`, `SELLER`, `ADMIN`, `LOGISTICS`, or `RIDER`.
- No `roles`, `user_roles`, Sanctum token table, or Buyer-to-Seller upgrade flow is used.

## What Phase 2 changes

- Replaces old role-pivot login logic with fixed `account_type` authentication.
- Rebuilds public registration for Buyer, Seller, Logistics, and Rider.
- Buyer/Seller/Logistics applications are reviewed by Admin.
- Rider applications are reviewed by the **selected active Logistics Center**.
- Creates `seller_profiles`, `logistics_centers`, and `rider_profiles` only after approval.
- Stores registration files privately on the existing `registrations` filesystem disk.
- Rebuilds Admin registration queue/detail/approve/reject/document access.
- Rebuilds Logistics rider-application queue/detail/approve/reject/document access.
- Removes direct Logistics creation of Rider accounts; riders self-register and are approved.
- Deletes obsolete `app/Models/Auth/Role.php`; any temporary `hasRole()` calls only proxy to `account_type` until old modules are rewritten.
- Keeps Google sign-in only as a Buyer convenience without adding database columns/tables.
- Removes Sanctum/mobile-login routes. Flutter remains a consumer of Laravel API/resource endpoints; there is no separate token-auth schema in this project phase.
- Replaces the old incompatible development seeder with a safe baseline category/settings seeder.
- Rewrites `UserFactory` for the final account/status fields.
- Rewrites `app:create-admin` for the final `users` table.

## Install

Extract this ZIP directly over the existing project root:

```text
C:\Users\mcarl\OneDrive\Documents\Likhae
```

Then run:

```powershell
cd "C:\Users\mcarl\OneDrive\Documents\Likhae"
powershell -ExecutionPolicy Bypass -File .\scripts\apply_phase2_p0_registration_auth.ps1
```

The script removes the obsolete registration/token-auth files, clears caches, lints Phase 2 PHP files, loads the route table, verifies there are 13 final migration files, and verifies the connected database still contains exactly 57 tables. It does **not** migrate or wipe the database.

## First administrator

If the fresh final database has no Admin account yet:

```powershell
php artisan app:create-admin
```

## Baseline categories

Seller registration requires at least one active category. The Phase 2 `DatabaseSeeder` safely seeds a small baseline category list and preserves the canonical commission/currency settings:

```powershell
php artisan db:seed
```

It does not recreate the old fake users, role pivots, geography tables, inventory tables, or old test seller fixtures.

## Frontend build

`resources/js/auth/register.js` changed. For production/static Vite assets, rebuild locally:

```powershell
npm install
npm run build
```

The source JavaScript was syntax-checked in the generated package. The packaged source is authoritative; prebuilt Vite assets are not bundled because the generation environment could not load the platform-specific Rolldown native binding from the Windows-origin `node_modules` tree.

## Registration ownership

| Account type | Reviewer | Profile after approval |
|---|---|---|
| Buyer | Admin | none; `users` + `addresses` are sufficient |
| Seller | Admin | `seller_profiles` |
| Logistics | Admin | `logistics_centers` |
| Rider | selected Logistics Center | `rider_profiles` |

Pending registrations are not logged in. Approval activates the account and enables the appropriate workspace.

## Intentionally deferred

Phase 2 does not rewrite the remaining commerce/operations modules that still depend on old columns. Product management, cart/checkout, seller fulfillment, logistics dispatch/scanning, rider delivery, Admin operations, messaging, reports, and end-to-end browser tests are handled in later phases.
