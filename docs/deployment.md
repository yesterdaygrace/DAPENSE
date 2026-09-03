# Deployment — DAPENSE

> Docker deployment, CI/CD, and operations runbook.

## 1. Prerequisites

| Requirement | Version | Purpose |
|------------|---------|---------|
| Docker | 24+ | Container runtime |
| Docker Compose | v2 | Multi-container orchestration |
| Git | 2.x | Source control |

## 2. Local Development

### 2.1 Quick Start (Docker)

```bash
# Clone
git clone https://github.com/yesterdaygrace/DAPENSE.git
cd DAPENSE

# Configure
cp .env.example .env
php artisan key:generate

# Start (MySQL)
docker compose up -d

# Access
open http://localhost:8080
```

### 2.2 Alternative: PostgreSQL

```bash
# Use PostgreSQL compose file
docker compose -f docker-compose.pgsql.yml up -d

# Update .env
DB_CONNECTION=pgsql
DB_HOST=dapense-pgsql
DB_PORT=5432
DB_DATABASE=dapense
DB_USERNAME=root
DB_PASSWORD=secret
```

### 2.3 Laravel Sail (Dev)

```bash
# Start with Sail
./vendor/bin/sail up -d

# Run commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

## 3. Docker Architecture

### 3.1 Production Container (Single Container)

```
┌─────────────────────────────────────────┐
│           dapense-app (php:8.3-fpm)     │
│  ┌─────────┐  ┌──────────┐  ┌────────┐  │
│  │  Nginx  │  │ PHP-FPM  │  │ Redis  │  │
│  │  :8080  │  │  :9000   │  │ :6379  │  │
│  └─────────┘  └──────────┘  └────────┘  │
└──────────────────┬──────────────────────┘
                   │
         ┌─────────▼─────────┐
         │   dapense-mysql   │
         │     (8.4)         │
         └───────────────────┘
```

### 3.2 Container Security

| Setting | Value | Purpose |
|---------|-------|---------|
| `read_only: true` | Immutable filesystem | Prevent runtime modification |
| `cap_drop: ALL` | Drop all Linux capabilities | Minimal privilege |
| `cap_add` | NET_BIND_SERVICE, SETGID, SETUID, CHOWN, DAC_OVERRIDE | Required for Nginx + www-data |
| `no-new-privileges` | true | Prevent privilege escalation |
| `tmpfs` | 8 mount points | Mutable paths (logs, cache, sessions) |

### 3.3 Network Isolation

```yaml
networks:
  internal:
    name: dapense-internal
    # No external network — MySQL is NOT exposed to host
```

## 4. Dockerfile Breakdown

### 4.1 Build Stages

| Stage | Action |
|-------|--------|
| Base | Install system packages + PHP extensions |
| Copy | Application code + Composer binary |
| Nginx | Configure nginx.conf + remove default site |
| Storage | Create storage directories + set permissions |
| Dependencies | `composer install --no-dev --optimize-autoloader` |
| Optimize | `config:cache`, `route:cache`, `view:cache`, `event:cache` |
| Health | `HEALTHCHECK` curl to `/health` |

### 4.2 Entrypoint Script

The `docker-entrypoint.sh` performs:

1. **Environment validation** — requires APP_KEY, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
2. **Vendor verification** — ensures `vendor/` directory exists
3. **Storage setup** — creates directories, sets www-data ownership
4. **Nginx configuration** — copies and patches `nginx.conf` with Railway PORT
5. **Laravel optimization** — runs `artisan optimize`
6. **Bootstrap verification** — runs `artisan about` to confirm app boots
7. **Redis start** — starts in-container Redis with optional password
8. **PHP-FPM start** — starts in background
9. **Nginx start** — runs in foreground (PID 1)

## 5. Environment Variables

### 5.1 Required

| Variable | Example | Notes |
|----------|---------|-------|
| `APP_KEY` | `base64:...` | Generated via `php artisan key:generate` |
| `APP_ENV` | `production` | Application environment |
| `APP_DEBUG` | `false` | Disable debug mode in production |
| `APP_URL` | `https://dapense.up.railway.app` | Application URL |
| `DB_CONNECTION` | `mysql` | Database driver |
| `DB_HOST` | `mysql` | Database host |
| `DB_DATABASE` | `dapense` | Database name |
| `DB_USERNAME` | `root` | Database user |
| `DB_PASSWORD` | `secret` | Database password |

### 5.2 Optional

| Variable | Default | Purpose |
|----------|---------|---------|
| `PORT` | 8080 | Nginx listen port |
| `REDIS_PASSWORD` | — | Redis auth |
| `BACKUP_ENCRYPTION_KEY` | — | AES-256-CBC backup encryption |
| `BACKUP_DIR` | /backups | Backup storage path |
| `RETENTION_DAYS` | 30 | Backup retention |

## 6. Health Check

```
GET /health → 200 OK

{
  "status": "ok",
  "timestamp": "2026-09-03T12:00:00+00:00",
  "app": "DAPENSE",
  "env": "production"
}
```

Docker health check:
```yaml
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT:-8080}/health || exit 1
```

## 7. Backup & Recovery

### 7.1 Backup

```bash
# Manual backup
./docker/backup.sh

# Automated (cron)
0 2 * * * /path/to/docker/backup.sh
```

**Pipeline:** `mysqldump` → `gzip` → `openssl enc -aes-256-cbc` → `.sql.enc`

### 7.2 Restore

```bash
# Decrypt and restore
openssl enc -aes-256-cbc -d -salt -pbkdf2 \
  -pass pass:"${BACKUP_ENCRYPTION_KEY}" \
  -in backup.sql.enc | gunzip | mysql -u root -p dapense
```

### 7.3 Retention

- Automatic cleanup of backups older than 30 days
- Configurable via `RETENTION_DAYS` environment variable

## 8. CI/CD

### 8.1 GitHub Actions (Recommended)

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: composer lint
      - run: composer analyse
      - run: composer test

  build:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: docker build -t dapense .
```

### 8.2 Railway Deployment

```bash
# Railway auto-detects Dockerfile
# Set environment variables in Railway dashboard:
#   APP_KEY, APP_ENV=production, APP_DEBUG=false
#   DB_CONNECTION=mysql, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#   APP_URL=https://your-app.up.railway.app
```

## 9. Operations Checklist

### 9.1 First Deploy

- [ ] Set `APP_KEY` via `php artisan key:generate`
- [ ] Configure database connection in `.env`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan db:seed`
- [ ] Verify `GET /health` returns 200
- [ ] Set up backup cron job
- [ ] Enable HTTPS (reverse proxy or Railway auto-SSL)

### 9.2 Ongoing Operations

| Task | Frequency | Command |
|------|-----------|---------|
| Backup | Daily (cron) | `docker/backup.sh` |
| Log rotation | Weekly | Docker log driver |
| Dependency updates | Monthly | `composer update` / `npm update` |
| Security audit | Monthly | `composer audit` |
| Database optimization | Monthly | `OPTIMIZE TABLE` / `VACUUM` |

## 10. Troubleshooting

| Issue | Solution |
|-------|----------|
| Container won't start | Check `docker logs dapense-app` |
| Database connection refused | Verify MySQL is running: `docker ps` |
| 502 Bad Gateway | PHP-FPM not running; check entrypoint logs |
| Storage permission errors | Run `docker exec dapense-app chown -R www-data:www-data storage` |
| Health check fails | Check `curl http://localhost:8080/health` inside container |
| Redis connection refused | Check Redis started in entrypoint logs |
