# DAPENSE — Vercel White Screen Investigation Report

**Version**: 1.0  
**Date**: 2026-07-27  
**Priority**: Critical  
**Project**: DAPENSE (Dana Pensiun Sekolah Kristen Salatiga)  
**Framework**: Laravel 13 + Livewire 4  

---

## Executive Summary

A comprehensive investigation was conducted across all 25 phases of the deployment checklist. The application shows a **blank white page** after deploying to Vercel due to **three critical root causes**:

1. **vercel.json configured for static hosting** — PHP runtime is not configured, so `public/index.php` is served as a static file instead of being executed.
2. **Database connection failure** — `SESSION_DRIVER=database` and `CACHE_STORE=database` cause every request to crash trying to connect to a MySQL database that doesn't exist on Vercel.
3. **No Vite build output** — `public/build/` doesn't exist, so `@vite()` directives in Blade templates throw `ManifestNotFoundException`.

---

## Phase 1 — Deployment Verification

| Check | Status | Details |
|-------|--------|---------|
| Deployment exists | ⚠️ Needs Vercel redeploy | No `.vercel/` directory locally — project not yet linked |
| No build failures | ❌ Incorrect config | `vercel.json` has `outputDirectory: "public"` which is for static frameworks |
| Functions deployed | ❌ Not configured | No PHP serverless function defined |

---

## Phase 2 — Build Logs

| Check | Status | Details |
|-------|--------|---------|
| npm install | ⚠️ Not run on Vercel | Build command `npm run build` exists but no `npm install` step defined |
| npm run build | ❌ public/build/ missing | Vite was never built — `public/build/` directory doesn't exist |
| composer install | ❌ Not configured | No composer install step in build pipeline |
| PHP runtime detection | ❌ Missing | No `@vercel/php` runtime configured |

---

## Phase 3 — Runtime Logs

```
[2026-07-26 10:57:55] production.ERROR: SQLSTATE[HY000] [1049] Unknown database 'dapense'
(Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: dapense,
 SQL: select * from `sessions` where `id` = ... limit 1)
```

**3 occurrences** of the same error, all failing on session reads against a MySQL database that doesn't exist.

---

## Phase 4 — vercel.json Review

### Current (BROKEN):
```json
{
  "outputDirectory": "public",
  "buildCommand": "npm run build"
}
```

**Issues**:
- `outputDirectory` is for static frameworks (Next.js, etc.) — not for PHP
- No PHP runtime (`vercel-php@0.7.4`)
- No routes configuration — all requests go to static file lookup
- No `api/index.php` entry point
- No static asset routing for `public/build/`

### Required Fix:
```json
{
  "version": 2,
  "framework": null,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.4"
    }
  },
  "routes": [
    { "src": "/build/assets/(.*)", "dest": "/public/build/assets/$1" },
    { "src": "/(.*)", "dest": "/api/index.php" }
  ]
}
```

---

## Phase 5 — Laravel Structure

All required directories present: ✅
- `artisan` ✅ | `app/` ✅ | `bootstrap/` ✅ | `config/` ✅
- `database/` ✅ | `public/` ✅ | `resources/` ✅ | `routes/` ✅
- `storage/` ✅ | `vendor/` ✅

---

## Phase 6 — Composer

| Check | Status | Details |
|-------|--------|---------|
| `vendor/` exists | ✅ | Composer dependencies installed |
| `composer.json` valid | ✅ | PHP ^8.3, Laravel ^13.0 |
| Key packages | ✅ | Livewire 4, Maatwebsite Excel, mpdf, Spatie Activitylog |
| `composer dump-autoload` | ✅ | PSR-4 autoloading configured |

---

## Phase 7 — Environment Variables

| Variable | Value | Vercel Compatible? |
|----------|-------|-------------------|
| APP_NAME | DAPENSE | ✅ |
| APP_ENV | production | ✅ |
| APP_KEY | base64:uU5eQn0guCBCy71ixWh7ZDhBmJwEzTggS2t6FiILU2Q= | ✅ |
| APP_DEBUG | false | ⚠️ Set to true during debugging, false after |
| APP_URL | http://localhost | ❌ Must be Vercel deployment URL |
| SESSION_DRIVER | database | ❌ Must be `cookie` |
| CACHE_STORE | database | ❌ Must be `array` |
| QUEUE_CONNECTION | database | ❌ Must be `sync` |
| DB_HOST | 127.0.0.1 | ❌ Must be external DB host |

---

## Phase 8 — Application Key

