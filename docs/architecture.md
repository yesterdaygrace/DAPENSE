# Architecture — DAPENSE

> **Dana Pensiun Sekolah Kristen Salatiga** — Pension Fund Accounting System

## 1. System Overview

DAPENSE is a server-rendered web application built on **Laravel 13 / PHP 8.3** with **Livewire 4** for reactive UI. It manages the general ledger, journal entries, trial balance, and balance reconciliation for a school pension fund.

```
┌─────────────────────────────────────────────────────────────┐
│                       CLIENT BROWSER                        │
│                  Alpine.js + Tailwind CSS 4                 │
│                     Livewire wire:snap                      │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP (port 8080)
┌────────────────────────▼────────────────────────────────────┐
│                       NGINX REVERSE PROXY                   │
│              Static assets · Security headers · FPM pass    │
└────────────────────────┬────────────────────────────────────┘
                         │ fastcgi :9000
┌────────────────────────▼────────────────────────────────────┐
│                     PHP-FPM 8.3 + OPcache                   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                  LARAVEL 13 FRAMEWORK                │   │
│  │  ┌────────────┐ ┌────────────┐ ┌─────────────────┐  │   │
│  │  │ Middleware  │ │ Livewire 4 │ │   Eloquent ORM  │  │   │
│  │  │            │ │ Components │ │   Models (8)    │  │   │
│  │  └──────┬─────┘ └─────┬──────┘ └────────┬────────┘  │   │
│  │         │              │                  │           │   │
│  │  ┌──────▼──────────────▼──────────────────▼────────┐  │   │
│  │  │              ROUTES (web.php + auth.php)         │  │   │
│  │  │    CheckRole · SecurityHeaders · PreventBfcache  │  │   │
│  │  └─────────────────────────────────────────────────┘  │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │ PDO                               │
│  ┌──────────────────────▼────────────────────────────────┐  │
│  │           MySQL 8.4  │  PostgreSQL 16                 │  │
│  │           (production)│  (portable alt)               │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## 2. Layered Architecture

| Layer | Location | Responsibility |
|-------|----------|---------------|
| **Transport** | `routes/web.php`, `routes/auth.php` | HTTP routing, middleware stack |
| **Presentation** | `app/Livewire/`, `resources/views/` | UI components, Blade templates |
| **Application** | Livewire `mount()`/`save()` methods | Orchestrate business logic |
| **Domain** | `app/Models/` (8 models) | Eloquent models, relationships, casts |
| **Data** | `database/migrations/` (19 files) | Schema DDL, constraints, indexes |
| **Infrastructure** | `docker/`, `Dockerfile` | Nginx, PHP-FPM, Redis, backups |

### 2.1 Request Lifecycle

```
Browser → Nginx → PHP-FPM → Laravel Kernel
  → SecurityHeaders middleware (global)
  → auth + verified middleware (route group)
  → CheckRole middleware (role:rootsuperuser, role:admin, etc.)
  → PreventBfcache middleware (no-cache alias)
  → Livewire full-page component
    → HasRole trait (role() / canAccess())
    → Policy check (8 Policies, 4 Gates)
    → Eloquent query → MySQL/PostgreSQL
    → Blade render → HTTP response
```

## 3. Frontend Stack

| Technology | Version | Role |
|-----------|---------|------|
| Livewire | 4.x | Reactive full-page components |
| Alpine.js | 3.x | Client-side interactivity |
| Tailwind CSS | 4.x | Utility-first styling |
| Vite | 8.x | Build tool / HMR |
| Lucide | 1.x | Icon library |

### 3.1 Livewire Components (12)

| Component | Purpose | Access |
|-----------|---------|--------|
| `Dashboard` | Role-aware dashboard | all roles |
| `COAWorkspace` | COA CRUD + import/export | root, admin, operator |
| `JournalEntry` | Journal entry form | root, admin, operator |
| `JurnalManager` | Journal management | root, admin, operator |
| `JurnalList` | Journal listing | root, admin, operator, bod |
| `BukuBesar` | General ledger | all roles |
| `NeracaSaldo` | Trial balance | all roles |
| `SaldoAwal` | Opening balances | root, admin, operator |
| `PeriodeManager` | Period CRUD | root, admin, operator |
| `OtorisatorManager` | Approver management | root, admin, operator |
| `UserManager` | User CRUD | root, admin |
| `Posting` | Period posting | root, admin |

### 3.2 Module Pages (View-only)

| Route | View | Purpose |
|-------|------|---------|
| `/master-data` | `modules.master-data.index` | Master data hub |
| `/transactions` | `modules.transactions.index` | Transaction hub |
| `/reports` | `modules.reports.index` | Reports hub |
| `/finance` | `modules.finance.index` | Finance hub |
| `/administration` | `modules.administration.index` | Admin hub |
| `/settings` | `modules.settings.index` | Settings hub |

## 4. Backend Stack

| Technology | Version | Role |
|-----------|---------|------|
| PHP | 8.3 | Runtime |
| Laravel | 13.x | Framework |
| Laravel Breeze | 2.x | Authentication scaffolding |
| Spatie Activity Log | 4.x | Audit trail |
| Maatwebsite Excel | 3.x | Import/export |
| mPDF | 8.x | PDF generation |
| Redis | 7.x | Cache / sessions (in-container) |

## 5. Data Layer

| Component | Details |
|-----------|---------|
| **Primary DB** | MySQL 8.4 (production) |
| **Alt DB** | PostgreSQL 16 (portable) |
| **Dev/Test** | SQLite (fallback) |
| **Session** | `database` driver |
| **Cache** | `database` driver |
| **Queue** | `database` driver |

## 6. Infrastructure

| Component | Details |
|-----------|---------|
| **Container** | `php:8.3-fpm` + Nginx + Redis (single container) |
| **Network** | `dapense-internal` (isolated bridge) |
| **Volumes** | `mysql_data`, `storage_data` |
| **Health** | `GET /health` → JSON `{"status":"ok"}` |
| **Security** | `read_only: true`, `no-new-privileges`, `cap_drop: ALL` |
| **Backups** | `mysqldump` → gzip → AES-256-CBC (30-day retention) |

## 7. Security Architecture

| Layer | Mechanism |
|-------|-----------|
| **Transport** | Nginx security headers (X-Frame-Options, CSP, etc.) |
| **Middleware** | `SecurityHeaders` (global), `CheckRole` (route-level) |
| **Auth** | Laravel Breeze (session-based) + email verification |
| **RBAC** | 4 roles × 8 Policies × 4 Gates |
| **Livewire** | `HasRole` trait with feature-level `canAccess()` |
| **Session** | Encrypted, database-backed, 120-min lifetime |
| **Password** | Bcrypt with 12 rounds |

## 8. Design Decisions

| Decision | Rationale |
|----------|-----------|
| Livewire over Inertia | Server-rendered reactive UI; no SPA complexity |
| MySQL as primary | Most common in Indonesian hosting; team familiarity |
| PostgreSQL as alt | Portable via docker-compose.pgsql.yml; better JSON support |
| Single-container Docker | Simplifies deployment; Redis + Nginx co-located |
| `database` for session/cache/queue | No external Redis dependency in production |
| Policy + Gate dual | Policies for model auth, Gates for feature-level permissions |
| `HasRole` trait | Livewire components check permissions without middleware |
| `read_only: true` container | Minimizes attack surface; tmpfs for mutable paths |
| AES-256-CBC backups | Encrypt-at-rest for pension fund compliance |
