# Entity-Relationship Diagram — DAPENSE

> Visual model of all database tables, relationships, and constraints.

## 1. ER Diagram (Mermaid)

```mermaid
erDiagram
    USERS {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        varchar usertype "rootsuperuser|admin|operator|bod"
        tinyint status "1=active, 0=inactive"
        varchar image
        timestamp email_verified_at
    }

    PERIODES {
        bigint id PK
        varchar nama_periode
        date tanggal_awal
        date tanggal_akhir
        boolean is_rekap
    }

    HEADER_COAS {
        bigint id PK
        varchar kode_header UK
        varchar nama_header
        int level
        bigint parent_id FK "self-ref"
    }

    COAS {
        bigint id PK
        varchar kode_akun UK
        varchar nama_akun
        enum saldo_normal "debet|kredit"
        varchar kategori
        int level
        bigint header_coa_id FK
    }

    JURNALINGS {
        bigint id PK
        date tanggal_jurnal
        varchar nomor_bukti
        text keterangan
        varchar kategori_jurnal "kas keluar|bank masuk|bank keluar|memorial|memorial penutup"
        decimal debit
        decimal kredit
        bigint coa_id FK
        bigint periode_id FK
    }

    SALDO_AWAL {
        bigint id PK
        bigint coa_id FK
        date tanggal_saldo
        decimal debit
        decimal kredit
        bigint periode_id FK
    }

    NERACA_SALDOS {
        bigint id PK
        bigint coa_id FK
        bigint periode_id FK
        int month
        decimal debit
        decimal kredit
        decimal balance
        decimal saldo_awal
    }

    OTORISATORS {
        bigint id PK
        varchar nama_otorisator
        varchar jabatan_otorisator
    }

    HEADER_COAS ||--o{ HEADER_COAS : "parent_id"
    HEADER_COAS ||--o{ COAS : "has"
    COAS ||--o{ JURNALINGS : "records"
    COAS ||--o{ SALDO_AWAL : "has"
    COAS ||--o{ NERACA_SALDOS : "reports"
    PERIODES ||--o{ JURNALINGS : "contains"
    PERIODES ||--o{ SALDO_AWAL : "defines"
    PERIODES ||--o{ NERACA_SALDOS : "summarizes"
```

## 2. Relationship Flowchart

```mermaid
flowchart TB
    subgraph "Spine (Core Accounting)"
        HC[HeaderCOA] -->|parent_id self-ref| HC
        HC -->|header_coa_id FK CASCADE| COA[COA]
        COA -->|coa_id FK CASCADE| J[Jurnaling]
        COA -->|coa_id FK CASCADE| SA[SaldoAwal]
        COA -->|coa_id FK CASCADE| NS[NeracaSaldo]
        P[Periode] -->|periode_id FK CASCADE| J
        P -->|periode_id FK CASCADE| SA
        P -->|periode_id FK CASCADE| NS
    end

    subgraph "Support Tables"
        U[User] -.- J
        O[Otorisator] -.- P
    end

    style HC fill:#e1f5fe,stroke:#0288d1
    style COA fill:#e8f5e9,stroke:#388e3c
    style J fill:#fff3e0,stroke:#f57c00
    style SA fill:#fce4ec,stroke:#c62828
    style NS fill:#f3e5f5,stroke:#7b1fa2
    style P fill:#e0f2f1,stroke:#00796b
```

## 3. Relationship Catalogue

### 3.1 Self-Referencing

| Relationship | FK | Cardinality | On Delete |
|-------------|-----|-------------|-----------|
| HeaderCOA → HeaderCOA | `parent_id → id` | 0..N → 0..1 | SET NULL |

### 3.2 One-to-Many

| Parent | Child | FK | Cardinality | On Delete |
|--------|-------|-----|-------------|-----------|
| HeaderCOA | COA | `header_coa_id → id` | 1 → 0..N | CASCADE |
| COA | Jurnaling | `coa_id → id` | 1 → 0..N | CASCADE |
| COA | SaldoAwal | `coa_id → id` | 1 → 0..N | CASCADE |
| COA | NeracaSaldo | `coa_id → id` | 1 → 0..N | CASCADE |
| Periode | Jurnaling | `periode_id → id` | 1 → 0..N | CASCADE |
| Periode | SaldoAwal | `periode_id → id` | 1 → 0..N | CASCADE |
| Periode | NeracaSaldo | `periode_id → id` | 1 → 0..N | CASCADE |

### 3.3 Key Design Patterns

| Pattern | Implementation |
|---------|---------------|
| **Hierarchical COA** | `header_coas.parent_id` self-referencing FK enables multi-level account grouping (Asset → Current Assets → Cash) |
| **Double-entry spine** | `jurnalings.debit` + `jurnalings.kredit` per COA per period — balanced at trigger level |
| **Period isolation** | All financial data (`jurnaling`, `saldo_awal`, `neraca_saldo`) scoped to `periode_id` |
| **Snapshot pattern** | `neraca_saldos` stores pre-computed period-end balances for fast report queries |
| **Natural key uniqueness** | `header_coas.kode_header` and `coas.kode_akun` are UNIQUE — human-readable identifiers |
| **Cascade deletion** | Deleting a COA cascades to all journal lines, opening balances, and trial balance records |
| **Approver metadata** | `otorisators` is standalone — referenced by name in journal approvals, not FK-linked |

## 4. Data Flow

```
                    ┌──────────────┐
                    │  HeaderCOA   │  ← Account grouping hierarchy
                    └──────┬───────┘
                           │ 1:N
                    ┌──────▼───────┐
                    │     COA      │  ← 100 accounts ( kode_akun UNIQUE )
                    └──────┬───────┘
                           │
              ┌────────────┼────────────┐
              │ 1:N        │ 1:N        │ 1:N
       ┌──────▼──────┐ ┌──▼────────┐ ┌──▼──────────┐
       │  Jurnaling   │ │ SaldoAwal │ │ NeracaSaldo │
       │  (1000+)     │ │ (54)      │ │ (computed)  │
       └──────┬───────┘ └──┬────────┘ └──┬──────────┘
              │            │              │
              └────────────┼──────────────┘
                           │ N:1
                    ┌──────▼───────┐
                    │   Periode    │  ← Accounting period
                    └──────────────┘
```

## 5. Eloquent Relationship Map

```php
// HeaderCOA
headerCoa->parent()    // BelongsTo HeaderCOA
headerCoa->children()  // HasMany HeaderCOA
headerCoa->coas()      // HasMany COA

// COA
coa->headerCoa()       // BelongsTo HeaderCOA
coa->jurnalings()      // HasMany Jurnaling

// Jurnaling
jurnaling->coa()       // BelongsTo COA
jurnaling->periode()   // BelongsTo Periode

// SaldoAwal
saldoAwal->coa()       // BelongsTo COA
saldoAwal->periode()   // BelongsTo Periode

// NeracaSaldo
neracaSaldo->coa()     // BelongsTo COA (by kode_akun)
neracaSaldo->periode() // BelongsTo Periode
```

## 6. Seed Data Distribution

| Table | Row Count | Source |
|-------|-----------|--------|
| `header_coas` | 17 | JurnalCoaSeeder |
| `coas` | 100 | JurnalCoaSeeder |
| `jurnalings` | 1,000+ | JurnalCoaSeeder |
| `saldo_awal` | 54 | JurnalCoaSeeder |
| `periodes` | varies | PeriodeManager |
| `otorisators` | varies | OtorisatorManager |
| `users` | 4 (role admin) | UsersTableSeeder |
