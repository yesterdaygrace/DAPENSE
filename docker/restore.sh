#!/bin/bash
set -euo pipefail

# MySQL restore — decrypt → gunzip → mysql
# Usage: ./docker/restore.sh /backups/dapense_20260101_020000.sql.enc
#   or:  cat dump.sql | ./docker/restore.sh --stdin
# Env: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, BACKUP_ENCRYPTION_KEY

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-dapense}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

if [ -z "${BACKUP_ENCRYPTION_KEY:-}" ]; then
  echo "ERROR: BACKUP_ENCRYPTION_KEY is required" >&2
  exit 1
fi

restore_stream() {
  mysql --host="${DB_HOST}" --port="${DB_PORT}" --user="${DB_USERNAME}" --password="${DB_PASSWORD}" "${DB_DATABASE}"
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
