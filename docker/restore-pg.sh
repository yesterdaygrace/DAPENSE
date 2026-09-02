#!/bin/bash
set -euo pipefail

# PostgreSQL restore — decrypt → gunzip → psql
# Usage: ./docker/restore-pg.sh /backups/dapense_20260101_020000.sql.enc
#   or:  cat dump.sql | ./docker/restore-pg.sh --stdin
# Env: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, BACKUP_ENCRYPTION_KEY

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-dapense}"
DB_USERNAME="${DB_USERNAME:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-}"

if [ -z "${BACKUP_ENCRYPTION_KEY:-}" ]; then
  echo "ERROR: BACKUP_ENCRYPTION_KEY is required" >&2
  exit 1
fi

export PGPASSWORD="${DB_PASSWORD}"

restore_stream() {
  psql --host="${DB_HOST}" --port="${DB_PORT}" --username="${DB_USERNAME}" --dbname="${DB_DATABASE}" -v ON_ERROR_STOP=1
}

if [ "${1:-}" = "--stdin" ]; then
  echo "[restore] Reading plain SQL from stdin..."
  restore_stream
  echo "[restore] Done."
  exit 0
fi

INPUT="${1:-}"
if [ -z "${INPUT}" ]; then
  echo "Usage: $0 <backup.sql.enc>  or  $0 --stdin < dump.sql" >&2
  exit 1
fi

if [ ! -f "${INPUT}" ]; then
  echo "ERROR: file not found: ${INPUT}" >&2
  exit 1
fi

echo "[restore] Decrypting ${INPUT}..."
openssl enc -d -aes-256-cbc -salt -pbkdf2 \
  -pass pass:"${BACKUP_ENCRYPTION_KEY}" \
  -in "${INPUT}" \
  | gunzip \
  | restore_stream

echo "[restore] Done — restored ${INPUT} into ${DB_DATABASE} @ ${DB_HOST}:${DB_PORT}"
