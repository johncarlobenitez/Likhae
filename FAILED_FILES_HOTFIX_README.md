# LIKHAE Failed Files Hotfix

This package contains full replacement files in their original Laravel locations.

It targets the previous FAIL audit items:

- missing `routes/Courier.php`
- missing `auth.register` view
- missing `Rider.messages` view
- missing `TrackingController::proof()` method
- broken single-brace Blade echo syntax in failed Blade files
- legacy test files that referenced removed schema fields/tables
- Rider Blade case mismatch `rider.app` → `Rider.app`

## Apply

1. Extract this ZIP into your project root:

```text
C:\Users\mcarl\OneDrive\Documents\Likhae
```

2. Allow overwrite.

3. Run:

```powershell
cd "C:\Users\mcarl\OneDrive\Documents\Likhae"
powershell -ExecutionPolicy Bypass -File .\scripts\apply_failed_files_hotfix.ps1
```

The script does not run migrations and does not touch the 57-table schema.

It only deletes two unsupported orphan views that Windows drag-and-drop cannot delete by itself:

```text
resources/views/Buyer/wishlist.blade.php
resources/views/Buyer/rewards.blade.php
```

## Copied files

Total copied replacement files: 140

