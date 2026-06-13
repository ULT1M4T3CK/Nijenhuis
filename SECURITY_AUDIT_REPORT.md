# Security Audit Report — Nijenhuis Botenverhuur

**Date:** 2026-03-27  
**Scope:** Local codebase (`/home/andre/Desktop/Projects/nijenhuis`) and live host `https://nijenhuis-botenverhuur.com`.  
**Note:** The hostnames `nijenhuis-botenverhuur.nl` and `niijenhuis-botenverhuur.nl` did not resolve from the audit environment; production behavior was verified against `nijenhuis-botenverhuur.com`, which matches [`deploy/aws/nginx-aws.conf`](deploy/aws/nginx-aws.conf).

---

## Executive summary

The application implements several solid patterns (API key comparison with `hash_equals`, CORS allowlists, session cookie settings, finfo-based uploads, Mollie server-side key usage). **However, live testing found critical data exposure:** customer booking data and other business JSON files are **directly downloadable over HTTPS** without authentication. This is a **GDPR/privacy incident-class** issue and must be fixed immediately at the web server or application layer.

Secondary risks include: legacy plaintext password support in `verifyPassword`, optional webhook signature bypass when `MOLLIE_WEBHOOK_SECRET` is empty, an alternate Python webhook handler without cryptographic verification of inbound POSTs, PHP code generation in a Python email fallback, hard-coded debug telemetry to localhost in an admin page, and multiple `npm audit` findings (mostly in dev/build tooling).

---

## 1. Threat surface inventory

### 1.1 Public web (PHP, nginx document root)

| Area | Paths / mechanism |
|------|-------------------|
| Marketing / booking UI | Clean URLs from [`router.php`](router.php) and nginx rewrites → `pages/*.php` (e.g. `/`, `/booking`, `/checkout`, boat slugs from `admin/boats.json`). |
| Admin UI | `/admin-login`, `/employee-login`, `/employee-portal`, PHP under `/admin/*.php`. |
| Blog | **Blocked on AWS** (`location ^~ /blog` → 404 per nginx-aws); full blog routes exist in [`router.php`](router.php) for local/dev. |
| Blog portal | **Blocked on AWS** (`location ^~ /blog-portal` → 404); [`blog-portal/api.php`](blog-portal/api.php) handles auth and CRUD when reachable. |
| **Static JSON under web root** | `admin/bookings.json`, `admin/boats.json`, `admin/for-sale.json`, `content/articles.json` — **served as static files if present** (see Section 3). |

### 1.2 JSON APIs (PHP)

| Endpoint | Role |
|----------|------|
| [`admin/booking-handler.php`](admin/booking-handler.php) | Session-based admin/employee; `BOOKING_API_KEY` for `createBooking`; CSRF for authenticated admin actions; many `action` values (bookings, boats, for-sale, uploads). |
| [`admin/api.php`](admin/api.php) | External integration: `X-API-Key` / `Bearer`; GET `boats`, `availability`; POST `checkAvailability`, `prepareBooking`. |
| [`mollie_api.php`](mollie_api.php) | Mollie proxy; CORS + origin checks; payment/cart actions. |
| [`blog-portal/api.php`](blog-portal/api.php) | Blog login, session, list/get/save article, image upload. |

### 1.3 Backend (Python)

| Component | Exposure |
|-----------|----------|
| [`backend/api/app.py`](backend/api/app.py) | Flask on port 8080; blueprint [`/api/health`](backend/api/routes/health.py) (proxied as `/api/` in nginx). |
| [`backend/chatbot/`](backend/chatbot/) | Separate Flask app (JWT/API keys); dev proxy in Vite — not mapped in [`deploy/aws/nginx-aws.conf`](deploy/aws/nginx-aws.conf) sample. |
| [`backend/webhooks/mollie/webhook_handler_plesk.php`](backend/webhooks/mollie/webhook_handler_plesk.php) | Mollie webhook; signature logic; GET health and non-prod `simulatePaid`. |
| [`backend/webhooks/mollie/webhook_handler_production.py`](backend/webhooks/mollie/webhook_handler_production.py) | Standalone `HTTPServer`; **no HMAC verification** on POST body; verifies payment via Mollie API using server key (trusts `id` from body). |

### 1.4 Reverse proxy / third party

| Path | Behavior |
|------|----------|
| `/api/` → `127.0.0.1:8080` | Flask health and future APIs. |
| `/vybris/api/` → `ultimaitech.com` | Origin/Referer spoofed to third-party host — review trust and data flow. |

