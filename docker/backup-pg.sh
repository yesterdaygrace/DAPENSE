#!/bin/bash
set -euo pipefail

# PostgreSQL backup — pg_dump → gzip → openssl AES-256-CBC
# Usage: DB_CONNECTION=pgsql ./docker/backup-pg.sh
# Env: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, BACKUP_ENCRYPTION_KEY
# Mirrors docker/backup.sh (MySQL) but uses pg_dump.

BACKUP_DIR="${BACKUP_DIR:-/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-dapense}"
DB_USERNAME="${DB_USERNAME:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-}"

if [ -z "${BACKUP_ENCRYPTION_KEY:-}" ]; then
  echo "ERROR: BACKUP_ENCRYPTION_KEY is required" >&2
  exit 1
fi

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/dapense_${TIMESTAMP}.sql.enc"

mkdir -p "${BACKUP_DIR}"

export PGPASSWORD="${DB_PASSWORD}"

pg_dump \
    --host="${DB_HOST}" \
    --port="${DB_PORT}" \
    --username="${DB_USERNAME}" \
    --dbname="${DB_DATABASE}" \
    --no-owner \
    --no-acl \
    --clean --if-exists \
    | gzip \
    | openssl enc -aes-256-cbc -salt -pbkdf2 \
        -pass pass:"${BACKUP_ENCRYPTION_KEY}" \
        -out "${BACKUP_FILE}"

echo "Backup created: ${BACKUP_FILE}"

find "${BACKUP_DIR}" -name "dapense_*.sql.enc" -mtime +"${RETENTION_DAYS}" -delete
echo "Removed backups older than ${RETENTION_DAYS} days"
