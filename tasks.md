# DAPENSE Security Hardening PRD
Version: 2.0

Project
DAPENSE (Dana Pensiun Sekolah Kristen Salatiga)

Framework
Laravel 13

Priority
Critical

Objective

Eliminate all Critical and High vulnerabilities while adopting Laravel 13 and OWASP best practices suitable for a production financial ERP.

---

# Success Criteria

- Zero Critical vulnerabilities
- Zero High vulnerabilities
- All financial transactions atomic
- Complete audit trail
- Secure authentication
- Secure authorization
- Secure infrastructure
- Automated security testing
- Production-ready deployment

---

# Phase 1
Financial Transaction Integrity

Priority

★★★★★ Critical

Problem

Financial operations are not wrapped inside database transactions.

Risk

Partial writes.

Example

Journal inserted

↓

Ledger fails

↓

Balance not updated

↓

Database corrupted

Implementation

Every financial workflow must use

DB::transaction()

Modules

- Journal
- Cash
- Bank
- General Ledger
- Pension
- Payroll
- Reports requiring updates

Tasks

- Locate every create/update/delete financial operation
- Wrap in DB::transaction()
- Roll back on failure
- Log exceptions
- Test rollback

Acceptance

✓ No partial financial writes

---

# Phase 2
Authorization Refactor

Priority

★★★★★

Problem

Role middleware only.

Implementation

Create

Policies

- JournalPolicy
- LedgerPolicy
- ReportPolicy
- UserPolicy
- EmployeePolicy
- SettingPolicy

Replace

if(role)

with

authorize()

Acceptance

Every CRUD operation protected by Policies.

---

# Phase 3
Remove Route Duplication

Priority

★★★★★

Problem

Duplicate route groups.

Implementation

Replace

/rootsuperuser

/operator

/bod

with

shared controllers

+

Policies

Acceptance

Single route definition.

---

# Phase 4
Fix IDOR

Priority

★★★★★

Implementation

Every resource

↓

Policy

↓

Ownership

↓

Permission

↓

404 or 403

Acceptance

Changing IDs in URL never exposes unauthorized data.

---

# Phase 5
Mass Assignment

Priority

★★★★★

Replace

$request->all()

with

$request->validated()

or DTO.

Create

DTO

- StoreJournalData
- UpdateJournalData
- StoreUserData
- StoreEmployeeData

Acceptance

No request()->all()

---

# Phase 6
Livewire Security

Priority

★★★★★

Review

- Public properties
- Locked properties
- Validation
- Authorization
- File uploads
- Component actions

Move business logic

↓

Services

Acceptance

Thin Livewire components.

---

# Phase 7
Secure File Uploads

Priority

★★★★★

Implement

- MIME validation
- Extension validation
- Max size
- Random filename
- Storage outside public
- Download controller
- Authorization

Reject

php

exe

js

svg (unless sanitized)

Acceptance

Uploads inaccessible directly.

---

# Phase 8
Audit Logging

Priority

★★★★★

Using

Spatie Activitylog

Log

- Login
- Logout
- Journal
- Ledger
- Export
- Import
- Settings
- Permission changes
- Failed authorization

Do not log

Passwords

Tokens

Secrets

Acceptance

Every sensitive action traceable.

---

# Phase 9
Security Headers

Priority

★★★★☆

Configure Nginx

Add

Content-Security-Policy

Strict-Transport-Security

X-Frame-Options

Permissions-Policy

Referrer-Policy

X-Content-Type-Options

Acceptance

Mozilla Observatory grade A.

---

# Phase 10
Docker Hardening

Priority

★★★★★

Tasks

Run as

www-data

Read-only filesystem where possible

Limit capabilities

Use .dockerignore

Hide ports

Redis private network

No root containers

Acceptance

No container runs as root.

---

# Phase 11
Redis Security

Priority

★★★★☆

Tasks

Require password

Internal Docker network

Memory limits

Persistence

Queue isolation

Acceptance

Redis inaccessible publicly.

---

# Phase 12
Environment Security

Priority

★★★★★

Verify

APP_DEBUG=false

APP_ENV=production

Secure APP_KEY

Encrypted backups

No .env committed

Acceptance

Production ready.

---

# Phase 13
Dependency Security

Priority

★★★★☆

Run

composer audit

npm audit

Update vulnerable packages

Remove abandoned packages

Acceptance

Zero known vulnerabilities.

---

# Phase 14
Rate Limiting

Priority

★★★★☆

Protect

Login

Export

Import

Password reset

Livewire endpoints

Acceptance

No brute-force attacks.

---

# Phase 15
Validation

Priority

★★★★★

Every request uses

Form Request

No validation inside controllers.

Acceptance

100% validation coverage.

---

# Phase 16
Exception Handling

Priority

★★★★☆

Hide

Stack traces

SQL errors

Internal paths

Create

Friendly error pages

Acceptance

No sensitive information leaks.

---

# Phase 17
Business Logic Refactor

Priority

★★★★★

Move logic

Controller

↓

Service

↓

Repository

↓

Model

Acceptance

Controllers under 200 lines.

---

# Phase 18
Testing

Priority

★★★★★

Pest

Feature tests

Authorization tests

Transaction rollback

File uploads

Policies

Validation

Acceptance

Minimum 90% coverage for business logic.

---

# Phase 19
Static Analysis

Priority

★★★★☆

Run

PHPStan Level 9

Larastan

Laravel Pint

Fix

Every warning.

Acceptance

Zero analysis errors.

---

# Phase 20
ERP Integrity Rules

Priority

★★★★★

Validate

Debit == Credit

Unique journal numbers

Closed periods immutable

Ledger consistency

Balance reconciliation

Foreign keys

No orphan records

Acceptance

Accounting integrity guaranteed.

---

# Phase 21
Concurrency & Race Conditions

Priority

★★★★★

Protect

Double-click submissions

Concurrent journal posting

Queue duplication

Duplicate exports

Implementation

Database transactions

Unique constraints

Cache locks

Redis locks

Idempotency keys

Acceptance

Concurrent requests cannot corrupt financial data.

---

# Phase 22
Backup & Recovery

Priority

★★★★☆

Implement

Daily encrypted database backups

Restore testing

Disaster recovery documentation

Retention policy

Acceptance

Recovery tested successfully.

---

# Phase 23
CI/CD Security

Priority

★★★★☆

GitHub Actions pipeline

Run

- composer audit
- npm audit
- phpstan
- larastan
- pest
- pint

Block merges on failure.

Acceptance

No insecure code reaches main.

---

# Deliverables

- Hardened Laravel application
- Security report
- Updated Docker configuration
- Updated Nginx configuration
- Automated security pipeline
- Security documentation
- OWASP compliance checklist
- ERP integrity verification report

---

# Final Acceptance

✔ Zero Critical findings

✔ Zero High findings

✔ Laravel 13 Best Practices

✔ OWASP Top 10 compliant

✔ Secure Docker deployment

✔ Secure Redis

✔ Secure Livewire components

✔ Financial transactions atomic

✔ Complete audit logging

✔ PHPStan Level 9 passes

✔ Pest tests pass

✔ Composer audit clean

✔ Production-ready financial ERP
