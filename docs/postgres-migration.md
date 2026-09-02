# PostgreSQL Migration — Dapense

> Source: MySQL 8.4 (docker-compose.yml) → Target: PostgreSQL 16 (docker-compose.pgsql.yml)

## 1) What changed for PostgreSQL

| Area | MySQL | PostgreSQL |
|------|-------|------------|
| Migration `2026_08_03` | `MODIFY COLUMN debit NUMERIC(15,2)` | Driver-aware: `ALTER COLUMN ... TYPE NUMERIC(15,2) USING ...::numeric` (fixed) |
| Config | `config/database.php` `mysql` conn | `pgsql` conn already present — just switch `DB_CONNECTION=pgsql` |
| Schema | `BIGINT UNSIGNED`, backticks, `MODIFY` | Standard `BIGINT`, no backticks, `ALTER TYPE` |
| Seed data | `database/seed_data_jurnal_coa.sql` (17 headers + 1 periode + 100 COAs + 54 saldo_awal + 1000 jurnalings) | **Already PG-compatible** — plain `INSERT INTO ... VALUES` with no MySQL-only syntax |
| Docker | `mysql:8.4` service | `postgres:16-alpine` service (`docker-compose.pgsql.yml`) |
| Backup | `docker/backup.sh` (mysqldump) | `docker/backup-pg.sh` (pg_dump + gzip + AES-256-CBC) |
| Restore | `docker/restore.sh` (mysql) | `docker/restore-pg.sh` (psql) |

No other migrations need changes — all other `Schema::create` / `Schema::table` calls use Laravel's agnostic blueprint.

## 2) Quick start (fresh PG install)

```bash
cp .env.example .env
# edit .env: DB_CONNECTION=pgsql, DB_PORT=5432 (or 15432 on host), DB_USERNAME=postgres, DB_PASSWORD=...

# Option A: separate pgsql compose file (recommended)
docker compose -f docker-compose.pgsql.yml up --build -d

# wait for healthy
docker inspect --format='{{.State.Health.Status}}' dapense-postgres

docker compose -f docker-compose.pgsql.yml exec app php artisan migrate --force
docker compose -f docker-compose.pgsql.yml exec app php artisan db:seed --class=DatabaseSeeder  # if you use seeders
# or load the bundled seed directly:
docker compose -f docker-compose.pgsql.yml exec postgres psql -U postgres -d dapense -f /path/to/database/seed_data_jurnal_coa.sql

# verify
docker compose -f docker-compose.pgsql.yml exec postgres psql -U postgres -d dapense -c "SELECT count(*) FROM jurnalings;"
```

## 3) Migrate existing MySQL data to PostgreSQL

### 3a — Export from MySQL (produce neutral dump)
```bash
# Inside running MySQL stack
mysqldump --host=127.0.0.1 --port=13306 --user=root --password="$DB_PASSWORD" \
  --single-transaction --routines --triggers --events \
  --no-create-db dapense > /tmp/dapense_mysql.sql

# Alternatively use encrypted backup + decrypt:
./docker/backup.sh                          # → /backups/dapense_*.sql.enc
openssl enc -d -aes-256-cbc -salt -pbkdf2 -pass pass:"$BACKUP_ENCRYPTION_KEY" \
  -in /backups/dapense_*.sql.enc | gunzip > /tmp/dapense_mysql.sql
```

### 3b — Convert & load into PostgreSQL

Simplest (Dapense-specific): migrations already define the schema — just migrate fresh and load seed/data:

```bash
DB_CONNECTION=pgsql DB_HOST=127.0.0.1 DB_PORT=15432 php artisan migrate --force
psql "host=127.0.0.1 port=15432 dbname=dapense user=postgres password=$DB_PASSWORD" \
  -f database/seed_data_jurnal_coa.sql

# For production data (not just seed): use pgloader (handles MySQL→PG type mapping automatically)
#   pgloader mysql://root:$DB_PASSWORD@127.0.0.1:13306/dapense \
#            pgsql://postgres:$DB_PASSWORD@127.0.0.1:15432/dapense
```

Or generate the PG bundle without a live PG:
```bash
./database/pgsql-export.sh --from-seed-only   # → database/pgsql-dump/full.sql
psql "host=... port=15432 dbname=dapense ..." -f database/pgsql-dump/full.sql
```

## 4) Backup & restore on PostgreSQL

```bash
# backup (cron-friendly; same retention/encryption contract as MySQL path)
BACKUP_ENCRYPTION_KEY=... DB_HOST=dapense-postgres DB_PORT=5432 ./docker/backup-pg.sh
# restore
BACKUP_ENCRYPTION_KEY=... DB_HOST=127.0.0.1 DB_PORT=15432 ./docker/restore-pg.sh /backups/dapense_20260101_020000.sql.enc
# plain SQL stdin
cat /tmp/dump.sql | BACKUP_ENCRYPTION_KEY=... ./docker/restore-pg.sh --stdin

# MySQL restore (symmetric)
BACKUP_ENCRYPTION_KEY=... ./docker/restore.sh /backups/dapense_...sql.enc
```

## 5) Generate a versioned export artifact

```bash
./database/pgsql-export.sh                     # live PG → database/pgsql-dump/{01_schema.sql,full.sql}
./database/pgsql-export.sh --from-seed-only    # no live DB → schema placeholder + seed → full.sql
ls -lh database/pgsql-dump/
```

Check into git or attach to a release: `database/pgsql-dump/full.sql` is the single-file PostgreSQL export.

## 6) Compatibility notes

- `jurnalings.debit/kredit` are `NUMERIC(15,2)` on both engines; the migration is now driver-aware.
- `neraca_saldos.coa_id` is a VARCHAR FK to `coas.kode_akun` — valid on both engines.
- `otorizators.nomor_bukti` unique handling: migration drops unique then re-adds after jurnalings switchover — PostgreSQL respects the same flow.
- `EXTRACT(MONTH FROM tanggal)` used in Livewire components is already PostgreSQL-native (MySQL also supports it).
- No `RLS` policies exist — not needed for PG migration.

## 7) Rollback to MySQL

```bash
docker compose -f docker-compose.pgsql.yml down
docker compose up --build -d          # back to mysql:8.4
php artisan migrate --force
```

Both stacks share `storage_data` volume; database volumes are separate (`mysql_data` vs `postgres_data`).