---

## 2. Static code review (selected findings)

### 2.1 Authentication and CSRF

- [`admin/booking-handler.php`](admin/booking-handler.php): `requireCsrf` rejects when `$_SESSION['csrf_token']` is non-empty and the request token mismatches. Admin login sets `csrf_token`; employee login also sets it (see ~587). The comment at ~850 (“employee sessions bypass”) is **misleading** once `csrf_token` is set — employees with a session token **are** protected the same as admins for those code paths.
- [`blog-portal/api.php`](blog-portal/api.php): Mutations (`saveArticle`, etc.) rely on **session cookie only** — no CSRF token. If the blog portal is ever exposed on the same-site deployment, classic **CSRF** against authenticated editors is possible.

### 2.2 Legacy password handling

[`components/security.php`](components/security.php) `verifyPassword` still allows **legacy plaintext** comparison when the stored value does not look like bcrypt/argon2 (~246–248). Any remaining plaintext env values are a critical weakness.

### 2.3 Webhooks

- [`webhook_handler_plesk.php`](backend/webhooks/mollie/webhook_handler_plesk.php): If `MOLLIE_WEBHOOK_SECRET` is **empty**, `verifyWebhookSignature` returns **true** without checking (~64–66) — **forged webhooks** if the URL is reachable.
- [`webhook_handler_production.py`](backend/webhooks/mollie/webhook_handler_production.py): Accepts JSON POST, extracts `id`, then calls Mollie API — **no verification** that the POST came from Mollie. Mitigation must be network ACL or signature verification.

### 2.4 Injection / unsafe composition

- [`webhook_handler_production.py`](backend/webhooks/mollie/webhook_handler_production.py) ~415–437: Builds a PHP script string with `customer_email`, `subject`, and `html_body` embedded in single-quoted PHP strings. Malicious content could **break out** of the generated PHP; use SMTP path only or pass data via `proc`/`stdin` with proper escaping.

### 2.5 Debug / telemetry

[`admin/for-sale-management.php`](admin/for-sale-management.php) contains multiple `fetch('http://127.0.0.1:7433/ingest/...')` calls that exfiltrate session/debug context to a local agent. **Remove before production** or gate behind a dev flag — if this page is deployed, browsers will fail silently but **build artifacts must not ship this**.

### 2.6 Information disclosure in repo

- [`.env`](.env) in workspace: must never be committed or shared; rotate keys if exposed.
- [`backend/webhooks/mollie/webhook_handler_production.py`](backend/webhooks/mollie/webhook_handler_production.py): Hard-coded server IP and filesystem paths aid targeted attacks.
- [`admin/bookings.json`](admin/bookings.json): If tracked or copied, contains **PII** — treat as confidential.

### 2.7 `.gitignore` gap

[`.gitignore`](.gitignore) does **not** ignore `admin/bookings.json` or other live JSON data files — risk of accidental commit of PII.

---

## 3. Dynamic testing (live) — `https://nijenhuis-botenverhuur.com`

| Test | Result |
|------|--------|
| `GET /api/health` | `200`, `{"status":"ok"}` |
| `OPTIONS /admin/api.php` with `Origin: https://evil.example` | `403` — CORS rejected |
| `OPTIONS /admin/api.php` with allowed Origin | `200` + `Access-Control-Allow-Origin` + credentials |
| `GET /admin/api.php?action=boats` without API key | `401` Unauthorized |
| `POST /admin/booking-handler.php` `{"action":"getBookings"}` without session | `401` Unauthorized |
| `GET /.env` | `403` (denied) |
| **`GET /admin/bookings.json`** | **`200`**, `Content-Type: application/json`, body is JSON array of bookings (**critical**) |
| `GET /admin/boats.json` | `200` |
| `GET /admin/for-sale.json` | `200` |
| `GET /content/articles.json` | `200` |
| `GET /webhooks/mollie/webhook_handler_plesk.php` | `404` (path not deployed or not under this URL on this host) |
| `GET /mollie_api.php?action=getBookingStatus` (no id) | JSON error `bookingId is required` (endpoint reachable; no open redirect observed in this probe) |

**Severity:** Unauthenticated download of `bookings.json` is **Critical** — it exposes names, emails, phones, addresses, payment-related identifiers, and reservation details to any client that can request the URL.

