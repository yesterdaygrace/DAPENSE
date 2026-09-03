# Stack Versions — DAPENSE

> Pinned toolchain versions for reproducible builds.

## 1. Runtime

| Component | Version | Constraint | Source |
|-----------|---------|-----------|--------|
| PHP | 8.3 | `^8.3` | composer.json |
| Laravel | 13.x | `^13.0` | composer.json |
| MySQL | 8.4 | pinned | docker-compose.yml |
| PostgreSQL | 16 | pinned | docker-compose.pgsql.yml |
| Redis | 7.x | latest | Dockerfile (in-container) |
| Node.js | 20+ | — | Vite 8 requirement |
| Nginx | latest | — | php:8.3-fpm base image |

## 2. PHP Dependencies (composer.json)

### 2.1 Production Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/framework` | `^13.0` | Core framework |
| `laravel/breeze` | `^2.0` | Authentication scaffolding |
| `laravel/tinker` | `^3.0` | REPL / debugging |
| `livewire/livewire` | `^4.0` | Reactive UI components |
| `maatwebsite/excel` | `^3.1` | Import/export Excel |
| `mpdf/mpdf` | `^8.2` | PDF generation |
| `spatie/laravel-activitylog` | `^4.12` | Audit trail |

### 2.2 Development Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `pestphp/pest` | `^4.0` | Test framework |
| `pestphp/pest-plugin-laravel` | `^4.0` | Pest Laravel integration |
| `laravel/pint` | `^1.13` | Code style (PSR-12) |
| `larastan/larastan` | `^3.0` | Static analysis (PHPStan) |
| `laravel/sail` | `^1.26` | Docker dev environment |
| `barryvdh/laravel-debugbar` | `^4.0` | Debug toolbar |
| `fakerphp/faker` | `^1.23` | Test data generation |
| `mockery/mockery` | `^1.6` | Mock objects |
| `nunomaduro/collision` | `^8.6` | Error reporting |

## 3. Frontend Dependencies (package.json)

### 3.1 Production

| Package | Version | Purpose |
|---------|---------|---------|
| `tailwindcss` | `^4.3.3` | Utility-first CSS |
| `@tailwindcss/vite` | `^4.3.3` | Vite integration |
| `alpinejs` | `^3.15.12` | Client-side interactivity |
| `lucide` | `^1.25.0` | Icon library |
| `jquery` | `^4.0.0` | DOM manipulation (legacy) |
| `axios` | `^1.18.1` | HTTP client |

### 3.2 Dev Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `vite` | `^8.0` | Build tool / HMR |
| `laravel-vite-plugin` | `^3.0` | Laravel Vite integration |

## 4. Docker Images

| Image | Version | Role |
|-------|---------|------|
| `php:8.3-fpm` | 8.3 | Application runtime |
| `mysql:8.4` | 8.4 | Primary database |
| `postgres:16-alpine` | 16 | Alternative database |
| `composer:latest` | — | Build-time dependency install |
| `redis:7` | 7 | In-container cache/session |

## 5. PHP Extensions

Installed in Dockerfile:

| Extension | Purpose |
|-----------|---------|
| `pdo_mysql` | MySQL driver |
| `mbstring` | Multibyte string handling |
| `exif` | Image metadata |
| `pcntl` | Process control |
| `bcmath` | Arbitrary precision math |
| `gd` | Image processing |
| `zip` | ZIP archive support |

## 6. System Packages

Installed in Dockerfile:

| Package | Purpose |
|---------|---------|
| `nginx` | Web server |
| `redis-server` | In-container cache |
| `libpng-dev` | PNG support for GD |
| `libonig-dev` | Multibyte string support |
| `libxml2-dev` | XML support |
| `zip` / `unzip` | Archive utilities |
| `curl` | HTTP client (health checks) |
| `libzip-dev` | ZIP extension support |
| `gettext-base` | Localization utilities |

## 7. Build Tools

| Tool | Version | Purpose |
|------|---------|---------|
| Composer | latest | PHP dependency management |
| Vite | 8.x | Frontend build / HMR |
| Laravel Pint | 1.x | Code style enforcement |
| Larastan | 3.x | Static analysis |
| Pest | 4.x | Test framework |

## 8. Composer Scripts

| Script | Command | Purpose |
|--------|---------|---------|
| `composer test` | `pest` | Run tests |
| `composer test-coverage` | `pest --coverage` | Tests with coverage |
| `composer lint` | `pint --test` | Check code style |
| `composer lint-fix` | `pint` | Fix code style |
| `composer analyse` | `phpstan analyse` | Static analysis |
| `composer analyse-verbose` | `phpstan analyse --debug` | Verbose analysis |
| `composer check` | `lint + analyse + test` | Full CI check |

## 9. NPM Scripts

| Script | Command | Purpose |
|--------|---------|---------|
| `npm run dev` | `vite` | Dev server with HMR |
| `npm run build` | `vite build` | Production build |

## 10. Version Pinning Strategy

| Strategy | Rationale |
|----------|-----------|
| Exact pin (Docker images) | Reproducible builds; avoid surprise upgrades |
| Caret range (`^`) | PHP/Node packages: allow minor/patch updates |
| Stable only (`minimum-stability: stable`) | No alpha/beta in production |
| `prefer-stable: true` | Prefer stable releases when multiple match |

## 11. Upgrade Considerations

| Component | Risk | Notes |
|-----------|------|-------|
| PHP 8.3 → 8.4 | Low | Minor version; check deprecated features |
| Laravel 13 → 14 | Medium | Major version; review upgrade guide |
| Livewire 4 → 5 | Medium | Check API changes |
| Tailwind 4 → 5 | Low | Utility classes mostly stable |
| MySQL 8.4 → 9.0 | Medium | Check ENUM and JSON changes |
| Vite 8 → 9 | Low | Build config may change |
