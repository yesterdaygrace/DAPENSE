# Upgrade Plan: wasas Dependencies

**Status**: Awaiting research results  
**Scope**: Full upgrade to latest (all packages)  
**Date**: 2026-07-20  

## Current → Target

### PHP / Composer

| Package | Current | Target | Gap |
|---|---|---|---|
| `laravel/framework` | ^11.9 → v11.53.1 | ^13.0 | 2 majors |
| `livewire/livewire` | ^3.0 → v3.8.2 | ^4.0 | 1 major |
| `pestphp/pest` | ^2.0 → v2.36.1 | ^4.0 | 2 majors |
| `pestphp/pest-plugin-laravel` | ^2.0 → v2.4.0 | ^4.0 | 2 majors |
| `larastan/larastan` | ^2.0 → v2.11.2 | ^3.0 | 1 major |
| `laravel/tinker` | ^2.9 → v2.11.1 | ^3.0 | 1 major |
| `barryvdh/laravel-debugbar` | ^3.15 → v3.16.5 | ^4.0 | 1 major |
| `nunomaduro/collision` | ^8.0 → v8.5.0 | ^8.9 | patch |
| `laravel/pint` | ^1.13 → v1.29.1 | ^1.29 | patch |
| `laravel/sail` | ^1.26 → v1.60.0 | ^1.63 | patch |
| `maatwebsite/excel` | ^3.1 | latest | check |
| `mpdf/mpdf` | ^8.2 | latest | check |
| `spatie/laravel-activitylog` | ^4.12 | latest | check |

### Node / NPM

| Package | Current | Target | Gap |
|---|---|---|---|
| `vite` | ^5.0 → v5.3.1 | ^8.0 | 3 majors |
| `laravel-vite-plugin` | ^1.0 → v1.0.4 | ^3.0 | 2 majors |
| `tailwindcss` | ^4.3.2 | ^4.3.3 | patch |
| `@tailwindcss/vite` | ^4.3.2 | ^4.3.3 | patch |
| `alpinejs` | ^3.4.2 → v3.14.0 | ^3.15 | minor |
| `axios` | ^1.6.4 → v1.7.2 | ^1.18 | minor |
| `lucide` | ^1.24.0 | ^1.25.0 | minor |
| `jquery` | ^4.0.0 | ^4.0.0 | current |

### Infrastructure

| Item | Current | Target | Notes |
|---|---|---|---|
| Docker PHP | 8.2-fpm | 8.3+? | Check Laravel 13 req |
| Node (local) | v24.13.0 | — | Already high |
| Docker MySQL | 8.0 | — | No change needed |
| Docker Redis | 7-alpine | — | No change needed |

## Execution Steps

1. Update composer.json constraints
2. Run `composer update` with careful testing
3. Fix any breaking changes
4. Update package.json constraints
5. Run `npm update`
6. Fix Vite/laravel-vite-plugin changes
7. Run test suite
8. Update Dockerfile if PHP version changes
