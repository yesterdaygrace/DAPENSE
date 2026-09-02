# DAPENSE — System Design Specification

| Field | Value |
|---|---|
| **System** | DAPENSE — *Dana Pensiun Sekolah Kristen Salatiga* (Pension Fund Accounting System) |
| **Internal name** | WAS Accounting System |
| **Version** | 1.0 |
| **Status** | Approved — derived from source of truth (`main` @ `b43f656`) |
| **Author** | Engineering (system analysis from production codebase) |
| **Last reviewed** | 2026-08-09 |

---

## 1. Purpose

This document specifies the **detailed system design** of the DAPENSE application as
implemented in the repository, so that any engineer can operate, extend, or audit the
system without re-deriving the design from code. Every statement below is grounded in
the actual source (controllers, Livewire components, models, migrations, routes,
middleware, Docker/deploy config).

---

## 2. System Overview

DAPENSE is a web-based double-entry accounting system for a school pension fund. It
manages accounting periods, a Chart of Accounts (COA), journal entries, ledger
(buku besar), trial balance (neraca saldo), opening balances (saldo awal), journal
recap (rekap jurnal), posting, and authorizing officers (otorisator), with role-based
access and Excel/PDF export. UI copy is Indonesian (`Jurnal`, `Buku Besar`,
`Neraca Saldo`, `Periode Aktif`). Currency is Indonesian Rupiah.

The system is a **monolithic Laravel application** with:

- Server-rendered Blade views + Laravel Livewire full-page reactive components.
- A single primary source model `jurnalings` — one row **per journal line** (debit or
  kredit), grouped by `nomor_bukti` (voucher number).
- MySQL (or SQLite/PostgreSQL for dev) persistence via Eloquent.
- Three deployment targets: local Docker, Railway, and Vercel (serverless).

---

## 3. Architecture

### 3.1 Layered Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│ PRESENTATION                                                       │
│  Blade layouts/ views · Blade components (x-dashboard.*, x-*)      │
│  Livewire full-page components (wire: /dashboard, /jurnaling …)    │
│  Tailwind CSS 4 · Alpine.js · Lucide icons (JS-injected)           │
├────────────────────────────────────────────────────────────────────┤
│ HTTP / ROUTING                                                     │
│  routes/web.php (public, Livewire-app, legacy role-prefixed,      │
│  consolidated policy-protected, health) · routes/auth.php         │
├────────────────────────────────────────────────────────────────────┤
│ INPUT GATE / SECURITY CUTS                                         │
│  Middleware: SecurityHeaders(app-wide), auth, verified, role,      │
│  no-cache(bfcache) · Form Requests validation                     │
├────────────────────────────────────────────────────────────────────┤
│ APPLICATION / ABSOLUTE                                             │
│  Controllers:  Base\* · Modules\* · admin\* · rootsuperuser\*      │
│  Livewire Components (role-aware via HasRole)                      │
│  Policies (resource) · Gates (feature) · RateLimiter              │
├────────────────────────────────────────────────────────────────────┤
│ DOMAIN / PERSISTENCE                                              │
│  Models: User, Periode, HeaderCOA, COA, Jurnaling, SaldoAwal,      │
│  NeracaSaldo, Otorisator · Spatie Activitylog                     │
│  Eloquent builders + raw SQL aggregates (COUNT/SUM/EXTRACT)       │
├────────────────────────────────────────────────────────────────────┤
│ DATA                                                               │
│  MySQL (prod) · SQLite/PostgreSQL (alt) · migrations · seeders     │
├────────────────────────────────────────────────────────────────────┤
│ INFRASTRUCTURE                                                     │
│  Nginx + PHP-FPM + Redis (single Docker container) · MySQL:8.4    │
│  Railway (DOCKERFILE) · Vercel (vercel-php serverless)           │
└────────────────────────────────────────────────────────────────────┘
```

### 3.2 Request Lifecycle (Dashboard example)

```
Browser GET /dashboard
  → Nginx (port 8080) → PHP-FPM → Laravel
  → Middleware chain: SecurityHeaders → auth → verified → no-cache
  → Route: Route::get('/dashboard', Dashboard::class)   // Livewire full-page
  → Livewire Dashboard::mount() → boot() checks canAccess('dashboard') → abort 403
  → loadDashboardData()
       · periodeAktif   = Periode where is_rekap=false, latest tanggal_awal
       · KPIs           = Jurnaling COUNT/SUM within active&previous periods
       · trends         = % delta vs previous period (null-safe)
       · activities     = 6 latest jurnals (period-filtered, with coa)
       · monthlySummary = groupBy 'Y-m' in PHP, aggregates + trend
  → render views.livewire.dashboard
  → Blade components: hero, kpi-card ×4, module-card, activity-list, monthly-summary
