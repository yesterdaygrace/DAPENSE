# Database — DAPENSE

> Schema, constraints, integrity, and data layer design.

## 1. Engine Support

| Engine | Version | Role | Config |
|--------|---------|------|--------|
| MySQL | 8.4 | Production primary | `DB_CONNECTION=mysql` |
| PostgreSQL | 16 | Portable alternative | `DB_CONNECTION=pgsql` |
| SQLite | — | Dev/test fallback | `DB_CONNECTION=sqlite` |

One engine is active at a time, selected via `DB_CONNECTION` in `.env`.

## 2. Schema Overview (19 Migrations)

| # | Migration | Table | Purpose |
|---|-----------|-------|---------|
| 1 | `0001_01_01_000000` | `sessions` | Laravel session storage |
| 2 | `0001_01_01_000001` | `cache` | Application cache |
| 3 | `0001_01_01_000002` | `jobs` | Queue jobs + failed_jobs |
| 4 | `2024_06_17_033310` | `users` | User accounts |
| 5 | `2024_06_17_033649` | `products` | Product catalog (legacy) |
| 6 | `2024_07_09_044815` | `periodes` | Accounting periods |
| 7 | `2024_07_12_155920` | `header_coas` | COA header groups |
| 8 | `2024_07_12_160105` | `coas` | Chart of Accounts |
| 9 | `2024_08_02_072943` | `jurnalings` | Journal entries |
| 10 | `2024_08_10_114736` | `saldo_awal` | Opening balances |
| 11 | `2024_09_06_023522` | `jurnalings` (alter) | Add `kategori_jurnal` column |
| 12 | `2024_09_07_064446` | `neraca_saldos` | Trial balance snapshots |
| 13 | `2025_12_04_020125` | `otorisators` | Authorized approvers |
| 14 | `2026_07_15_142905` | `activity_log` | Spatie audit trail |
| 15 | `2026_07_15_142906` | `activity_log` (alter) | Add `event` column |
| 16 | `2026_07_15_142907` | `activity_log` (alter) | Add `batch_uuid` column |
| 17 | `2026_07_26_000001` | `jurnalings` (alter) | Add unique `nomor_bukti+periode_id` |
| 18 | `2026_08_02_000002` | `jurnalings` (alter) | Drop unique constraint |
| 19 | `2026_08_03_000001` | `jurnalings` (alter) | Convert `debit`/`kredit` VARCHAR → NUMERIC(15,2) |

## 3. Core Tables (8 domain tables)

### 3.1 `users`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `name` | VARCHAR(255) | NOT NULL |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL |
| `email_verified_at` | TIMESTAMP | NULLABLE |
| `password` | VARCHAR(255) | NOT NULL, hashed |
| `usertype` | VARCHAR(50) | NOT NULL, ENUM: rootsuperuser, admin, operator, bod |
| `status` | TINYINT | NOT NULL, DEFAULT 1 (1=active, 0=inactive) |
| `image` | VARCHAR(255) | NULLABLE |
| `remember_token` | VARCHAR(100) | NULLABLE |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

### 3.2 `periodes`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `nama_periode` | VARCHAR(255) | NOT NULL |
| `tanggal_awal` | DATE | NOT NULL |
| `tanggal_akhir` | DATE | NOT NULL |
| `is_rekap` | BOOLEAN | DEFAULT false |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

### 3.3 `header_coas`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `kode_header` | VARCHAR(50) | NOT NULL, UNIQUE |
| `nama_header` | VARCHAR(255) | NOT NULL |
| `level` | INT | NOT NULL |
| `parent_id` | BIGINT UNSIGNED | FK → `header_coas.id` (self-referencing), ON DELETE SET NULL |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

### 3.4 `coas`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `kode_akun` | VARCHAR(50) | NOT NULL, UNIQUE |
| `nama_akun` | VARCHAR(255) | NOT NULL |
| `saldo_normal` | ENUM('debet','kredit') | NOT NULL |
| `kategori` | VARCHAR(100) | NULLABLE |
| `level` | INT | NOT NULL |
| `header_coa_id` | BIGINT UNSIGNED | FK → `header_coas.id`, ON DELETE CASCADE |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

**Unique constraint:** `kode_akun` + `nama_akun` (composite)

### 3.5 `jurnalings`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `tanggal_jurnal` | DATE | NOT NULL |
| `nomor_bukti` | VARCHAR(100) | NOT NULL |
| `keterangan` | TEXT | NULLABLE |
| `kategori_jurnal` | VARCHAR(50) | NOT NULL (kas keluar, bank masuk, bank keluar, memorial, memorial penutup) |
| `debit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `kredit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `coa_id` | BIGINT UNSIGNED | FK → `coas.id`, ON DELETE CASCADE |
| `periode_id` | BIGINT UNSIGNED | FK → `periodes.id`, ON DELETE CASCADE |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

