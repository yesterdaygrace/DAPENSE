# DAPENSE — Database Documentation

> **Project:** DAPENSE — *Dana Pensiun Sekolah Kristen Salatiga* (Pension Fund Accounting System)  
> **Stack:** Laravel 13 · MySQL 8.4 (prod) / SQLite (test) · Eloquent ORM  
> **Generated:** 2026-08-31 — derived from `database/migrations/*.php`, `app/Models/*.php`, `config/activitylog.php`, `database/seed_data_jurnal_coa.sql`  
> **Source commit:** `main` — all statements verified against live DDL

---

## Table of Contents

1. [Overview & Conventions](#1-overview--conventions)
2. [Entity-Relationship Diagram](#2-entity-relationship-diagram)
3. [Relationship Summary Matrix](#3-relationship-summary-matrix)
4. [Domain Tables](#4-domain-tables)
   - [4.1 users](#41-users)
   - [4.2 periodes](#42-periodes)
   - [4.3 header_coas](#43-header_coas)
   - [4.4 coas](#44-coas)
   - [4.5 jurnalings](#45-jurnalings)
   - [4.6 saldo_awal](#46-saldo_awal)
   - [4.7 neraca_saldos](#47-neraca_saldos)
   - [4.8 otorisators](#48-otorisators)
   - [4.9 products (demo module)](#49-products-demo-module)
5. [Auditing Table](#5-auditing-table)
   - [5.1 activity_log](#51-activity_log--activitylogtable_name)
6. [Framework / Infrastructure Tables](#6-framework--infrastructure-tables)
   - [6.1 password_reset_tokens](#61-password_reset_tokens)
   - [6.2 sessions](#62-sessions)
   - [6.3 cache / cache_locks](#63-cache--cache_locks)
   - [6.4 jobs / job_batches / failed_jobs](#64-jobs--job_batches--failed_jobs)
7. [Foreign-Key & Constraint Catalogue](#7-foreign-key--constraint-catalogue)
8. [Indexes & Uniqueness](#8-indexes--uniqueness)
9. [Schema Evolution Notes](#9-schema-evolution-notes)
10. [Eloquent Model Map](#10-eloquent-model-map)
11. [Seed Data Reference](#11-seed-data-reference)
12. [Business Rules Enforced in Application Layer](#12-business-rules-enforced-in-application-layer)
13. [File Reference Index](#13-file-reference-index)

---

## 1. Overview & Conventions

| Concern | Convention |
|---|---|
| **Naming** | Laravel default: `id` BIGSERIAL PK, `created_at`/`updated_at` timestamps, snake_case columns |
| **Engine** | InnoDB (MySQL) — FKs enforced; SQLite in tests (FKs via `constrained()`) |
| **Currency** | Indonesian Rupiah — `DECIMAL(15,2)` / `NUMERIC(15,2)` throughout ledger |
| **Double-entry** | One row **per journal line** in `jurnalings`; a single `nomor_bukti` (voucher) spans ≥2 rows (debit + credit) — sum must balance |
| **Period scoping** | Every ledger row (`jurnalings`, `saldo_awal`, `neraca_saldos`) is scoped to `periodes.id` |
| **Soft deletes** | Not used — deletes are hard with `CASCADE` where FKs exist |
| **Auditing** | `spatie/laravel-activitylog` on `users` only (name/email/usertype/status) |

> **Reading the tables below:** `PK` = primary key, `FK` = foreign key, `UQ` = unique, `IDX` = indexed, `NN` = NOT NULL.

---

## 2. Entity-Relationship Diagram

```mermaid
erDiagram
    users ||--o{ activity_log : "causes (morph causer)"
    
    periodes ||--o{ jurnalings : "1:N periode_id CASCADE"
    periodes ||--o{ saldo_awal : "1:N periode_id CASCADE"
    periodes ||--o{ neraca_saldos : "1:N periode_id CASCADE"

    header_coas ||--o{ header_coas : "self parent_id"
    header_coas ||--o{ coas : "1:N header_coa_id CASCADE"

    coas ||--o{ jurnalings : "1:N coa_id CASCADE"
    coas ||--o{ saldo_awal : "1:N coa_id CASCADE"
    coas ||--o{ neraca_saldos : "1:N kode_akun (string FK)"

    %% Isolated / framework
    otorisators
    products
    activity_log
    password_reset_tokens
    sessions
    cache
    cache_locks
    jobs
    job_batches
    failed_jobs

    header_coas {
        bigint id PK
        varchar kode_header
        varchar nama_header
        int level
        bigint parent_id FK_NULL
    }
    coas {
        bigint id PK
        varchar kode_akun UQ
        varchar nama_akun
        varchar saldo_normal
        varchar kategori
        int level
        bigint header_coa_id FK
    }
    periodes {
        bigint id PK
        varchar nama_periode
        date tanggal_awal
        date tanggal_akhir
        boolean is_rekap
    }
    jurnalings {
        bigint id PK
        date tanggal_jurnal
        varchar nomor_bukti
        varchar keterangan
        varchar kategori_jurnal
        numeric debit
        numeric kredit
        bigint coa_id FK
        bigint periode_id FK
    }
    saldo_awal {
        bigint id PK
        bigint coa_id FK
        date tanggal_saldo
        decimal debit
        decimal kredit
        bigint periode_id FK
    }
    neraca_saldos {
        bigint id PK
        varchar coa_id FK_kode_akun
        bigint periode_id FK
        date month
        decimal saldo_awal
        decimal debit
        decimal kredit
        decimal balance
    }
```

**Visual reading order:** `periodes` is the time dimension — everything financial hangs off it. `header_coas` → `coas` is the account taxonomy. `jurnalings` is the fact table at the center, joining both dimensions. `saldo_awal` and `neraca_saldos` are derived/snapshot tables for opening and trial balance.

---

## 3. Relationship Summary Matrix

| Parent | Child | Cardinality | FK Column | On Delete | Notes |
|---|---|---|---|---|---|
| `header_coas` | `header_coas` | 1:N self | `parent_id` | — (nullable, no cascade in DDL) | 2-level hierarchy in seed (level 1 → 2) |
| `header_coas` | `coas` | 1:N | `coas.header_coa_id` | `CASCADE` | Every COA must belong to a header group |
| `coas` | `jurnalings` | 1:N | `jurnalings.coa_id` | `CASCADE` | Journal lines reference `coas.id` |
| `coas` | `saldo_awal` | 1:N | `saldo_awal.coa_id` | `CASCADE` | Opening balance per COA/period |
| `coas` | `neraca_saldos` | 1:N | `neraca_saldos.coa_id` | `CASCADE` | **String FK → `coas.kode_akun`** (exception) |
| `periodes` | `jurnalings` | 1:N | `jurnalings.periode_id` | `CASCADE` | All journals scoped to a period |
| `periodes` | `saldo_awal` | 1:N | `saldo_awal.periode_id` | `CASCADE` | |
| `periodes` | `neraca_saldos` | 1:N | `neraca_saldos.periode_id` | `CASCADE` | |
| `users` | `activity_log` | 1:N (polymorphic) | `causer_type`+`causer_id` | — | Spatie morph, no FK constraint |
| *(none)* | `otorisators` | — | — | — | Standalone signatory table |
| *(none)* | `products` | — | — | — | Demo CRUD, no relations |

> No `user_id` FK exists on `jurnalings`. Audit trail for user activity is via `activity_log.causer` (Spatie), not the journal row itself.

---

## 4. Domain Tables

### 4.1 `users`

Application users with role-based access. Default seeding via `UsersTableSeeder`.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | Surrogate key |
| `name` | `VARCHAR(255)` | **NN** | Display name |
| `email` | `VARCHAR(255)` | **NN**, **UQ** | Login identifier, unique |
| `usertype` | `VARCHAR(255)` | **NN**, `DEFAULT 'rootsuperuser'` | Role enum: `rootsuperuser` \| `admin` \| `operator` \| `bod` |
| `image` | `VARCHAR(255)` | nullable | Avatar path (nullable) |
| `status` | `INT` | **NN**, `DEFAULT 1` | `1` = active, `0` = inactive — checked by `User::isActive()` |
| `email_verified_at` | `TIMESTAMP` | nullable | Verified timestamp (MustVerifyEmail) |
| `password` | `VARCHAR(255)` | **NN** | Hashed (`password: hashed` cast) |
| `remember_token` | `VARCHAR(100)` | nullable | Remember-me token |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Constraints:** `UNIQUE(email)` · `fillable: name, email, usertype, status, password, image` · Logs activity on `name, email, usertype, status` (dirty only).  
**Model:** `App\Models\User` — `HasFactory, LogsActivity, Notifiable`, implements `MustVerifyEmail`.

---

### 4.2 `periodes`

Accounting periods — the time boundary for all reporting. Active period = `is_rekap = false` ordered by `tanggal_awal DESC` (most recent open).

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `nama_periode` | `VARCHAR(255)` | **NN** | e.g. `"Tahun 2025"` |
| `tanggal_awal` | `DATE` | **NN** | Period start (inclusive) |
| `tanggal_akhir` | `DATE` | **NN** | Period end (inclusive) |
| `is_rekap` | `BOOLEAN` | **NN**, `DEFAULT false` | `false` = open/active, `true` = recapped/closed |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Model:** `App\Models\Periode` — `table: periodes`.  
**Seed:** `1` row — `Tahun 2025 (2025-01-01 → 2025-12-31)`.

---

### 4.3 `header_coas`

Hierarchical COA group headers. Self-referencing tree (parent → children).

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `kode_header` | `VARCHAR(255)` | **NN** | Group code — e.g. `1`, `1.1`, `2.1` |
| `nama_header` | `VARCHAR(255)` | **NN** | Group name — e.g. `ASET`, `ASET LANCAR` |
| `level` | `INT` | **NN** | Depth in tree: `1` = root, `2` = child |
| `parent_id` | `BIGINT UNSIGNED` | nullable, **FK → header_coas.id** | Self-FK; `NULL` for root nodes |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Hierarchy (seed):**

```
1  ASET                    (level 1, parent NULL)
 ├─ 1.1  ASET LANCAR       (level 2)
 ├─ 1.2  ASET TETAP
 └─ 1.3  ASET TIDAK BERWUJUD
2  KEWAJIBAN
 ├─ 2.1  KEWAJIBAN LANCAR
 └─ 2.2  KEWAJIBAN JANGKA PANJANG
3  MODAL
 ├─ 3.1  MODAL SAHAM
 └─ 3.2  LABA DITAHAN
4  PENDAPATAN
 ├─ 4.1  PENDAPATAN USAHA
 └─ 4.2  PENDAPATAN LAIN-LAIN
5  BEBAN
 ├─ 5.1  BEBAN OPERASIONAL
 ├─ 5.2  BEBAN ADMINISTRASI DAN UMUM
 └─ 5.3  BEBAN LAIN-LAIN
```

**Model:** `App\Models\HeaderCOA` — `parent(): BelongsTo(self)`, `children(): HasMany(self)`, `coas(): HasMany(COA)` ordered by `kode_akun`.  
**Seed:** 17 rows.

---

### 4.4 `coas`

Chart of Accounts — leaf accounts. The central reference for all ledger entries.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | Surrogate key (used by most FKs) |
| `kode_akun` | `VARCHAR(255)` | **NN**, **UQ**, **IDX** | Account code, e.g. `10010001`. Globally unique. Also FK target for `neraca_saldos` |
| `nama_akun` | `VARCHAR(255)` | **NN** | Account name, e.g. `Kas`, `Utang Usaha` |
| `saldo_normal` | `VARCHAR(255)` | **NN** | Normal balance: `Debit` or `Kredit` |
| `kategori` | `VARCHAR(255)` | **NN** | Category: `Aset` \| `Kewajiban` \| `Modal` \| `Pendapatan` \| `Beban` |
| `level` | `INT` | **NN** | Always `3` in seed (leaf level under level-2 headers) |
| `header_coa_id` | `BIGINT UNSIGNED` | **NN**, **FK → header_coas.id**, `CASCADE` | Owning header group |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Constraints:**

- `UNIQUE(kode_akun)`
- `UNIQUE(kode_akun, nama_akun)` — composite (redundant with single-col but present in DDL)

**Code ranges (seed):**

| Prefix | Meaning | Count |
|---|---|---|
| `1001xxxx` | Aset Lancar (Kas, Bank, Piutang…) | 16 |
| `1002xxxx` | Aset Tetap + Akum. Penyusutan | 10 |
| `1003xxxx` | Aset Tidak Berwujud | 4 |
| `2001xxxx` | Kewajiban Lancar | 13 |
| `2002xxxx` | Kewajiban Jangka Panjang | 4 |
| `3001xxxx` | Modal Saham / Prive | 4 |
| `3002xxxx` | Laba Ditahan / Cadangan | 3 |
| `4001xxxx` | Pendapatan Usaha | 8 |
| `4002xxxx` | Pendapatan Lain-lain | 4 |
| `5001xxxx` | Beban Operasional | 14 |
| `5002xxxx` | Beban Adm & Umum | 10 |
| `5003xxxx` | Beban Lain-lain | 10 |

**Model:** `App\Models\COA` — `headerCoa(): BelongsTo`, `jurnalings(): HasMany`.  
**Seed:** 100 rows.

---

### 4.5 `jurnalings`

**Fact table — one row per journal line** (not per voucher). Core of the double-entry system.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `tanggal_jurnal` | `DATE` | **NN** | Transaction date — used for period filtering & dashboard trends |
| `nomor_bukti` | `VARCHAR(255)` | **NN**, **IDX** (historic unique dropped) | Voucher number, e.g. `BV/2025/000001`. Shared across ≥2 rows per transaction |
| `keterangan` | `VARCHAR(255)` | **NN** | Description / narration |
| `kategori_jurnal` | `VARCHAR(255)` | **NN** | Category: `Kas Masuk` `Kas Keluar` `Bank Masuk` `Bank Keluar` `Memorial` `Memorial Penutup` `Penyesuaian` `Pembelian` `Penjualan` `Penggajian` `Pajak` `Modal` `Investasi Aset Tetap` etc. |
| `debit` | `NUMERIC(15,2)` | **NN**, `DEFAULT 0` | Debit amount. **Was `VARCHAR(255)`** — converted 2026-08-03 |
| `kredit` | `NUMERIC(15,2)` | **NN**, `DEFAULT 0` | Credit amount. **Was `VARCHAR(255)`** — converted 2026-08-03 |
| `coa_id` | `BIGINT UNSIGNED` | **NN**, **FK → coas.id**, `CASCADE` | Account affected by this line |
| `periode_id` | `BIGINT UNSIGNED` | **NN**, **FK → periodes.id**, `CASCADE` | Accounting period scope |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Double-entry invariant (application-enforced):**

```
For each nomor_bukti within a periode:
  SUM(debit) == SUM(kredit)
```

Not enforced by DB constraint — validated in `StoreJurnalingRequest` (`debit.*`/`kredit.*` arrays `numeric|min:0`) and controller `storeEntry` logic. The per-row model allows vouchers to split across many accounts.

**Example (seed `BV/2025/000001`):**

| id | nomor_bukti | keterangan | debit | kredit | coa_id (akun) |
|---|---|---|---|---|---|
| 1 | BV/2025/000001 | Pembelian bahan baku… | 11,926,000 | 0 | 13 — Persediaan Bahan Baku |
| 2 | BV/2025/000001 | Pembelian bahan baku… | 0 | 11,926,000 | 31 — Utang Usaha |

**Indexes:** `FK` indexes on `coa_id`, `periode_id`. Historical `UNIQUE(nomor_bukti, periode_id)` was added 2026-07-26 then dropped 2026-08-02 (conflicts with paired rows).  
**Model:** `App\Models\Jurnaling` — casts `debit`/`kredit` as `decimal:2`; `coa(): BelongsTo`, `periode(): BelongsTo`.  
**Seed:** 1,000 rows = 500 transactions × 2 lines.

---

### 4.6 `saldo_awal`

Opening balances per account per period. Used by Buku Besar (ledger) to compute `saldoAwal`.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `coa_id` | `BIGINT UNSIGNED` | **NN**, **FK → coas.id**, `CASCADE` | Account |
| `tanggal_saldo` | `DATE` | **NN** | As-of date, e.g. `2025-01-01` (period start) |
| `debit` | `DECIMAL(15,2)` | **NN** | Opening debit |
| `kredit` | `DECIMAL(15,2)` | **NN** | Opening credit |
| `periode_id` | `BIGINT UNSIGNED` | **NN**, **FK → periodes.id**, `CASCADE` | Period |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Ledger formula:**

```
saldoAwal = SUM(saldo_awal.debit - kredit WHERE coa=X AND tanggal_saldo <= start)
          + SUM(jurnalings.debit - kredit WHERE coa=X AND tanggal_jurnal < start)
```

**Model:** `App\Models\SaldoAwal` — `table: saldo_awal`; `coa(): BelongsTo`, `periode(): BelongsTo`.  
**Seed:** 54 rows (Aset/Kewajiban/Modal accounts as of 2025-01-01).

---

### 4.7 `neraca_saldos`

Persisted trial-balance snapshots. Built by `NERACA` Livewire component and `NeracaSaldoController`.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `coa_id` | `VARCHAR(255)` (string, **not** BIGINT) | **NN**, **FK → coas.kode_akun**, `CASCADE` | ⚠️ References `kode_akun`, not `id` |
| `periode_id` | `BIGINT UNSIGNED` | **NN**, **FK → periodes.id**, `CASCADE` | Period |
| `month` | `DATE` | **NN** | Snapshot month (first day of month or period month) |
| `saldo_awal` | `DECIMAL(15,2)` | **NN** | Opening for that month |
| `debit` | `DECIMAL(15,2)` | **NN** | Total debit in month |
| `kredit` | `DECIMAL(15,2)` | **NN** | Total credit in month |
| `balance` | `DECIMAL(15,2)` | **NN** | Computed `saldo_awal + debit - kredit` (or saldo-normal-aware) |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

> **Anomaly — string FK:** Unlike every other table, `neraca_saldos.coa_id` is a `VARCHAR` FK to `coas.kode_akun`. The Eloquent relation reflects this: `belongsTo(COA::class, 'coa_id', 'kode_akun')`. Any manual join must use `kode_akun`, not `id`. A `where('coa_id', $coa->id)` will return zero rows.

**Model:** `App\Models\NeracaSaldo` — `coa(): BelongsTo(COA, 'coa_id', 'kode_akun')`, `periode(): BelongsTo`.

---

### 4.8 `otorisators`

Authorizing officers — signatories on financial reports (e.g., Ketua, Bendahara). Standalone lookup table, no FKs out.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `nama_otorisator` | `VARCHAR(255)` | **NN** | Officer name |
| `jabatan_otorisator` | `VARCHAR(255)` | **NN** | Position / title |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Model:** `App\Models\Otorisator` — `table: otorisators`.

---

### 4.9 `products` (demo module)

Legacy/demo CRUD sample. No relations to accounting domain. Retained for template reference.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment | |
| `title` | `VARCHAR(255)` | **NN** | Product title |
| `category` | `VARCHAR(255)` | **NN** | Category string |
| `price` | `INT` | **NN** | Price (integer, no decimals) |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

---

## 5. Auditing Table

### 5.1 `activity_log` (`activitylog.table_name`)

Spatie `laravel-activitylog` — audit trail. Table name configurable via `config/activitylog.php` → `env('ACTIVITY_LOGGER_TABLE_NAME', 'activity_log')`.

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK**, auto-increment (`bigIncrements`) | |
| `log_name` | `VARCHAR(255)` | nullable, **IDX** | Log channel, default `default` |
| `description` | `TEXT` | **NN** | Human-readable event |
| `subject_type` | `VARCHAR(255)` | nullable | Polymorphic subject model class |
| `subject_id` | `BIGINT UNSIGNED` | nullable | Polymorphic subject id |
| `causer_type` | `VARCHAR(255)` | nullable | Polymorphic causer model class (e.g. `App\Models\User`) |
| `causer_id` | `BIGINT UNSIGNED` | nullable | Polymorphic causer id |
| `properties` | `JSON` | nullable | Changed attributes / custom props |
| `event` | `VARCHAR(255)` | nullable | Added 2026-07-15: event name (`created`/`updated`/custom) |
| `batch_uuid` | `UUID` | nullable | Added 2026-07-15: groups related log entries |
| `created_at` | `TIMESTAMP` | nullable | |
| `updated_at` | `TIMESTAMP` | nullable | |

**Morph indexes:** `subject_type + subject_id` (`subject` index), `causer_type + causer_id` (`causer` index), `log_name`.  
**Active usage:** Only `User` logs via `LogsActivity` (`logOnly: name, email, usertype, status`, dirty + non-empty). Journals themselves are not audited row-level.

---

## 6. Framework / Infrastructure Tables

### 6.1 `password_reset_tokens`

| Column | Type | Attr | Description |
|---|---|---|---|
| `email` | `VARCHAR(255)` | **PK** | User email |
| `token` | `VARCHAR(255)` | **NN** | Reset token (hashed) |
| `created_at` | `TIMESTAMP` | nullable | |

### 6.2 `sessions`

| Column | Type | Attr | Description |
|---|---|---|---|
| `id` | `VARCHAR(255)` | **PK** | Session id (string) |
| `user_id` | `BIGINT UNSIGNED` | nullable, **IDX**, **FK (nullable)** | Authenticated user |
| `ip_address` | `VARCHAR(45)` | nullable | Client IP |
| `user_agent` | `TEXT` | nullable | User agent |
| `payload` | `LONGTEXT` | **NN** | Serialized session data |
| `last_activity` | `INT` | **NN**, **IDX** | Unix timestamp |

### 6.3 `cache` / `cache_locks`

| Table | Column | Type | Attr | Description |
|---|---|---|---|---|
| `cache` | `key` | `VARCHAR(255)` | **PK** | Cache key |
|  | `value` | `MEDIUMTEXT` | **NN** | Serialized value |
|  | `expiration` | `INT` | **NN** | Unix expiry |
| `cache_locks` | `key` | `VARCHAR(255)` | **PK** | Lock key |
|  | `owner` | `VARCHAR(255)` | **NN** | Lock owner token |
|  | `expiration` | `INT` | **NN** | Unix expiry |

### 6.4 `jobs` / `job_batches` / `failed_jobs`

Standard Laravel queue tables.

**`jobs`:**

| Column | Type | Attr |
|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK** |
| `queue` | `VARCHAR(255)` | **NN**, **IDX** |
| `payload` | `LONGTEXT` | **NN** |
| `attempts` | `TINYINT UNSIGNED` | **NN** |
| `reserved_at` | `INT UNSIGNED` | nullable |
| `available_at` | `INT UNSIGNED` | **NN** |
| `created_at` | `INT UNSIGNED` | **NN** |

**`job_batches`:**

| Column | Type |
|---|---|
| `id` (PK) | `VARCHAR(255)` |
| `name` | `VARCHAR(255)` **NN** |
| `total_jobs` | `INT` **NN** |
| `pending_jobs` | `INT` **NN** |
| `failed_jobs` | `INT` **NN** |
| `failed_job_ids` | `LONGTEXT` **NN** |
| `options` | `MEDIUMTEXT` nullable |
| `cancelled_at` | `INT` nullable |
| `created_at` | `INT` **NN** |
| `finished_at` | `INT` nullable |

**`failed_jobs`:**

| Column | Type | Attr |
|---|---|---|
| `id` | `BIGINT UNSIGNED` | **PK** |
| `uuid` | `VARCHAR(255)` | **NN**, **UQ** |
| `connection` | `TEXT` | **NN** |
| `queue` | `TEXT` | **NN** |
| `payload` | `LONGTEXT` | **NN** |
| `exception` | `LONGTEXT` | **NN** |
| `failed_at` | `TIMESTAMP` | `useCurrent()` |

---

## 7. Foreign-Key & Constraint Catalogue

| # | Child Table | Child Column | → Parent Table.Column | On Delete | Migration |
|---|---|---|---|---|---|
| 1 | `header_coas` | `parent_id` | `header_coas.id` | `SET NULL` (implicit — nullable, no cascade) | `2024_07_12_155920_header` |
| 2 | `coas` | `header_coa_id` | `header_coas.id` | `CASCADE` | `2024_07_12_160105_c_o_a` |
| 3 | `jurnalings` | `coa_id` | `coas.id` | `CASCADE` | `2024_08_02_072943_jurnalings` |
| 4 | `jurnalings` | `periode_id` | `periodes.id` | `CASCADE` | `2024_08_02_072943_jurnalings` |
| 5 | `saldo_awal` | `coa_id` | `coas.id` | `CASCADE` | `2024_08_10_114736_create_saldo_awals_table` |
| 6 | `saldo_awal` | `periode_id` | `periodes.id` | `CASCADE` | same |
| 7 | `neraca_saldos` | `coa_id` (VARCHAR) | `coas.kode_akun` | `CASCADE` | `2024_09_07_064446_create_neraca_saldos_table` |
| 8 | `neraca_saldos` | `periode_id` | `periodes.id` | `CASCADE` | same |

> `neraca_saldos` (row 7) is the only string-to-string FK in the domain schema — all others are integer `BIGINT` to `id`.

---

## 8. Indexes & Uniqueness

| Table | Index | Columns | Type | Notes |
|---|---|---|---|---|
| `users` | `users_email_unique` | `email` | **UNIQUE** | Login uniqueness |
| `coas` | `coas_kode_akun_unique` | `kode_akun` | **UNIQUE** | Account code globally unique |
| `coas` | `coas_kode_akun_nama_akun_unique` | `kode_akun`, `nama_akun` | **UNIQUE** | Composite — redundant but present |
| `jurnalings` | *(dropped)* `jurnalings_nomor_bukti_periode_unique` | `nomor_bukti`, `periode_id` | **UNIQUE** (removed 2026-08-02) | Dropped — conflicts with double-entry paired rows |
| `activity_log` | `activity_log_log_name_index` | `log_name` | **INDEX** | Spatie |
| `activity_log` | `subject` | `subject_type`, `subject_id` | **INDEX** (morph) | |
| `activity_log` | `causer` | `causer_type`, `causer_id` | **INDEX** (morph) | |
| `sessions` | `sessions_user_id_index` | `user_id` | **INDEX** | |
| `sessions` | `sessions_last_activity_index` | `last_activity` | **INDEX** | |
| `jobs` | `jobs_queue_index` | `queue` | **INDEX** | |
| `failed_jobs` | `failed_jobs_uuid_unique` | `uuid` | **UNIQUE** | |

All FK columns are implicitly indexed by InnoDB.

---

## 9. Schema Evolution Notes

| Date | Migration | Effect |
|---|---|---|
| 2024-07-09 | `create_periodes_table` | Introduces period dimension |
| 2024-07-12 | `header` + `c_o_a` | COA taxonomy (2 files) |
| 2024-08-02 | `jurnalings` | Fact table — `debit`/`kredit` as **VARCHAR(255)** originally |
| 2024-08-10 | `create_saldo_awals_table` | Opening balance |
| 2024-09-06 | `add_kategori_jurnal_to_jurnaling_table` | **Empty stub / no-op** — `kategori_jurnal` already existed in 2024-08-02 DDL. Safe dead code. |
| 2024-09-07 | `create_neraca_saldos_table` | Trial-balance snapshot with string FK to `kode_akun` |
| 2025-12-04 | `create_otorisators_table` | Signatories |
| 2026-07-15 | `create_activity_log_table` + `add_event_column` + `add_batch_uuid_column` | Spatie audit log (3 migrations) |
| 2026-07-26 | `add_unique_nomor_bukti_per_periode` | `UNIQUE(nomor_bukti, periode_id)` on `jurnalings` |
| 2026-08-02 | `drop_unique_nomor_bukti_per_periode` | **Drops** that UNIQUE — reason: one voucher legitimately has multiple rows (debit + credit). Comment preserved in migration. |
| 2026-08-03 | `convert_jurnalings_debit_kredit_to_numeric` | `VARCHAR → NUMERIC(15,2) NOT NULL DEFAULT 0` via raw `DB::statement`. Fixes `SUM()` type errors on PostgreSQL/MySQL. `down()` returns to `VARCHAR(255)`. |

---

## 10. Eloquent Model Map

| Model | Table | Key Relations | Casts / Traits |
|---|---|---|---|
| `App\Models\User` | `users` | *(no FK to journals)* | `email_verified_at: datetime`, `password: hashed`; `HasFactory, LogsActivity, Notifiable` |
| `App\Models\Periode` | `periodes` | — | `fillable: nama_periode, tanggal_awal, tanggal_akhir, is_rekap` |
| `App\Models\HeaderCOA` | `header_coas` | `parent(): BelongsTo(self)`<br>`children(): HasMany(self)`<br>`coas(): HasMany(COA)` | `fillable: kode_header, nama_header, level, parent_id` |
| `App\Models\COA` | `coas` | `headerCoa(): BelongsTo(HeaderCOA)`<br>`jurnalings(): HasMany(Jurnaling)` | `fillable: kode_akun, nama_akun, saldo_normal, kategori, level, header_coa_id` |
| `App\Models\Jurnaling` | `jurnalings` | `coa(): BelongsTo(COA)`<br>`periode(): BelongsTo(Periode)` | `debit: decimal:2`, `kredit: decimal:2` |
| `App\Models\SaldoAwal` | `saldo_awal` | `coa(): BelongsTo(COA)`<br>`periode(): BelongsTo(Periode)` | `table: saldo_awal` |
| `App\Models\NeracaSaldo` | `neraca_saldos` | `coa(): BelongsTo(COA, 'coa_id', 'kode_akun')`<br>`periode(): BelongsTo(Periode)` | String FK binding |
| `App\Models\Otorisator` | `otorisators` | — | `table: otorisators` |

**Factory coverage:** `UserFactory`, `COAFactory`, `HeaderCoaFactory`, `JurnalingFactory`, `PeriodeFactory`, `SaldoAwalFactory`, `OtorisatorFactory` under `database/factories/`.

---

## 11. Seed Data Reference

Single SQL seed file `database/seed_data_jurnal_coa.sql` executed via `JurnalCoaSeeder`:

| Dataset | Volume | Detail |
|---|---|---|
| `header_coas` | 17 rows | 5 roots (ASET/KEWAJIBAN/MODAL/PENDAPATAN/BEBAN) + 12 level-2 children |
| `periodes` | 1 row | `Tahun 2025` |
| `coas` | 100 rows | Codes `10010001–50030010` covering all 5 categories |
| `saldo_awal` | 54 rows | Balance-sheet accounts (Aset/Kewajiban/Modal) as of `2025-01-01` |
| `jurnalings` | 1,000 rows | 500 voucher transactions (`BV/2025/000001` … `BV/2025/000500`), each 2 paired rows, spanning 2025-01-01 → 2025-12-31, ~15 `kategori_jurnal` variants with realistic Rupiah amounts |

Plus `UsersTableSeeder` for default users.

---

## 12. Business Rules Enforced in Application Layer

These invariants are **not** DB constraints — they are validated in controllers, Form Requests, and Livewire components:

| Rule | Where |
|---|---|
| `SUM(debit) == SUM(kredit)` per `nomor_bukti` per period | `StoreJurnalingRequest` + `JurnalingController::store*` family |
| `tanggal_jurnal` required, `nomor_bukti` required max 50, `coa_id` array must `exists:coas,id`, `debit.*`/`kredit.*` numeric `min:0` | `StoreJurnalingRequest` |
| Active period resolver: `is_rekap=false` latest `tanggal_awal`; previous period = max `tanggal_akhir < aktif.tanggal_awal` | Dashboard, ledger, exports |
| Voucher prefix checks per journal category (KM/KK/BM/BK/Mem…) | `cekNomorBukti*` methods |
| Rate limits: `export` 3/min, `import` 2/min, `posting` 10/min | `RateLimiter` + routes |
| Role gates: `export-journal`, `import-data`, `post-journal`, `manage-users` + `CheckRole` middleware + `HasRole::canAccess()` | Routes, policies, Livewire `boot()` |

---

## 13. File Reference Index

| Concern | Paths |
|---|---|
| Migrations (domain) | `database/migrations/2024_07_09_044815_create_periodes_table.php`<br>`2024_07_12_155920_header.php`<br>`2024_07_12_160105_c_o_a.php`<br>`2024_08_02_072943_jurnalings.php`<br>`2024_08_10_114736_create_saldo_awals_table.php`<br>`2024_09_07_064446_create_neraca_saldos_table.php`<br>`2025_12_04_020125_create_otorisators_table.php` |
| Migrations (audit) | `2026_07_15_14290{5,6,7}_*activity_log*.php` |
| Migrations (constraint evolution) | `2026_07_26_000001_add_unique_nomor_bukti_per_periode.php`<br>`2026_08_02_000002_drop_unique_nomor_bukti_per_periode.php`<br>`2026_08_03_000001_convert_jurnalings_debit_kredit_to_numeric.php` |
| Migrations (framework) | `0001_01_01_00000{0,1,2}_*.php` (password_reset_tokens/sessions, cache, jobs) |
| Models | `app/Models/{User,Periode,HeaderCOA,COA,Jurnaling,SaldoAwal,NeracaSaldo,Otorisator}.php` |
| Config | `config/activitylog.php` (table_name, connection, model) |
| Seed | `database/seed_data_jurnal_coa.sql` + `database/seeders/{JurnalCoaSeeder,DatabaseSeeder,UsersTableSeeder}.php` |
| Factories | `database/factories/*.php` (7 factories) |
| System design (companion) | `docs/system-design.md` §5 |

---

*End of DAPENSE Database Documentation. For architecture, request lifecycle, RBAC, and deployment, see `docs/system-design.md`.*
