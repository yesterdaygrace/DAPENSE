#!/bin/bash
set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-dapense}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/dapense_${TIMESTAMP}.sql.enc"

mkdir -p "${BACKUP_DIR}"

mysqldump \
    --host="${DB_HOST}" \
    --port="${DB_PORT}" \
    --user="${DB_USERNAME}" \
    --password="${DB_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    "${DB_DATABASE}" | \
gzip | \
openssl enc -aes-256-cbc -salt -pbkdf2 \
    -pass pass:"${BACKUP_ENCRYPTION_KEY}" \
    -out "${BACKUP_FILE}"

echo "Backup created: ${BACKUP_FILE}"

find "${BACKUP_DIR}" -name "dapense_*.sql.enc" -mtime +"${RETENTION_DAYS}" -delete
echo "Removed backups older than ${RETENTION_DAYS} days"
