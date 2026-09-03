# ROADMAP — DAPENSE

> Planned work, feature backlog, and improvement areas.

## 1. Current State (v1.0)

| Feature | Status |
|---------|--------|
| General Ledger (Buku Besar) | ✓ Complete |
| Journal Entries (5 types) | ✓ Complete |
| Trial Balance (Neraca Saldo) | ✓ Complete |
| Opening Balances (Saldo Awal) | ✓ Complete |
| Chart of Accounts (COA) | ✓ Complete |
| Period Management | ✓ Complete |
| RBAC (4 roles) | ✓ Complete |
| Authentication (Breeze) | ✓ Complete |
| Import/Export (Excel) | ✓ Complete |
| PDF Reports (mPDF) | ✓ Complete |
| Audit Trail (Spatie) | ✓ Complete |
| Docker Deployment | ✓ Complete |
| Database Backup (AES-256) | ✓ Complete |
| MySQL 8.4 + PostgreSQL 16 | ✓ Complete |
| Livewire 4 UI | ✓ Complete |

## 2. Phase 2: Security Hardening

| Priority | Task | Effort |
|----------|------|--------|
| P0 | Enable HTTPS + HSTS header | Small |
| P0 | Add global rate limiting | Small |
| P0 | Enforce password complexity rules | Small |
| P1 | Implement 2FA for admin/root roles | Medium |
| P1 | Add CSP reporting endpoint | Small |
| P1 | Enable database encryption at rest | Infrastructure |
| P2 | Add RBAC audit logging | Medium |

## 3. Phase 3: Testing & Quality

| Priority | Task | Effort |
|----------|------|--------|
| P0 | Write Policy unit tests (8 policies) | Medium |
| P0 | Write Gate unit tests (4 gates) | Small |
| P0 | Write Livewire component tests (12 components) | Large |
| P1 | Add Feature tests for import/export | Medium |
| P1 | Add Feature tests for posting workflow | Medium |
| P2 | Set up GitHub Actions CI pipeline | Medium |
| P2 | Add code coverage reporting | Small |

## 4. Phase 4: API & Integration

| Priority | Task | Effort |
|----------|------|--------|
| P1 | RESTful API for mobile/external access | Large |
| P1 | API authentication (Sanctum tokens) | Medium |
| P2 | Webhook support for journal events | Medium |
| P2 | LDAP/SSO integration | Large |
| P3 | Third-party accounting software import | Large |

## 5. Phase 5: Performance & Scale

| Priority | Task | Effort |
|----------|------|--------|
| P1 | Database query optimization (N+1 audit) | Medium |
| P1 | Redis caching for dashboard aggregates | Medium |
| P2 | Queue workers for export/PDF generation | Medium |
| P2 | Database connection pooling | Infrastructure |
| P3 | Horizontal scaling (load balancer) | Infrastructure |

## 6. Phase 6: UX & Features

| Priority | Task | Effort |
|----------|------|--------|
| P1 | Balance Sheet (Neraca) report | Medium |
| P1 | Income Statement (Laba Rugi) report | Medium |
| P1 | Cash Flow Statement (Arus Kas) report | Medium |
| P2 | Dashboard charts (period-over-period) | Medium |
| P2 | Multi-company support | Large |
| P2 | Fiscal year management | Medium |
| P3 | Role-based dashboard customization | Large |
| P3 | Notification system (email/in-app) | Medium |

## 7. Phase 7: Operations

| Priority | Task | Effort |
|----------|------|--------|
| P1 | Automated backup verification | Medium |
| P1 | Monitoring (Prometheus/Grafana) | Medium |
| P2 | Log aggregation (Loki/ELK) | Medium |
| P2 | Alerting (UptimeRobot / PagerDuty) | Small |
| P3 | Blue-green deployment | Infrastructure |
| P3 | Database migration rollback strategy | Medium |

## 8. Known Issues

| Issue | Severity | Notes |
|-------|----------|-------|
| LSP errors in Pest tests | Low | Static analysis doesn't understand Pest dynamic properties |
| Legacy controller routes duplicated | Medium | Both Livewire and controller routes exist for same pages |
| No global rate limiting | High | Only export/import/posting throttled |
| CSP allows `unsafe-inline` | Medium | Required for Livewire inline scripts |
| `products` table unused | Low | Legacy table from template; not part of accounting domain |

## 9. Tech Debt

| Item | Priority | Impact |
|------|----------|--------|
| Consolidate legacy + Livewire routes | Medium | Reduces route table from 120+ to ~40 |
| Remove unused `products` table | Low | Cleaner schema |
| Extract HasRole permission matrix to config | Medium | Easier to maintain |
| Add PHPStan level 6+ | Medium | Better static analysis |
| Migrate jQuery to Alpine.js | Low | Remove unused dependency |

## 10. Success Metrics

| Metric | Current | Target |
|--------|---------|--------|
| Test coverage | ~30% | 80%+ |
| PHPStan level | 5 | 6+ |
| Response time (p95) | <500ms | <200ms |
| Backup success rate | Manual | 100% automated |
| Security headers score | A | A+ |
| Zero critical vulnerabilities | ✓ | Maintain |
