# Security remediation closure

This document records the implementation of the **Security Remediation Plan** (without modifying the plan file itself).

## Completed controls

### 1. No raw public JSON

- **Nginx:** [`deploy/aws/nginx-aws.conf`](deploy/aws/nginx-aws.conf) and [`deploy/nginx/site.conf`](deploy/nginx/site.conf) deny all `*.json` URLs with `403`.
- **Apache:** Root [`.htaccess`](.htaccess) blocks `*.json` (removed previous exceptions for `boats.json` / `for-sale.json`).
- **PHP dev router:** [`router.php`](router.php) returns `403` for `*.json` paths before static file handling.

### 2. Data directory

- Operational JSON lives under [`data/`](data/) with helpers in [`components/data_paths.php`](components/data_paths.php).
- Optional `DATA_DIR` in `.env` (documented in [`.env.example`](.env.example)).
- One-time migration from `admin/*.json` and `content/articles.json` runs inside [`loadEnvSafe()`](components/data_access.php).
- [`.gitignore`](.gitignore) and [`data/.gitignore`](data/.gitignore) exclude sensitive JSON and backups.

### 3. Auth / CSRF

- [`components/security.php`](components/security.php): removed legacy **plaintext** password fallback in `verifyPassword`.
- [`blog-portal/api.php`](blog-portal/api.php): CSRF tokens for state-changing blog actions; portal JS sends `X-CSRF-Token` / `csrfToken` on uploads.

### 4. Webhooks and Python handler

- [`backend/webhooks/mollie/webhook_handler_plesk.php`](backend/webhooks/mollie/webhook_handler_plesk.php): production **fails closed** without `MOLLIE_WEBHOOK_SECRET`; signature verification required when secret is set; bookings path uses `data/bookings.json`.
- [`backend/webhooks/mollie/webhook_handler_production.py`](backend/webhooks/mollie/webhook_handler_production.py): HMAC verification, `NIJENHUIS_ROOT`/`data/` paths, **removed unsafe PHP subprocess** email; SMTP-only with clear errors if unset.

### 5. Transport / CORS

- [`backend/api/app.py`](backend/api/app.py): production CORS limited to HTTPS site origins; localhost ports only when `APP_ENV=development`.

### 6. Admin cleanup

- [`admin/for-sale-management.php`](admin/for-sale-management.php): removed debug `127.0.0.1:7433` ingest calls.

### 7. CI / dependencies

- [`.github/workflows/security.yml`](.github/workflows/security.yml): PHP syntax checks and `npm audit` (non-blocking where legacy dev deps remain).
- `npm audit fix` was run; remaining issues are mostly **dev-only** chains (`live-server`, `workbox`, `vite`/`esbuild`). Address with upgrades or removing unused dev tooling in a follow-up.

### 8. Deploy

- [`scripts/deploy_aws.sh`](scripts/deploy_aws.sh): excludes live `data/bookings.json`, `data/for-sale.json`, etc., similar to previous `admin/` excludes.

## Your follow-up (Mollie)

1. Set **`MOLLIE_WEBHOOK_SECRET`** in production `.env` to match Mollie dashboard.
2. Redeploy **nginx** so the `location ~* \.json$` block is active.
3. On the server, ensure **`data/`** exists and is writable by PHP-FPM; copy or rely on migration from legacy `admin/*.json` if needed.

## Re-test checklist

```bash
# After nginx reload on production
curl -sI "https://YOUR_DOMAIN/admin/bookings.json" | head -1
curl -sI "https://YOUR_DOMAIN/data/bookings.json" | head -1
# Expect 403

curl -s "https://YOUR_DOMAIN/api/health"
# Expect {"status":"ok"}
```

Run booking checkout and blog portal save/delete in staging before production cutover.