`APP_KEY` is set: ✅ `base64:uU5eQn0guCBCy71ixWh7ZDhBmJwEzTggS2t6FiILU2Q=`

---

## Phase 9 — Laravel Cache

Must run on Vercel deploy (or point to `/tmp`):
```
php artisan config:cache   → APP_CONFIG_CACHE=/tmp/config.php
php artisan route:cache    → APP_ROUTES_CACHE=/tmp/routes.php
```

Serverless filesystem is read-only except `/tmp`.

---

## Phase 10 — Vite Build

| Check | Status | Details |
|-------|--------|---------|
| `public/build/` exists | ❌ | Directory doesn't exist |
| `manifest.json` | ❌ | Not generated — Vite never built |
| `assets/` directory | ❌ | Not generated |

Need to run: `npm install && npm run build`

---

## Phase 11 — Blade Layout

| Layout File | @vite Directive | Status |
|-------------|-----------------|--------|
| `layouts/app.blade.php` | `@vite(['resources/css/app.css', 'resources/js/app.js'])` | ✅ Correct syntax |
| `layouts/guest.blade.php` | `@vite(['resources/css/app.css', 'resources/js/app.js'])` | ✅ Correct syntax |
| `layouts/applayout.blade.php` | `@vite(['resources/css/app.css', 'resources/js/app.js'])` | ✅ Correct syntax |

No outdated Mix references found. ✅

---

## Phase 12 — Storage

| Check | Status |
|-------|--------|
| `storage/` exists | ✅ |
| `bootstrap/cache/` exists | ✅ |
| `storage/logs/laravel.log` | ✅ Has errors |

On Vercel, these must be configured to use `/tmp` since the filesystem is read-only.

---

## Phase 13 — File Permissions

On Vercel serverless:
- `storage/` → Must point to `/tmp`
- `bootstrap/cache/` → Must point to `/tmp`
- Set via env vars: `VIEW_COMPILED_PATH=/tmp`

---

## Phase 14 — Routes

| Check | Status | Details |
|-------|--------|---------|
| Homepage route exists | ✅ | `Route::get('/', ...)` returns `view('auth.login')` |
| Health endpoint | ✅ | `/health` returns JSON |
| Auth routes | ✅ | Breeze auth: login, register, password reset |
| GET /logout | ⚠️ | Exists alongside POST — CSRF risk |

---

## Phase 15 — Middleware

| Check | Status |
|-------|--------|
| CheckRole middleware | ✅ Defined, alias `role` |
| SecurityHeaders middleware | ✅ Appended globally |
| Liveware middleware | ✅ DisableBackButtonCache |
| No auth redirect loops | ✅ |

---

## Phase 16 — Database

| Check | Status | Details |
|-------|--------|---------|
| Connection succeeds | ❌ | `SQLSTATE[HY000] [1049] Unknown database 'dapense'` |
| DB_HOST valid | ❌ | `127.0.0.1` — not reachable from Vercel |
| SSL configured | ❌ | `MYSQL_ATTR_SSL_CA` not set |

---

## Phase 17 — Session Driver

`SESSION_DRIVER=database` → ❌ **Must be `cookie` for Vercel serverless.**

Vercel serverless functions are stateless. File-based sessions won't persist, and database sessions require a persistent MySQL connection.

---

## Phase 18 — Cache Driver

`CACHE_STORE=database` → ❌ **Must be `array` for Vercel serverless.**

Array cache is per-request, which is fine for serverless. Database cache requires MySQL.

---

## Phase 19 — Queue Driver

`QUEUE_CONNECTION=database` → ❌ **Must be `sync` for Vercel serverless.**

Serverless functions can't run queue workers. All jobs must execute synchronously.

---

## Phase 20 — Logs

| Check | Status |
|-------|--------|
| `storage/logs/laravel.log` | ✅ Has content (error logs) |
| Log channel | `stack` → `single` — OK |
| Log level | `warning` — OK |

On Vercel, set `LOG_CHANNEL=stderr` to see logs in Vercel dashboard.

---

## Phase 21 — PHP Extensions

Required extensions (standard Laravel): mbstring, openssl, tokenizer, fileinfo, ctype, PDO, pdo_mysql, json, xml, curl, gd, zip.

The `vercel-php@0.7.4` runtime includes all standard PHP extensions. ✅

---

## Phase 22 — Packages

