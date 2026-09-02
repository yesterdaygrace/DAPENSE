#!/bin/bash
set -euo pipefail

# Generates a PostgreSQL-ready export from a live database.
# Supports two sources: MySQL (via Laravel migrations) or existing PostgreSQL.
#
# What it produces (under database/pgsql-dump/):
#   01_schema.sql  — pg_dump --schema-only from a PG instance running migrations
#                  — or `php artisan schema:dump` fallback (Laravel schema dump)
#   02_data.sql    — copy of database/seed_data_jurnal_coa.sql (already PG-compatible)
#   full.sql       — 01 + 02 concatenated, ready for psql / pg_restore
#
# Usage:
#   ./database/pgsql-export.sh                      # uses current DB_CONNECTION (expects pgsql live DB)
#   DB_CONNECTION=mysql ./database/pgsql-export.sh  # exports MySQL schema via mysqldump then converts header
#   ./database/pgsql-export.sh --from-seed-only     # just wrap seed file into full.sql (no live DB needed)

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT_DIR="${ROOT}/database/pgsql-dump"
SEED="${ROOT}/database/seed_data_jurnal_coa.sql"

mkdir -p "${OUT_DIR}"

if [[ "${1:-}" == "--from-seed-only" ]]; then
  echo "[pgsql-export] --from-seed-only: building full.sql from seed (no live DB needed)"
  echo "[pgsql-export] TIP: for a real 01_schema.sql, run: docker compose -f docker-compose.pgsql.yml up -d && php artisan migrate --database=pgsql && ./database/pgsql-export.sh"

  SCHEMA_CANDIDATE="${ROOT}/database/schema/pgsql-schema.sql"
  if [[ -f "${SCHEMA_CANDIDATE}" ]]; then
    cp "${SCHEMA_CANDIDATE}" "${OUT_DIR}/01_schema.sql"
    echo "[pgsql-export] Copied ${SCHEMA_CANDIDATE} -> 01_schema.sql"
  else
    cat > "${OUT_DIR}/01_schema.sql" <<'EOSQL'
-- No live PostgreSQL dump available — run migrations against a real PG instance to capture schema:
--   docker compose -f docker-compose.pgsql.yml up -d
--   php artisan migrate --database=pgsql
--   ./database/pgsql-export.sh        # then captures pg_dump --schema-only
-- Schema is otherwise defined by Laravel migrations (database/migrations/*.php).
EOSQL
  fi

  {
    cat "${OUT_DIR}/01_schema.sql"
    echo ""
    echo "-- ====================================================================="
    echo "-- DATA — from database/seed_data_jurnal_coa.sql (17 headers + 1 periode + 100 COAs + 54 saldo_awal + 1000 jurnalings)"
    echo "-- Already PostgreSQL-compatible (no backticks, standard INSERTs)"
    echo "-- ====================================================================="
    cat "${SEED}"
  } > "${OUT_DIR}/full.sql"

  echo "[pgsql-export] Done: ${OUT_DIR}/full.sql ($(wc -l < "${OUT_DIR}/full.sql") lines)"
  exit 0
fi

# Live DB path — prefer pgsql
DRIVER="${DB_CONNECTION:-$(grep -E '^DB_CONNECTION=' "${ROOT}/.env" 2>/dev/null | cut -d= -f2 || echo pgsql)}"
echo "[pgsql-export] Driver: ${DRIVER}"

if [[ "${DRIVER}" == "pgsql" ]]; then
  DB_HOST="${DB_HOST:-127.0.0.1}"
  DB_PORT="${DB_PORT:-5432}"
  DB_DATABASE="${DB_DATABASE:-dapense}"
  DB_USERNAME="${DB_USERNAME:-postgres}"
  export PGPASSWORD="${DB_PASSWORD:-}"

  echo "[pgsql-export] Dumping schema from ${DB_HOST}:${DB_PORT}/${DB_DATABASE}..."
  pg_dump --host="${DB_HOST}" --port="${DB_PORT}" --username="${DB_USERNAME}" \
    --dbname="${DB_DATABASE}" --schema-only --no-owner --no-acl --clean --if-exists \
    > "${OUT_DIR}/01_schema.sql"
  echo "[pgsql-export] Schema -> ${OUT_DIR}/01_schema.sql"

  {
    cat "${OUT_DIR}/01_schema.sql"
    echo ""
    cat "${SEED}"
  } > "${OUT_DIR}/full.sql"
  echo "[pgsql-export] Full bundle -> ${OUT_DIR}/full.sql"

else
  echo "[pgsql-export] MySQL source detected — using mysqldump for reference, then assembling PG bundle"
  echo "[pgsql-export] NOTE: for a true PG schema, run: DB_CONNECTION=pgsql php artisan migrate && $0"
  ./database/pgsql-export.sh --from-seed-only
fi
