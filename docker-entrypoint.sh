#!/bin/sh
set -e

APP_DIR="/var/www/html"

# ── Logging helpers ──────────────────────────────────────────────────
log()   { echo "[DAPENSE] $(date '+%Y-%m-%d %H:%M:%S') $*"; }
error() { echo "[DAPENSE] ERROR: $(date '+%Y-%m-%d %H:%M:%S') $*" >&2; }

# ── FR-3: Validate required environment variables ────────────────────
log "Validating environment variables..."
: "${APP_KEY:?APP_KEY is required — set it in Railway dashboard and run 'php artisan key:generate'}"
: "${APP_ENV:?APP_ENV is required}"
: "${APP_DEBUG:?APP_DEBUG is required}"
: "${APP_URL:?APP_URL is required}"
: "${DB_CONNECTION:?DB_CONNECTION is required}"
: "${DB_HOST:?DB_HOST is required}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"
log "All required environment variables are set."

# ── FR-1: Verify vendor directory exists ─────────────────────────────
log "Verifying Composer vendor directory..."
if [ ! -d "${APP_DIR}/vendor" ]; then
    error "vendor directory not found at ${APP_DIR}/vendor."
    error "Run 'composer install --no-dev --optimize-autoloader' during Docker build."
    exit 1
fi
log "Vendor directory found."

# ── FR-6: Verify storage directories and permissions ─────────────────
log "Setting up storage directories and permissions..."
mkdir -p "${APP_DIR}/storage/framework/views" \
    "${APP_DIR}/storage/framework/cache/data" \
    "${APP_DIR}/storage/framework/sessions" \
    "${APP_DIR}/storage/logs"
chown -R www-data:www-data "${APP_DIR}/storage" 2>/dev/null || true
chown -R www-data:www-data "${APP_DIR}/bootstrap/cache" 2>/dev/null || true

# Verify storage is writable
if ! su -s /bin/sh www-data -c "test -w ${APP_DIR}/storage" 2>/dev/null; then
    error "Storage directory is not writable by www-data user."
    exit 1
fi
log "Storage directories ready."

# ── FR-4: Configure Nginx with Railway PORT ──────────────────────────
PORT="${PORT:-8080}"
log "Configuring Nginx to listen on port ${PORT}..."

# Generate nginx config in writable /tmp (container may be read-only)
NGINX_CONF="/tmp/nginx-default.conf"
cp "${APP_DIR}/docker/nginx.conf" "${NGINX_CONF}"
sed -i "s/listen 80;/listen ${PORT};/" "${NGINX_CONF}"
sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/" "${NGINX_CONF}"

# Wrap server block in full nginx.conf for standalone -c usage
NGINX_CONF="/tmp/nginx-wrapper.conf"
cat > "${NGINX_CONF}" <<WRAPPER
events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    sendfile on;
    types_hash_max_size 2048;
    server_tokens off;
    client_max_body_size 20M;

$(cat /tmp/nginx-default.conf)
}
WRAPPER

# Create nginx temp directories (tmpfs mounts are empty at boot)
mkdir -p /var/lib/nginx/body /var/lib/nginx/proxy \
         /var/lib/nginx/fastcgi /var/lib/nginx/client_body
chown -R www-data:www-data /var/lib/nginx /var/log/nginx

# ── FR-2: Run Laravel optimization at runtime ────────────────────────
log "Running Laravel optimization..."
php "${APP_DIR}/artisan" optimize --no-interaction 2>&1 | tee -a /var/log/nginx/dapense_error.log || true

# ── FR-6 / FR-7: Verify Laravel can bootstrap ────────────────────────
log "Verifying Laravel bootstrap..."
php "${APP_DIR}/artisan" about --no-interaction 2>&1 | tee -a /var/log/nginx/dapense_error.log || {
    error "Laravel failed to bootstrap. Check the logs above."
    exit 1
}

# ── Start PHP-FPM ────────────────────────────────────────────────────
log "Starting PHP-FPM..."
php-fpm -D

# ── Start Nginx in foreground ───────────────────────────────────────
log "Starting Nginx..."
exec nginx -c "${NGINX_CONF}" -g 'pid /tmp/nginx.pid; daemon off;'
