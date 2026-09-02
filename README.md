# DAPENSE — Dana Pensiun Sekolah Kristen Salatiga

> **Pension Fund Accounting System** · *Sistem Akuntansi Dana Pensiun*
> Double-entry, period-scoped, audit-ready ledger for a regulated pension fund — built as a production Laravel monolith.

<p>
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel" alt="Laravel 13" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Livewire-4-4E56A6?style=flat-square" alt="Livewire 4" />
  <img src="https://img.shields.io/badge/MySQL-8.4-4479A1?style=flat-square&logo=mysql" alt="MySQL 8.4" />
  <img src="https://img.shields.io/badge/PostgreSQL-16-336791?style=flat-square&logo=postgresql" alt="PostgreSQL 16" />
  <img src="https://img.shields.io/badge/Tailwind-4.3-06B6D4?style=flat-square&logo=tailwindcss" alt="Tailwind" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="MIT" />
</p>

**Internal name:** WAS Accounting System · **Version:** 1.0 · **License:** MIT · **Currency:** IDR (Rupiah) · **Language:** Indonesian UI (`Jurnal`, `Buku Besar`, `Neraca Saldo`, `Periode Aktif`)

---

## Table of Contents

1. [Identity — What is DAPENSE?](#1-identity--what-is-dapense)
2. [Problem → Solution](#2-problem--solution)
3. [Features](#3-features)
4. [Architecture](#4-architecture)
5. [Tech Stack](#5-tech-stack)
6. [Data Model](#6-data-model)
7. [Roles & Authorization](#7-roles--authorization)
8. [Project Structure](#8-project-structure)
9. [Quick Start](#9-quick-start)
10. [Environment](#10-environment)
11. [Commands](#11-commands)
12. [Testing & Quality](#12-testing--quality)
13. [Deployment](#13-deployment)
14. [Security](#14-security)
15. [Backup, Restore & PostgreSQL Portability](#15-backup-restore--postgresql-portability)
16. [Documentation Map](#16-documentation-map)
17. [Roadmap](#17-roadmap)

---

## 1. Identity — What is DAPENSE?

**DAPENSE** is the operational accounting system for **Dana Pensiun Sekolah Kristen Salatiga** — a pension fund that must maintain a complete, auditable, period-closed double-entry ledger.

It is **not** a generic accounting SaaS. Identity-defining constraints:

| Identity trait | Implication |
|---|---|
| **Regulated fund accounting** | Every entry is period-scoped, voucher-numbered (`nomor_bukti`), and categorized (KM/KK/BM/BK/Mem/MemPenutup). No entry lives outside a `Periode`. |
| **Single source of truth: `jurnalings`** | One DB row **per journal line** (debit *or* kredit). A voucher spans ≥2 rows sharing one `nomor_bukti`; balance is `SUM(debit) == SUM(kredit)` enforced in the application. |
| **Period lifecycle** | `periodes.is_rekap = false` = open period. Dashboard, ledger, and recap all resolve the *active period* as `latest is_rekap=false`. Closing = `rekapJurnal`, reversing = `unrekapJurnal`. |
| **Monolithic, server-rendered** | Blade + Livewire full-page components. No SPA, no external frontend build required to be productive. Optimized for correctness and auditability over cleverness. |
| **Excel/PDF-native reporting** | Every ledger view has a throttled export path (Maatwebsite Excel, mPDF) resolved per-role. |

**One-line pitch:** *The ledger the fund auditors trust — periods, vouchers, and trial balances that actually reconcile.*

---

## 2. Problem → Solution

**Before:** Spreadsheets, manual voucher numbering, no period close, no role separation, exports that don't reconcile.

**DAPENSE solves it with:**

- Structured **Chart of Accounts** (`header_coas` → `coas` with `kode_akun` as business key)
- **Double-entry validation** per voucher, per period, with opening balances (`saldo_awal`) and running totals
- **Trial balance snapshots** (`neraca_saldos`) per period/month — the fund's month-end truth
- **Role-aware UI + authorization** (4 roles, 8 policies, 4 gates, middleware + component guards)
- **Single-command deploy** (Docker) and **serverless fallback** (Vercel), with MySQL 8.4 prod and a first-class **PostgreSQL 16** export path

---

## 3. Features

### Accounting core

- **Chart of Accounts (COA)** — hierarchical headers + accounts, `saldo_normal`/`kategori`/`level`, import/export via COAWorkspace
- **Periode** — `nama_periode`, `tanggal_awal/akhir`, `is_rekap` flag; active period drives every screen
- **Saldo Awal** — opening balances per COA per period (`debit`/`kredit` DECIMAL 15,2)
- **Journal Entry** — 6 voucher categories: Kas Masuk (KM), Kas Keluar (KK), Bank Masuk (BM), Bank Keluar (BK), Memorial, Memorial Penutup — each with `cekNomorBukti*` prefix validation
- **Jurnal List / Manager** — period-filtered, `with('coa')`, 6-row recent activities, editable per-line
- **Buku Besar (General Ledger)** — `COA + Periode + bulan` or date range → opening + running total, missing `keterangan` resolved by `nomor_bukti` group, prepended "Saldo Awal" row
- **Neraca Saldo (Trial Balance)** — per period / per month persisted snapshot, PDF (mPDF) + Excel (Maatwebsite) export, `saldo_awal/debit/kredit/balance`
- **Rekap Jurnal / Posting** — period close & post, throttled (`posting` 10/min), gate `post-journal`
- **Otorisator** — authorizing officers for report sign-offs (`nama_otorisator`, `jabatan_otorisator`)

### Operations

- **Dashboard** — KPIs (COUNT/SUM per active vs previous period, % trends), 6 latest journals, 6-month grouped monthly summary (PHP `Y-m` grouping)
- **Reports** — `BukuBesar`, `LaporanNeraca`, `LaporanArusKas`, `LaporanAsetNeto`, etc. — per-role export class family `app/Export/{Base,admin,bod,operator,rootsuperuser}`
- **Activity Log** — Spatie `activity_log` on `users` mutations (name/email/usertype/status)
- **Users** — CRUD under `manage-users` gate

---

## 4. Architecture

### 4.1 Layered view

```
┌──────────────────────────────────────────────────────────────────┐
│ PRESENTATION                                                       │
│  Blade layouts/views · Blade components (x-dashboard.*, x-*)      │
│  Livewire full-page components (/dashboard, /jurnaling …)          │
│  Tailwind CSS 4 · Alpine.js 3.15 · Lucide (explicit imports)       │
├────────────────────────────────────────────────────────────────────┤
│ HTTP / ROUTING                                                     │
│  routes/web.php (public, Livewire-app, legacy role-prefixed,      │
│  consolidated policy-protected, health) · routes/auth.php         │
├────────────────────────────────────────────────────────────────────┤
│ INPUT GATE / SECURITY CUTS                                         │
│  Middleware: SecurityHeaders(app-wide), auth, verified, role,      │
│  no-cache(bfcache) · Form Requests validation                     │
├────────────────────────────────────────────────────────────────────┤
│ APPLICATION                                                        │
│  Controllers: Base/* · Modules/* · admin/* · rootsuperuser/*      │
│  Livewire Components (12: BukuBesar, COAWorkspace, Dashboard,     │
│    JournalEntry, JurnalList, JurnalManager, NeracaSaldo,          │
│    OtorisatorManager, PeriodeManager, Posting, SaldoAwal,          │
│    UserManager) — role-aware via HasRole                           │
│  Policies (8) · Gates (4) · RateLimiters                          │
├────────────────────────────────────────────────────────────────────┤
│ DOMAIN / PERSISTENCE                                               │
│  Models: User, Periode, HeaderCOA, COA, Jurnaling, SaldoAwal,      │
│  NeracaSaldo, Otorisator · Spatie Activitylog                     │
│  Eloquent + raw SQL aggregates (COUNT/SUM/EXTRACT)                │
├────────────────────────────────────────────────────────────────────┤
│ DATA                                                               │
│  MySQL 8.4 (prod) · PostgreSQL 16 (portable) · SQLite (test)      │
│  18 migrations · seed_data_jurnal_coa.sql (1222 lines)            │
├────────────────────────────────────────────────────────────────────┤
│ INFRASTRUCTURE                                                     │
│  Nginx + PHP-FPM 8.3 + Redis (single container via                 │
│  docker-entrypoint.sh) · mysql:8.4 / postgres:16-alpine           │
│  Railway (DOCKERFILE) · Vercel (vercel-php@0.9.0 serverless)      │
└────────────────────────────────────────────────────────────────────┘
```

### 4.2 Request lifecycle (Dashboard)

```
Browser GET /dashboard
  → Nginx :8080 → PHP-FPM → Laravel
  → Middleware: SecurityHeaders → auth → verified → no-cache
  → Route: Route::get('/dashboard', Dashboard::class)  // Livewire full-page
  → Livewire Dashboard::mount() → boot() checks canAccess('dashboard') → 403 on fail
  → loadDashboardData()
       · periodeAktif   = Periode where is_rekap=false orderBy tanggal_awal desc first
       · KPIs           = Jurnaling COUNT/SUM within active & previous periods
       · trends         = % delta vs previous (null-safe)
       · activities     = 6 latest journals (period-filtered, with coa)
       · monthlySummary = groupBy Y-m in PHP, aggregates + trend per month
  → render views.livewire.dashboard
  → Blade: hero, kpi-card ×4, module-card, activity-list, monthly-summary
```

### 4.3 Deployment topology

| Target | Topology |
|---|---|
| **Docker** (local/prod) | `app` container (Nginx + PHP 8.3-FPM + Redis via `docker-entrypoint.sh`) on `:8080`, `mysql:8.4` or `postgres:16-alpine` sidecar, `dapense-internal` network, volumes `mysql_data`/`pg_data` + `storage_data`; app runs **read_only** root FS with tmpfs for `storage/framework/*`, `bootstrap/cache`, `logs`, `nginx/redis/tmp`. |
| **Railway** | `Dockerfile` + `railway.json`: healthcheck `/health`, restart `ON_FAILURE` ×10 |
| **Vercel** | `vercel-php@0.9.0` serverless; build = `composer install --no-dev --optimize-autoloader` + `npm run build` + `artisan optimize`; `vercel.json` routes `/build/assets/*` → `public/build/assets/*`, rest → `api/index.php`; env forces `CACHE_STORE=array`, `SESSION_DRIVER=cookie`, compiled views → `/tmp` |

Boot hygiene (`docker-entrypoint.sh`): validates env, verifies `vendor/`, fixes `storage` ownership, writes nginx conf to `/tmp` (read-only root), runs `artisan optimize`, boots Redis (256 MB `allkeys-lru`), starts `php-fpm -D`, then `nginx daemon off` as PID 1. Two queue workers via `supervisor.conf` (`queue:work redis`).

---

## 5. Tech Stack

| Layer | Choice | Version | Notes |
|---|---|---|---|
| **Language** | PHP | 8.3 | `composer.json` `php ^8.3` |
| **Framework** | Laravel | 13.x | `laravel/framework ^13.0` |
| **Reactivity** | Livewire | 4.x | Full-page components, `HasRole` trait |
| **Auth scaffolding** | Laravel Breeze | 2.x | Blade + auth routes |
| **Database (prod)** | MySQL | 8.4 | `mysql:8.4` container, InnoDB, FKs enforced |
| **Database (portable)** | PostgreSQL | 16 | `postgres:16-alpine` via `docker-compose.pgsql.yml`, `DB_CONNECTION=pgsql`, driver-aware migrations (`NUMERIC(15,2) USING ::numeric`) |
| **Database (test)** | SQLite | — | `phpunit.xml` in-memory for Pest |
| **Cache / Queue** | Redis | 7 | In-container, `maxmemory 256mb allkeys-lru`, 2 workers; Vercel falls back to `array`/`sync` |
| **Frontend build** | Vite | 8.x | `laravel-vite-plugin ^3.0` |
| **CSS** | Tailwind CSS | 4.3 | `@tailwindcss/vite ^4.3.3` |
| **JS** | Alpine.js, Axios, jQuery, Lucide | 3.15 / 1.18 / 4.0 / 1.25 | Lucide via explicit imports (regression fix) |
| **Excel** | Maatwebsite Excel | 3.1 | Per-role `app/Export/**` family |
| **PDF** | mPDF | 8.2 | Trial balance & ledger PDFs |
| **Auditing** | spatie/laravel-activitylog | 4.12 | `activity_log` on `users` |
| **Testing** | Pest | 4.x | `pestphp/pest-plugin-laravel` |
| **Static analysis** | Larastan (PHPStan) | 3.x | `phpstan.neon` |
| **Style** | Laravel Pint | 1.13 | PSR-12 |
| **Dev** | Debugbar, Faker, Collision, Sail, Mockery | — | `require-dev` |

**Scripts** (`composer.json` / `package.json`): `composer check` = `lint + analyse + test` · `npm run dev` / `npm run build` (Vite)

---

## 6. Data Model

> Full 636-line spec: [`documentation.md`](./documentation.md). Summary below.

### 6.1 Table inventory (18 migrations)

| Table | Purpose | Key columns |
|---|---|---|
| `users` | App users | `name`, `email` UQ, `usertype` string default `rootsuperuser`, `status` int (=1 active), `password`, `image` nullable |
| `header_coas` | COA group hierarchy | `kode_header`, `nama_header`, `level`, `parent_id` self-FK |
| `coas` | Chart of Accounts | `kode_akun` UQ + UQ(`kode_akun`,`nama_akun`), `saldo_normal`, `kategori`, `level`, `header_coa_id` FK |
| `periodes` | Accounting periods | `nama_periode`, `tanggal_awal`, `tanggal_akhir`, `is_rekap` bool |
| `jurnalings` | Journal **lines** | `tanggal_jurnal` date, `nomor_bukti` string, `keterangan` max 500, `kategori_jurnal` (KM/KK/BM/BK/Mem/MemPenutup), `debit`/`kredit` NUMERIC(15,2) DEFAULT 0, `coa_id` FK, `periode_id` FK |
| `saldo_awal` | Opening balances | `coa_id` FK, `tanggal_saldo` date, `debit`/`kredit` DECIMAL(15,2), `periode_id` FK |
| `neraca_saldos` | Trial balance snapshot | `coa_id` **string FK → `coas.kode_akun`** (deliberate), `periode_id` FK, `month` date, `saldo_awal`/`debit`/`kredit`/`balance` |
| `otorisators` | Authorizing officers | `nama_otorisator`, `jabatan_otorisator` |
| `products` | Demo CRUD module | — |
| `activity_log` | Spatie audit log | `log_name`, `description`, `subject/causer` polymorphic, `properties` JSON, `event`, `batch_uuid` |
| `jobs` / `job_batches` / `failed_jobs` | Queue | standard |
| `cache` / `cache_locks` | Cache | standard |
| `sessions` / `password_reset_tokens` | Auth | standard |

### 6.2 ER sketch

```
HeaderCOA ──< COA ──< Jurnaling >── Periode
  (self)        │         │
                ├─< SaldoAwal ─┘
                └─< NeracaSaldo (via kode_akun string FK)
```

> **Design note:** `neraca_saldos.coa_id` is a **string FK to `coas.kode_akun`**, not `coas.id`. The model uses `belongsTo(COA::class, 'coa_id', 'kode_akun')` — plain `where('coa_id', $coa->id)` will miss rows.

### 6.3 Key constraints & evolution

- 8 FKs total (including string FK + self-FK on `header_coas.parent_id`). Hard deletes with `CASCADE` where present.
- `jurnalings.debit/kredit` migrated `VARCHAR → NUMERIC(15,2) NOT NULL DEFAULT 0` (`2026_08_03`, driver-aware: `USING ::numeric` on PG). `down()` returns to `VARCHAR(255)`.
- `2024_09_06_add_kategori_jurnal…` is an **empty no-op stub** — DDL already contains `kategori_jurnal` (safe to keep/delete).
- Seed: `database/seed_data_jurnal_coa.sql` — 17 `header_coas`, 100 `coas`, 54 `saldo_awal`, ~1000 `jurnalings` (standard `INSERT` — MySQL + PostgreSQL compatible).

---

## 7. Roles & Authorization

`users.usertype` = `rootsuperuser` (default) · `admin` · `operator` · `bod`

| Feature | rootsuperuser | admin | operator | bod | Enforcement |
|---|---|---|---|---|---|
| dashboard | ✓ | ✓ | ✓ | ✓ | `HasRole::canAccess` |
| master-data / transactions / jurnal-entry / jurnaling / saldo-awal / otorisator | ✓ | ✓ | ✓ | — | `HasRole` |
| reports / finance / bukubesar / neraca-saldo | ✓ | ✓ | ✓ | ✓ | `HasRole` |
| administration / settings | ✓ | ✓ | — | — | `HasRole` |
| posting | ✓ | ✓ | — | — | `HasRole` + Gate `post-journal` + `role` middleware |
| users | ✓ | ✓ | — | — | Gate `manage-users` |
| export-journal | ✓ | ✓ | ✓ | ✓ | Gate `export-journal` |
| import-data | ✓ | ✓ | ✓ | — | Gate `import-data` |

**Enforcement points:** `CheckRole` middleware (`role` alias) · `HasRole` trait (`canAccess` + `routePrefix`, `abort_unless(...,403)` in every Livewire `boot()`) · 8 Policies (`Journal`, `User`, `Ledger`, `Periode`, `SaldoAwal`, `Otorisator` …) · 4 Gates (`export-journal`, `import-data`, `post-journal`, `manage-users`) in `AuthServiceProvider`. **Nuance:** operator can view ledger but not post; BOD is read-only.

---

## 8. Project Structure

```
Dapense/
├── app/
│   ├── Export/{Base,admin,bod,operator,rootsuperuser}/  # per-role Excel exports
│   ├── Http/{Controllers/{Base,Modules,admin,rootsuperuser},Middleware,Requests}
│   ├── Livewire/  # BukuBesar, COAWorkspace, Dashboard, JournalEntry, JurnalList,
│   │              # JurnalManager, NeracaSaldo, OtorisatorManager, PeriodeManager,
│   │              # Posting, SaldoAwal, UserManager (+ HasRole trait)
│   ├── Models/    # User, COA, HeaderCOA, Jurnaling, Periode, SaldoAwal, NeracaSaldo, Otorisator
│   └── Policies/  # 8 policies
├── config/        # database (mysql + pgsql), activitylog, auth, cache, queue, …
├── database/
│   ├── migrations/        # 18 migrations (incl. driver-aware NUMERIC conversion)
│   ├── pgsql-dump/        # generated PG export (full.sql, 01_schema.sql)
│   ├── pgsql-export.sh    # offline/online dump generator
│   └── seed_data_jurnal_coa.sql  # 1222-line seed (PG-compatible)
├── docker/
│   ├── backup.sh / backup-pg.sh      # mysqldump / pg_dump + gzip + AES-256-CBC
│   ├── restore.sh / restore-pg.sh    # mysql / psql restore
│   ├── Dockerfile, nginx.conf, supervisord.conf, docker-entrypoint.sh
│   └── backup.sh handles both engines
├── docs/
│   ├── system-design.md       # 395-line detailed design spec (source of truth)
│   └── postgres-migration.md  # MySQL → PG migration, backup/restore, rollback
├── documentation.md           # 636-line DB spec (ER, matrices, per-table DDL, FK catalogue)
├── resources/
│   ├── views/{livewire,admin,operator,bod,rootsuperuser,components,layouts}
│   ├── css/app.css  # Tailwind 4
│   └── js/app.js    # Alpine, Lucide, Axios, jQuery
├── routes/{web.php,auth.php}
├── docker-compose.yml         # app + mysql:8.4 (8080)
├── docker-compose.pgsql.yml   # postgres:16-alpine (15432→5432, pg_isready)
├── vercel.json / railway.json / api/index.php
├── phpstan.neon / pint.json / vite.config.js / tailwind.config (via vite plugin)
└── README.md (this file)
```

---

## 9. Quick Start

### Prereqs

PHP 8.3+, Composer, Node 20+, Docker (optional), MySQL 8.4 or PostgreSQL 16.

### A. Host dev (recommended for iteration)

```bash
git clone https://github.com/<org>/Dapense.git && cd Dapense
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed   # or: mysql < database/seed_data_jurnal_coa.sql
npm run dev                  # Vite HMR
php artisan serve            # http://localhost:8000
```

`.env` keeps `DB_HOST=127.0.0.1` + `DB_PORT=13306` when MySQL runs via `docker compose up` (host-mapped), or `3306` for native MySQL. `DEMO_ENABLED=false` by default — `/demo-login` only works when `APP_ENV=local` or `DEMO_ENABLED=true` (throttle `6,1`).

> If you see `getaddrinfo for dapense-mysql failed`, your host `.env` has the container-only host — switch to `127.0.0.1`.

### B. Docker (MySQL)

```bash
docker compose up --build    # app at http://localhost:8080 (DB_HOST=dapense-mysql inside)
docker compose exec app php artisan migrate --seed
```

Compose overrides `DB_HOST` to `dapense-mysql` inside `dapense-internal`; host keeps `127.0.0.1`.

### C. Docker (PostgreSQL)

```bash
docker compose -f docker-compose.pgsql.yml up --build   # postgres:16-alpine on 15432
# .env: DB_CONNECTION=pgsql, DB_HOST=127.0.0.1 (host) / dapense-postgres (container), DB_PORT=5432/15432
DB_CONNECTION=pgsql php artisan migrate --seed
# or: psql $DATABASE_URL < database/seed_data_jurnal_coa.sql
```

Offline PG artifact (no DB required):

```bash
bash database/pgsql-export.sh --offline   # writes database/pgsql-dump/full.sql
```

See [`docs/postgres-migration.md`](./docs/postgres-migration.md) for pgloader / mysqldump → PG migration.

---

## 10. Environment

Key `.env` keys (see `.env.example` — PG block documented there):

```
APP_ENV=local|production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=http://localhost:8080

DB_CONNECTION=mysql|pgsql
DB_HOST=127.0.0.1        # host dev; container override is dapense-mysql / dapense-postgres
DB_PORT=13306            # 3306 native MySQL, 5432 PG, 15432 PG via docker-compose.pgsql.yml
DB_DATABASE=dapense
DB_USERNAME=root
DB_PASSWORD=...

DEMO_ENABLED=false       # enables /demo-login (throttle 6/min) — keep false in prod
CACHE_STORE=redis|array  # array on Vercel
SESSION_DRIVER=redis|cookie
QUEUE_CONNECTION=redis|sync
REDIS_HOST=127.0.0.1
```

---

## 11. Commands

```bash
composer check        # lint + analyse + test
composer lint         # Pint --test
composer lint-fix     # Pint fix
composer analyse      # Larastan
composer test         # Pest

npm run dev           # Vite dev
npm run build         # Vite build (also run by Dockerfile + Vercel)

php artisan migrate               # 18 migrations
php artisan migrate:fresh --seed  # reset + seed
php artisan tinker

# PG export
bash database/pgsql-export.sh              # online (needs DB)
bash database/pgsql-export.sh --offline    # offline artifact only

# Backup / restore (AES-256-CBC + gzip)
bash docker/backup.sh          # MySQL  → docker/backups/
bash docker/backup-pg.sh       # PostgreSQL
bash docker/restore.sh <file>  # MySQL
bash docker/restore-pg.sh <file>

# Format / build gates (CI parity)
gofmt -w . 2>/dev/null; go vet ./... 2>/dev/null  # no Go in this repo — PHP gates above
```

---

## 12. Testing & Quality

- **Pest 4** (`tests/`): `AuthenticationTest`, `EmailVerificationTest`, `AuthorizationTest`, `DashboardTotalsTest` (SUM regressions), `HttpTest`, `ModuleTest`, `UiStateTest`, `UiConsistencyTest`, `ValidationTest`, Profile/Password.
- **Pint** (PSR-12) + **Larastan/PHPStan** (`phpstan.neon`) + `composer check`.
- Factories per model · `JurnalCoaSeeder` (1000 rows) + `UsersTableSeeder` · `phpunit.xml` SQLite in-memory for CI.
- Validation: `StoreJurnalingRequest` — `tanggal_jurnal` required|date · `nomor_bukti` required max 50 · `keterangan` required max 500 · `coa_id` array each `exists:coas,id` · `debit.*`/`kredit.*` required array `numeric|min:0` · `periode_id` exists. Balance (`SUM(debit)==SUM(kredit)`) is application-enforced (no DB cross-column constraint).

---

## 13. Deployment

| Target | How |
|---|---|
| **Docker** | `docker compose up --build` (MySQL) or `docker compose -f docker-compose.pgsql.yml up --build` (PG). Healthcheck `/health` + `/up`. Read-only root, `no-new-privileges`, `cap_drop: ALL`. |
| **Railway** | Push `main` → Dockerfile build, `railway.json` healthcheck + restart `ON_FAILURE ×10`. |
| **Vercel** | `vercel --prod` — `vercel-php@0.9.0`, `npm run build` + `composer install --no-dev --optimize-autoloader` + `artisan optimize` at build. |
| **Host** | `composer install --no-dev --optimize-autoloader && npm run build && php artisan optimize && php artisan migrate --force` behind Nginx. |

---

## 14. Security

| Control | Implementation |
|---|---|
| Transport | `URL::forceScheme('https')` in production outside Docker; Vercel always HTTPS |
| Headers (global) | `SecurityHeaders` middleware: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 0`, `Referrer-Policy`, `Permissions-Policy`, full **CSP** (`default-src 'self'`, `script-src 'unsafe-inline' 'unsafe-eval'` for Livewire/Alpine, `frame-src 'none'`, `object-src 'none'`) |
| bfcache | `PreventBfcache` on `/dashboard` (`Cache-Control: no-cache, no-store…`) |
| Anti-abuse | Rate limiters: `export 3/min`, `import 2/min`, `posting 10/min` (user/IP), `demo-login 6/min` |
| CSRF | Laravel default on all web POST routes |
| Container | `read_only: true`, `cap_drop: ALL` + minimal `cap_add`, `no-new-privileges`, tmpfs writable dirs |
| Secrets | Env-driven; `APP_KEY` + DB creds validated at boot (`docker-entrypoint.sh`) |
| Auditing | Spatie Activitylog on `users` |

---

## 15. Backup, Restore & PostgreSQL Portability

| Engine | Backup | Restore |
|---|---|---|
| MySQL | `bash docker/backup.sh` — `mysqldump` + `gzip` + `openssl enc -aes-256-cbc` → `docker/backups/` | `bash docker/restore.sh <file>` — `mysql < dump` |
| PostgreSQL | `bash docker/backup-pg.sh` — `pg_dump` + `gzip` + `AES-256-CBC` | `bash docker/restore-pg.sh <file>` — `psql` |

**PostgreSQL export:** `database/pgsql-export.sh` generates `database/pgsql-dump/full.sql` (1232 lines). The single migration that was MySQL-only (`2026_08_03` `MODIFY COLUMN`) is now **driver-aware** (`ALTER COLUMN TYPE NUMERIC(15,2) USING ::numeric` on PG). `config/database.php` already ships a `pgsql` connection; `seed_data_jurnal_coa.sql` is plain `INSERT` (PG-compatible).

Full MySQL→PG migration via **pgloader** or `mysqldump --compatible=postgresql` is documented in [`docs/postgres-migration.md`](./docs/postgres-migration.md) (diff table, fresh-PG quick start, rollback). Verify with `bash -n docker/*.sh && php -l database/pgsql-export.sh && yamllint docker-compose*.yml`.

---

## 16. Documentation Map

| Doc | What it is |
|---|---|
| [`documentation.md`](./documentation.md) | 636-line DB spec — ER (Mermaid), relationship matrix, per-table columns (PK/FK/UQ/IDX), FK catalogue (8 FKs), indexes, schema evolution, model map, seed & business rules |
| [`docs/system-design.md`](./docs/system-design.md) | 395-line system design — architecture, deployment, RBAC matrix, data design, domain logic (active period, KPIs, ledger, trial balance), route map, security, performance budgets, gaps |
| [`docs/postgres-migration.md`](./docs/postgres-migration.md) | MySQL→PostgreSQL migration, dual-engine backup/restore, export artifact, compat notes |
| `database/pgsql-dump/full.sql` | Generated PG dump (offline artifact) |
| This `README.md` | Startup identity + system design + tech stack + operational guide |

---

## 17. Roadmap

**Known gaps** (from `docs/system-design.md` §11 — no fix in this pass, tracked as design items):

1. Extract `DashboardService` — `Livewire\Dashboard::loadDashboardData()` vs `HomeController::getDashboardData()` are ~95% duplicated.
2. Monthly summary is in-memory `groupBy Y-m`; move to SQL `GROUP BY EXTRACT` if a period exceeds ~20k rows.
3. Legacy `admin|operator|bod|rootsuperuser/*` view trees shadow Livewire — deprecate after Livewire coverage completes.
4. `kategori_jurnal` no-op migration — fold or delete.
5. Add `jurnalings.created_by` FK if per-user reporting is needed.
6. Re-verify BOD read-only after any new BOD menu.
7. Keep `neraca_saldos` string FK mapping documented.

**Next:** per-period voucher uniqueness policy, richer `activity_log` coverage (journal mutations), composite index on `jurnalings(periode_id, tanggal_jurnal)`.

---

## License

MIT — see [LICENSE](LICENSE). Laravel framework portions remain MIT per upstream.

## Contributing

PRs welcome. Run `composer check` + `npm run build` before pushing. See [Laravel contribution guide](https://laravel.com/docs/contributions) for framework conventions.

---

<p align="center"><sub>Built for the fund, not the demo — <strong>DAPENSE</strong> · Dana Pensiun Sekolah Kristen Salatiga</sub></p>