```

---

## 3.3 Runtime Deployment

| Target | Topology |
|---|---|
| **Docker** | `app` container (Nginx + PHP 8.3-FPM + Redis in one process tree via `docker-entrypoint.sh`) on `:8080`, `mysql:8.4` sidecar, `dapense-internal` network, volumes `mysql_data` + `storage_data`; app runs **read-only** root FS with tmpfs for `storage/framework/*`, sessions, logs, nginx/redis tmp. |
| **Railway** | Dockerfile + `railway.json`: healthcheck `/health`, restart ON_FAILURE ×10. |
| **Vercel** | `vercel-php@0.9.0` serverless; build = composer + `npm run build` + `artisan optimize`; routes `/build/assets/*` → `public/build/assets/*`, everything else → `api/index.php`; env pindah cache/session to `array`/`sync`, compiled views → `/tmp`. |

Boot hygiene: `docker-entrypoint.sh` validates env vars, verifies vendor, sets
`storage` ownership, writes nginx conf to `/tmp` (read-only root), `artisan optimize`,
boots Redis (maxmemory 256MB, allkeys-lru), starts `php-fpm -D`, then `nginx
daemon off` as PID 1. Two queue workers defined in `supervisor.conf` (`queue:work
redis`).

---

## 3.4. Frontend (assets)

- **Vite 8** + `laravel-vite-plugin` + `@tailwindcss/vite`; inputs `resources/css/app.css`,
  `resources/js/app.js`.
- Tailwind CSS 4, Alpine.js 3.15, Axios, jQuery, **Lucide** icons (explicit imports —
  regression fix from an earlier icon issue).
- Views: layouts (`app`, `applayout`, `guest`, `navigation`), role view
  hierarchy (`admin/` `operator/` `rootsuperuser/`) plus Livewire views
  (`livewire/*`), modules index pages, auth pages, component library
  (`components/*` + `components/dashboard/*`).

---

## 4. Roles & Authorization (RBAC)

**roles = `users.usertype`**: `rootsuperuser` (default), `admin`, `operator`, `bod`.

| Feature | rootsuperuser | admin | operator | bod | Enforcement |
|---|---|---|---|---|---|
| dashboard | ✓ | ✓ | ✓ | ✓ | HasRole::canAccess |
| master-data | ✓ | ✓ | ✓ | — | HasRole |
| transactions / jurnal-entry / jurnaling / saldo-awal / otorisator | ✓ | ✓ | ✓ | — | HasRole |
| reports | ✓ | ✓ | ✓ | ✓ | HasRole |
| finance | ✓ | ✓ | ✓ | ✓ | HasRole |
| administration | ✓ | ✓ | — | — | HasRole |
| settings | ✓ | ✓ | ✓ | — | HasRole |
| bukubesar / neraca-saldo | ✓ | ✓ | ✓ | ✓ | HasRole |
| posting | ✓ | ✓ | — | — | HasRole + Gate `post-journal` + Policy role middleware |
| users | ✓ | ✓ | — | — | HasRole + Gate `manage-users` |
| export-journal | ✓ | ✓ | ✓ | wait ✓ | Gate `export-journal` |
| import-data | ✓ | ✓ | ✓ | — | Gate `import-data` |

Enforcement points, all present:

1. **`CheckRole` middleware** (`role` alias) — redirects when `usertype` mismatch
   (used on legacy `rootsuperuser/*` and consolidated role routes).
2. **`HasRole` trait** — `canAccess(feature)` + `routePrefix()` used by every
   Livewire component (`abort_unless(..., 403)` in `boot`).
3. **Policies** (7): `Journal, User, Ledger, Periode, SaldoAwal, Otorisator` +
   feature **Gates** (`export-journal`, `import-data`, `post-journal`, `manage-users`).
4. **Nuance**: operator can view ledger content but cannot **post**; BOD is
   read-only (no master data/posting).

---

## 5. Data Design (Database)

### 5.1. Physical model summary

| Table | Purpose | Key columns / constraints |
|---|---|---|
| `users` | App users | `name`, `email` **UQ**, `usertype` string (default `rootsuperuser`), `status` int (=1 active), `password`, `remember_token`, `image` nullable |
| `header_coas` | COA group hierarchy | `kode_header`, `nama_header`, `level`, `parent_id` self-FK |
| `coas` | Chart of Accounts | `kode_akun` **UQ** + UQ(`kode_akun`,`nama_akun`), `nama_akun`, `saldo_normal`, `kategori`, `level`, `header_coa_id` FK |
| `periodes` | Accounting periods | `nama_periode`, `tanggal_awal`, `tanggal_akhir`, `is_rekap` bool (open period = false) |
| `jurnalings` | Journal **lines** | `tanggal_jurnal` date, `nomor_bukti` string, `keterangan`, `kategori_jurnal` (KM/KK/BM/BK/Mem/MemPenutup), `debit`/`kredit` NUMERIC(15,2) DEFAULT 0, `coa_id` FK, `periode_id` FK |
| `saldo_awal` | Opening balances | `coa_id` FK, `tanggal_saldo` date, `debit`/`kredit` DECIMAL(15,2), `periode_id` FK |
| `neraca_saldos` | Trial balance snapshot | `coa_id` **string FK → coas.kode_akun** (deliberate), `periode_id` FK, `month` date, `saldo_awal`/`debit`/`kredit`/`balance` DECIMAL(15,2) |
| `otorisators` | Authorizing officers | `nama_otorisator`, `jabatan_otorisator` (report sign-offs) |
| `products` | Demo CRUD module | fixed module |
| `activity_log` (+ `event`/`batch_uuid`) | Spatie audit log | logs user profile mutations |
| `jobs`, `cache`, `sessions`, `password_reset_tokens` | Framework | standard |

### 5.2. Relationships (models)

```
User        1——*  Jurnaling (implicit via created_at söylenir — no FK in DDL)
HeaderCOA   1——*  COA            (children via parent_id self)
COA          1——* Jurnaling; 1——* SaldoAwal; 1——* NeracaSaldo (by kode_akun)
Periode      1——* Jurnaling; 1——* SaldoAwal; 1——* NeracaSaldo
Jurnaling    *——1 COA · *——1 Periode   — Eloquent `belongsTo` (no `user` FK)
NeracaSaldo  *——1 COA (`kode_akun`) · *——1 Periode
```

> **Design note (neraca_saldos)**: `coa_id` is a **string FK to `coas.kode_akun`**,
> unlike other tables which FK to integer `coas.id`. The model mirrors this via
> `belongsTo(COA::class, 'coa_id', 'kode_akun')`. Any join must respect this
> mapping; a plain `where('coa_id', $coa->id)` will miss rows.

### 5.3. Schema history relevant to design

- Original DDL stored `jurnalings.debit/kredit` as `VARCHAR`; a `2026-08-03`
  migration converts to `NUMERIC(15,2) NOT NULL DEFAULT 0` (raw `DB::statement`,
  `down()` returns to `VARCHAR(255)`).
- Migration `2024_09_06_add_kategori_jurnal…` is an **empty stub** (no-op) — the
  DDL of `2024_08_02_072943_jurnalings` already contains `kategori_jurnal`. Safe to
  treat as dead code.
- `2026_07`/`2026_08` add + later drop a `unique(nomor_bukti)` per period — captures the
  moment the system moved to per-period voucher uniqueness and back.

---

## 6. Domain Logic Specifications

### 6.1 Active period resolution

Used by dashboard, ledger filters, exports:

```
periodeAktif = Periode::where('is_rekap', false)
                     ->orderBy('tanggal_awal', 'desc')
                     ->first();          // most recent OPEN period
periodeSebelum = Periode::where('tanggal_akhir', '<', aktif.tanggal_awal)
                        ->orderBy('tanggal_awal','desc')->first();
```

**Guard**: if `periodeAktif == null` → KPIs default to `0`, activities [] —
dashboard shows `—`. No period → empty dashboard (observed behavior in the PRD).

### 6.2 Dashboard KPIs & trends

Per active & previous period:

```
total_entries = COUNT(*)        total_debit = COALESCE(SUM(debit),0)
total_kredit  = COALESCE(SUM(kredit),0)
filter: tanggal_jurnal BETWEEN [aktif.tanggal_awal, aktif.tanggal_akhir]
trend X = prev>0 && cur≠prev ? round((cur-prev)/prev*100,1) : null
```

Recent activities: 6 newest (by `created_at` desc), `with('coa')`, period-filtered.
Monthly summary: fetch period rows (`tanggal_jurnal, debit, kredit`), group in PHP
by `Y-m` (`Carbon::parse()->format('Y-m')`), last 6 months asc, trend per month vs
previous month in the same window.

> **Performance**: monthly summary pulls all period rows into memory. Acceptable
> at record volumes; see §10 gaps for scale-up variant.

### 6.2 Journaling (double-entry, single-line-per-row)

Voucher categories (driving separate screens & store routes):

| Id | Route / action | Nomor bukti prefix check |
|---|---|---|
| Kas Masuk | `store` / `index` | `cekNomorBuktiKM` |
| Kas Keluar | `storekaskeluar` | `cekNomorBuktiKK` |
| Bank Masuk | `storebankmasuk` | `cekNomorBuktiBM` |
| Bank Keluar | `storebankkeluar` | `cekNomorBuktiBK` |
| Memorial | `storememorial` | `cekNomorBuktiMem` |
| Memorial Penutup | `storememorialpenutup` | `cekNomorBuktiMemPenutup` |

Validation (`StoreJurnalingRequest`):
`tanggal_jurnal` required|date · `nomor_bukti` required max50 ·
`keterangan` required max500 · `coa_id` required **array** each `exists:coas,id` ·
`debit.*` / `kredit.*` required array, `numeric|min:0` · `periode_id` exists.

The controller and Livewire `JournalEntry` create one DB row per account line
(fallback: zero stays in the line over multiple detail rows), all sharing one
`nomor_bukti`; balances rely on consistent `SUM(debit)==SUM(kredit)` enforced by
**application validation** — the DB has no cross-column constraint. `updatekm`
family edits only per `id`. Recap (`rekapJurnal`) marks the period; `unrekapJurnal`
reverses.

### 6.3 Ledger (Buku Besar)

Input: `COA + Periode + bulan` or date range. Computation (server-side, PostgreSQL-
safe since a fix sweep):

```
saldoAwalByPeriod = SaldoAwal( coa, periode, tanggal_saldo <= start ) SUM(debit-kredit)
saldoAwalTrans   = Jurnaling(coa,periode, tanggal < start)     SUM(debit-kredit)
saldoAwal        = saldoAwalByPeriod + saldoAwalTrans
entry: running_total = running_total + (detail.debit - detail.kredit)
missing keterangan: group by nomor_bukti, pick any non-empty row's text (keteranganGabungan)
shape rows: tanggal_jurnal, nomor_bukti, keterangan, debit, kredit, running_total
initial entry “Saldo Awal” (running_total = saldoAwal) prepended when applicable
```

### 6.4 Trial balance (Neraca saldo) & rekap

- Per period (optionally per month), persisted snapshot in `neraca_saldos`
  (`saldo_awal`, `debit`, `kredit`, `balance`); built by `NERACA` Livewire
  component + `NeracaSaldoController` (recap / months / stampil hierarchy).
- PDF export via mpdf; Excel via Maatwebsite Excel.

### 6.5 Exports / reports

Per-role export class family `app/Export/{Base,admin,bod,operator,rootsuperuser}/…`
(BukuBesar, JurnalingSheet, LaporanNeraca, LaporanArusKas, LaporanAsetNeto,
LaporanPerhitunganHasilUsaha, LaporanPerubahanAsetNeto, LaporanAnalisaLikuiditas,
LaporanInvestasi, LaporanBulanHasilInvestasi, NeracaSaldoBulanan…) — dynamic
`bukuBesarExportClass()` resolves the correct class by role.
All export routes carry `throttle:export`.

---

## 7. Route Map (HTTP contract)

| Area | Routes (prefix) | Notes |
|---|---|---|
| Public | `/`, `/health`, `/up` | health JSON |
| Auth | `/login` … Breeze set + custom `GET /logout`. `/demo-login` creates/logs in a demo `rootsuperuser` (bypasses email verification — **demo only**) |
| Livewire app (`auth`+`verified`) | `/dashboard /activity /master-data /co-coa-workspace(+export/import/template) /transactions /jurnal-entry(+store) /jurnaling /jurnaling-list /jurnaling/export /reports /bukubesar(+export) /neraca-saldo/{periode?} (+exportexcel/exportpdf) /finance /saldo-awal /periodes /otorisator /administration /users /posting(+post,throttle) /settings` | role-agnostic paths; roles checked in-component |
| Legacy `rootsuperuser/*` (`role:rootsuperuser`) | full CRUD: coas, headers, periodes, all journal categories + rekap/unrekap, ledger, ledger views, posting, official CRUD, exports, account pages | Back-compat; duplicate of Livewire UI |
| Consolidated | `/admin/dashboard` `/operator/dashboard` `/bod/dashboard` `/products[*]` (`role:admin`) | Policy + role middleware |

---

## 8. Security Design

| Control | Implementation |
|---|---|
| Transport | HTTPS forced in production outside Docker (`URL::forceScheme`) — required by Vercel |
| Security headers (applobal) | `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 0`, `Referrer-Policy`, `Permissions-Policy`, full **CSP** (`default-src 'self'`, `script-src 'unsafe-inline' 'unsafe-eval'` for Livewire/Alpine, `frame-src 'none'`, `object-src 'none'`) |
| bfcache | `PreventBfcache` on `/dashboard` (`Cache-Control: no-cache, no-store…`) to avoid Livewire stale-DOM rehydrates on back-nav |
| Anti-abuse | Rate limiters: `export` 3/min, `import` 2/min, `posting` 10/min — keyed by user id / IP |
| CSRF | Laravel default for all web forms + POST routes |
| Container | `read_only: true`, `cap_drop: ALL`, add only `NET_BIND_SERVICE/SETGID/SETUID/CHOWN/DAC_OVERRIDE`, `no-new-privileges`, tmpfs writable dirs |
| Secrets | env-driven; `APP_KEY`, DB creds required at boot; Vercel env block for immutable caches |
| Auditing | Spatie Activitylog on `users` only (name/email/usertype/status changes) |

---

## 9. Performance Budgets & Optimization

- Dashboard target **< 500 ms**; SQL **≤ 15 queries** with eager loading
  (`with('coas')`) — current path measures well below.
- Framework caches: `php artisan optimize` (config, routes, views, events) in
  Docker build + at container start, and in `composer run vercel`.
- Redis: cache/session backend with maxmemory 256MB policy `allkeys-lru`; queue
  workers 2×; falls back to `sync` on Vercel (serverless).
- **PG-compatible SQL**: all hand-written aggregates use `EXTRACT(MONTH FROM …)`
  / `COALESCE(SUM(…),0)` — a deliberate multi-DB portability decision (repo
  history: the original MySQL `MONTH()` broke PostgreSQL/AWS Aurora runs).

---

## 10. Testing & Quality

- **Pest** (^4) with Laravel plugin — feature suites: `AuthenticationTest`,
  `EmailVerificationTest`, `AuthorizationTest`, `DashboardTotalsTest` (sum
  regressions), `HttpTest`, `ModuleTest`, `UiStateTest`, `UiConsistencyTest`,
  `ValidationTest`, Profile/auth/Password tests.
- Static: **Pint** (PSR-12, `lint/lint-fix`), **PHPStan + Larastan** (`analyse`,
  config `phpstan.neon`), unit tests standalone.
- `composer check` = lint + analyse + test.
- Seeders: `JurnalCoaSeeder` (1000 clean jurnal rows) + `UsersTableSeeder`;
  factories for each model; `phpunit.xml`/sqlite default for CI.
- CI presence: `.github/` workflow; deployment quality gates on Daisy + Railway.

---

## 11. Known Gaps & Recommended Improvements

**Documented deviations / risks (no fix applied in this pass — design review items):**

1. **Dashboard logic duplicated** — `Livewire\Dashboard::loadDashboardData()` and
   `HomeController::getDashboardData()` are ~95% identical. **Recommendation:** extract
   a `DashboardService` used by both; the PRD anticipated a Service layer.
2. **Monthly summary in-memory** — pulls all period rows into PHP for grouping.
   Fine now; if the journal volume per period grows past ~20k rows, move to
   `GROUP BY DATE_FORMAT`/`EXTRACT` SQL.
3. **Legacy duplicate view trees** — `admin|operator|bod|rootsuperuser/*` mirrors of
   the same jurnaling/ledger pages; legacy routes + old controllers shadow the
   Livewire versions (two ways to reach the same screen). Deprecated once the
   Livewire set is fully tested; legacy `rootsuperuser/*` routes can then be
   stripped (breaking change — coordinate with auth).
4. **`kategori_jurnal` migration is a no-op stub** — consider deleting the empty
   migration or folding intent into `README` (already correct in DDL).
5. **No `user_id` on journals** — “user” column in activity arrives from
   `activity_log` (`causer`) rather than the journal row; if per-user reporting is
   needed, add `created_by` FK **+ backfill**.
6. **BOD read-only** is enforced by routes/HasRole, but nothing stops a BOD user
   from hitting a write endpoint on the legacy set (`admin` prefix excludes BOD by
   CheckRole; re-verify after adding any future BOD menu).
7. **nerca_saldos string FK** — robust but runs against convention; keep the
   explicit `'coa_id','kode_akun'` mapping documented (see §5.2) so joins are
   FK-correct.

---

## 12. Recent Architecture Timeline (for context)

| Change | What it did | Why |
|---|---|---|
| Dashboard-sum fix (`/draft/fix-pg-sum`) | jurnal `debit/kredit` VARCHAR→NUMERIC(15,2) + `decimal:2` cast; `EXTRACT()` swap in GL | Fixed `SUM()` type-42883 crash on PG/MySQL; database-portable counts |
| Consolidate Docker | Redis merged into app container (read-only root + tmpfs) | Simplicity, one container on Railway, fewer moving parts |
| Livewire-app 5 (`Dashboard` etc.) | New role-aware full-page components; old controller views kept as legacy | UX & maintenance; dual path for 0-downtime migration |
| Unique `nomor_bukti` added then dropped | unique per period → removed | Business rule was relaxed |