**Unique constraint (dropped):** `nomor_bukti` + `periode_id` (added migration #17, dropped migration #18)

### 3.6 `saldo_awal`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `coa_id` | BIGINT UNSIGNED | FK → `coas.id`, ON DELETE CASCADE |
| `tanggal_saldo` | DATE | NOT NULL |
| `debit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `kredit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `periode_id` | BIGINT UNSIGNED | FK → `periodes.id`, ON DELETE CASCADE |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

### 3.7 `neraca_saldos`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `coa_id` | BIGINT UNSIGNED | FK → `coas.id`, ON DELETE CASCADE |
| `periode_id` | BIGINT UNSIGNED | FK → `periodes.id`, ON DELETE CASCADE |
| `month` | INT | NOT NULL |
| `debit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `kredit` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `balance` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `saldo_awal` | DECIMAL(15,2) | NOT NULL, DEFAULT 0 |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

### 3.8 `otorisators`

| Column | Type | Constraints |
|--------|------|------------|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `nama_otorisator` | VARCHAR(255) | NOT NULL |
| `jabatan_otorisator` | VARCHAR(255) | NOT NULL |
| `created_at` | TIMESTAMP | — |
| `updated_at` | TIMESTAMP | — |

## 4. Foreign Key Catalogue (8 FKs)

| # | Source Table | Source Column | Target Table | Target Column | On Delete |
|---|-------------|--------------|-------------|--------------|-----------|
| 1 | `header_coas` | `parent_id` | `header_coas` | `id` | SET NULL |
| 2 | `coas` | `header_coa_id` | `header_coas` | `id` | CASCADE |
| 3 | `jurnalings` | `coa_id` | `coas` | `id` | CASCADE |
| 4 | `jurnalings` | `periode_id` | `periodes` | `id` | CASCADE |
| 5 | `saldo_awal` | `coa_id` | `coas` | `id` | CASCADE |
| 6 | `saldo_awal` | `periode_id` | `periodes` | `id` | CASCADE |
| 7 | `neraca_saldos` | `coa_id` | `coas` | `id` | CASCADE |
| 8 | `neraca_saldos` | `periode_id` | `periodes` | `id` | CASCADE |

## 5. Unique Constraints

| Table | Columns | Status |
|-------|---------|--------|
| `header_coas` | `kode_header` | Active |
| `coas` | `kode_akun` | Active |
| `coas` | `kode_akun` + `nama_akun` | Active (composite) |
| `jurnalings` | `nomor_bukti` + `periode_id` | Dropped (migration #18) |

## 6. Indexes

All FK columns are auto-indexed by MySQL/PostgreSQL. Additional indexes:

| Table | Column(s) | Type | Purpose |
|-------|-----------|------|---------|
| `jurnalings` | `coa_id` | FK index | JOIN performance |
| `jurnalings` | `periode_id` | FK index | Period filtering |
| `jurnalings` | `nomor_bukti` | Query pattern | Evidence number lookup |
| `jurnalings` | `kategori_jurnal` | Query pattern | Journal type filtering |
| `coas` | `kode_akun` | Unique index | Account code lookup |
| `header_coas` | `kode_header` | Unique index | Header code lookup |

## 7. Data Integrity

### 7.1 REFERENTIAL INTEGRITY

All 8 foreign keys enforce referential integrity at the database level:
- **CASCADE** on `coas` and `periodes` deletions propagates to child records
- **SET NULL** on `header_coas.parent_id` preserves child headers when parent is deleted

### 7.2 APPLICATION-LEVEL CHECKS

| Check | Location | Description |
|-------|----------|-------------|
| Journal balance | `trg_jurnal_balance_check` (SQL trigger) | Ensures `debit > 0 XOR kredit > 0` |
| Duplicate prevention | `JurnalingController` | Validates `nomor_bukti` uniqueness per period |
| Period locking | `PostingController` | `is_rekap` flag prevents edits to closed periods |
| User activation | `UserPolicy` | `status == 1` required for login |

### 7.3 SEED DATA

| Seed | Count | Source |
|------|-------|--------|
| Header COAs | 17 | `JurnalCoaSeeder` |
| COAs | 100 | `JurnalCoaSeeder` |
| Saldo Awal | 54 | `JurnalCoaSeeder` |
| Journal entries | 1,000+ | `JurnalCoaSeeder` |
| Users | 4 (role admin) | `UsersTableSeeder` |

## 8. VARCHAR → NUMERIC Migration

**Problem:** `debit` and `kredit` columns were originally `VARCHAR`, causing:
- SUM/AVG aggregation failures
- Sorting by numeric value impossible
- PHP `decimal:2` cast producing incorrect results

**Solution:** Migration #19 (`2026_08_03_000001_convert_jurnalings_debit_kredit_to_numeric`)

```sql
-- MySQL
ALTER TABLE jurnalings
  MODIFY COLUMN debit DECIMAL(15,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN kredit DECIMAL(15,2) NOT NULL DEFAULT 0;

-- PostgreSQL
ALTER TABLE jurnalings
  ALTER COLUMN debit TYPE DECIMAL(15,2) USING debit::DECIMAL(15,2),
  ALTER COLUMN kredit TYPE DECIMAL(15,2) USING kredit::DECIMAL(15,2);
```

**Impact:** All 1,000+ journal entries now support proper SQL aggregation (`SUM`, `AVG`, `ORDER BY`).

## 9. Backup Strategy

| Component | Details |
|-----------|---------|
| **Tool** | `mysqldump` (MySQL) / `pg_dump` (PostgreSQL) |
| **Flags** | `--single-transaction --routines --triggers --events` |
| **Compression** | gzip |
| **Encryption** | AES-256-CBC via `openssl enc` with PBKDF2 |
| **Retention** | 30 days |
| **Script** | `docker/backup.sh` |

## 10. Dual-Engine Portability

DAPENSE is designed to run on both MySQL 8.4 and PostgreSQL 16 with zero code changes:

| Feature | MySQL | PostgreSQL |
|---------|-------|-----------|
| `DB_CONNECTION=mysql` | ✓ (default) | — |
| `DB_CONNECTION=pgsql` | — | ✓ (alternative) |
| `DECIMAL(15,2)` | ✓ native | ✓ native |
| `ENUM` (saldo_normal) | ✓ native | CHECK constraint |
| `ON DELETE CASCADE` | ✓ | ✓ |
| `BOOLEAN` | `TINYINT(1)` | `BOOLEAN` |
| Auto-increment | `AUTO_INCREMENT` | `SERIAL` / `BIGSERIAL` |

Portability achieved via Eloquent's query builder abstraction — no raw SQL in application code.
