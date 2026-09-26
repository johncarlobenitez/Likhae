# LIKHAE Phase 2 Route Hotfix

This patch adds the missing `app/Http/Controllers/Api/ProductApiController.php` required by `routes/api.php`.

It does not add migrations, does not change the final 57-table schema, and does not re-enable Sanctum or `personal_access_tokens`.

## Apply

Extract this ZIP into the Laravel project root:

```powershell
C:\Users\mcarl\OneDrive\Documents\Likhae
```

Then run:

```powershell
cd "C:\Users\mcarl\OneDrive\Documents\Likhae"
powershell -ExecutionPolicy Bypass -File .\scripts\apply_phase2_route_hotfix.ps1
```

After it passes, rerun Phase 2 if you want to complete the same validation script:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\apply_phase2_p0_registration_auth.ps1
```