---

## 4. Dependency and supply-chain (`npm audit`)

**Root** (`npm audit`): **21** issues reported (**6 moderate, 15 high**), including transitive issues in `live-server`, `workbox-webpack-plugin`, `rollup`, `minimatch`, `braces`, etc. Many are **dev/build-time** risks; prioritize based on whether those tools run in CI or on production servers.

**Frontend** (`frontend/npm audit`): **3** issues (**2 moderate, 1 high**), including `esbuild` / `vite` dev-server advisory — **development-only** exposure if `vite dev` is bound to non-localhost.

**Recommendation:** Run `npm audit fix` in both trees; for `vite` major upgrades, plan a separate upgrade path. Add `npm audit` to CI with a policy (e.g. fail on high in production dependencies only).

---

## 5. Findings summary (severity)

| ID | Severity | Title |
|----|----------|--------|
| F-01 | **Critical** | Public HTTP access to `admin/bookings.json` (and other JSON) on production — unauthenticated PII disclosure |
| F-02 | High | Webhook signature optional when `MOLLIE_WEBHOOK_SECRET` empty (`webhook_handler_plesk.php`) |
| F-03 | High | Python standalone webhook accepts POST without cryptographic verification (`webhook_handler_production.py`) |
| F-04 | High | PHP subprocess email fallback embeds user-controlled strings unsafely (`webhook_handler_production.py`) |
| F-05 | Medium | Legacy plaintext path in `verifyPassword` (`components/security.php`) |
| F-06 | Medium | Blog portal mutations without CSRF tokens if blog API is exposed (`blog-portal/api.php`) |
| F-07 | Medium | Debug `127.0.0.1:7433` ingest calls in `for-sale-management.php` |
| F-08 | Medium | Flask CORS allows `http://85.215.195.147` ([`backend/api/app.py`](backend/api/app.py)) — reduce if not required |
| F-09 | Low | CSP allows `'unsafe-inline'` scripts on booking handler responses — weakens XSS defense-in-depth |
| F-10 | Low / Ops | `npm audit` debt in root and frontend — patch and separate prod vs dev dependency policy |

---

## 6. Remediation plan (prioritized)

1. **Immediate — F-01:** Block direct HTTP access to sensitive JSON:
   - **nginx:** `location ^~ /admin/` with `location ~ \.json$ { deny all; }` or move JSON **outside** `document_root` and read only via PHP with auth.
   - Ensure `boats.json` / `for-sale.json` exposure is **intentional**; if the public site only needs derived data, expose a **sanitized** read-only API instead of raw files.
2. **F-02:** Always set `MOLLIE_WEBHOOK_SECRET` in production; refuse to process webhooks if unset.
3. **F-03:** Prefer the PHP handler with signature verification, or add Mollie’s signature verification to the Python handler; restrict listener to Mollie IPs or internal network if feasible.
4. **F-04:** Remove `php -r` string embedding; use SMTP only or pass payload safely.
5. **F-05:** Remove legacy plaintext branch after confirming all hashes are bcrypt/argon2.
6. **F-06:** Add CSRF tokens to blog POSTs or use `SameSite=Strict` session cookies and re-verify for mutations.
7. **F-07:** Delete or `#if dev` guard all ingest `fetch` calls.
8. **F-08:** Trim Flask `allowed_origins` to HTTPS production hostnames only.
9. **Supply chain:** Apply `npm audit fix`; track remaining issues; consider `npm ci` + lockfile in CI.

---

## 7. Re-test checklist

After fixes:

- [ ] `curl -I https://nijenhuis-botenverhuur.com/admin/bookings.json` returns **403** or **404** (or empty body without PII).
- [ ] `admin/boats.json` and `for-sale.json` behavior matches product intent (public vs private).
- [ ] Webhook endpoint rejects unsigned requests when secret is configured; logs no secrets.
- [ ] Blog portal (if enabled) CSRF or cookie policy verified with manual CSRF PoC **failing**.
- [ ] `npm audit` shows no unaccepted highs in runtime dependency paths (document exceptions).
- [ ] `.env` and `bookings.json` not in git; add ignore rules if needed.
- [ ] Re-run CORS preflight with disallowed origin → **403**.

---

## 8. Evidence handling

This report intentionally **does not reproduce** full booking records from production responses. Store raw HTTP transcripts and redacted samples under your internal security process if you need compliance evidence.

---

*End of report.*