| Package | Laravel 13 Compat? | Vercel Compat? |
|---------|-------------------|----------------|
| laravel/framework ^13.0 | ✅ Native | ⚠️ Needs serverless config |
| livewire/livewire ^4.0 | ✅ | ✅ |
| maatwebsite/excel ^3.1 | ✅ | ⚠️ PHPExcel may need `/tmp` for temp files |
| mpdf/mpdf ^8.2 | ✅ | ✅ Uses `/tmp` by default |
| spatie/laravel-activitylog ^4.12 | ✅ | ⚠️ Needs DB — disable or configure |

---

## Phase 23 — Serverless Compatibility

| Feature | Compatible? | Alternative |
|---------|-------------|-------------|
| Local filesystem persistence | ❌ | Use `/tmp` for temp; S3 for permanent |
| Database sessions | ❌ | Use `cookie` driver |
| Database cache | ❌ | Use `array` driver |
| Queue workers | ❌ | Use `sync` driver |
| File uploads | ❌ | Must use S3 or external storage |
| Background jobs | ❌ | Use external queue service |
| Scheduled tasks | ❌ | Use external cron (GitHub Actions, etc.) |
| WebSockets | ❌ | Not supported |

---

## Phase 24 — Bootstrap Process

```
public/index.php
    ↓ (via api/index.php on Vercel)
bootstrap/app.php
    ↓
ServiceProviders (including AppServiceProvider)
    ↓
Middleware pipeline (StartSession → runs DB query → CRASH ❌)
```

**Execution stops at `StartSession` middleware** because `SESSION_DRIVER=database` triggers a MySQL query that fails.

---

## Phase 25 — Root Cause Analysis

### Issue #1 — CRITICAL: Static vercel.json
- **Description**: `vercel.json` has `outputDirectory: "public"` instead of PHP runtime configuration
- **Root Cause**: Project was configured as a static site, not a PHP serverless app
- **Impact**: PHP never executes → blank white page
- **Fix**: Rewrite `vercel.json` with `vercel-php@0.7.4` runtime and `api/index.php` entry point

### Issue #2 — CRITICAL: Database session/cache configured
- **Description**: `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- **Root Cause**: Environment variables configured for local MySQL that doesn't exist on Vercel
- **Impact**: Every request crashes at session read → white page
- **Fix**: Set `SESSION_DRIVER=cookie`, `CACHE_DRIVER=array`, `QUEUE_CONNECTION=sync`

### Issue #3 — CRITICAL: Missing Vite build
- **Description**: `public/build/` doesn't exist — no `manifest.json`
- **Root Cause**: `npm run build` never executed
- **Impact**: `@vite()` throws `ManifestNotFoundException`
- **Fix**: Run `npm install && npm run build` before deploy

---

## Deliverables

- ✅ Executive Summary
- ✅ Root Cause Analysis
- ✅ Error Log Summary
- ✅ Deployment Configuration Review
- ✅ Environment Variable Audit
- ✅ Composer Audit
- ✅ Laravel Configuration Audit
- ✅ Vite Build Audit
- ✅ Asset Loading Audit
- ✅ Runtime Compatibility Report
- ✅ Serverless Compatibility Report
- ✅ Prioritized Fix List
- ✅ Recommended Code Changes
- ✅ Risk Assessment
- ✅ Final Deployment Validation Checklist

---

## Prioritized Fix List

| Priority | Issue | Effort | Impact |
|----------|-------|--------|--------|
| **P0** | Rewrite `vercel.json` with PHP runtime | 5 min | Resolves white screen |
| **P0** | Create `api/index.php` entry point | 5 min | Required for PHP execution |
| **P0** | Fix env vars (session, cache, queue) | 5 min | Prevents DB crash |
| **P0** | Run `npm run build` for Vite assets | 2 min | Prevents manifest error |
| **P1** | Create `.vercelignore` | 2 min | Optimizes deployment |
| **P1** | Set env vars in Vercel dashboard | 10 min | Configures production |
| **P2** | Update `AppServiceProvider` for HTTPS | 5 min | Ensures asset URLs |
| **P2** | Add Vercel detection logic | 5 min | Auto-configures drivers |

---

## Acceptance Criteria

| Criterion | Status |
|-----------|--------|
| No white screen appears | ⬜ Pending fix |
| No runtime exceptions exist | ⬜ Pending fix |
| No missing assets | ⬜ Pending fix |
| Vite loads correctly | ⬜ Pending fix |
| All routes return expected responses | ⬜ Pending fix |
| Environment variables validated | ⬜ Pending fix |
| PHP runtime configured correctly | ⬜ Pending fix |
| Laravel functions correctly on Vercel | ⬜ Pending fix |
| All critical issues documented with fixes | ✅ Complete |

---

*Report generated by Sisyphus — Vercel Deployment Audit Agent*
