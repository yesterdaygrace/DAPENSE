-- =====================================================================
-- 04_complex_joins.sql — SYNTHETIC DEMO — 3 complex JOINs for recruiters
-- Runnable vs seed_data_jurnal_coa.sql (17 headers / 100 COAs / 500 vouchers / 1000 journals)
-- Dialect: MySQL 8.4 + PostgreSQL 16 (EXTRACT, SUM, JOIN, GROUP BY, HAVING, LEFT JOIN, window)
-- Sources mirrored: NeracaSaldoController:135-136, BukuBesarController:56,119,189
-- =====================================================================

-- ---------------------------------------------------------------------
-- Q1 — Buku Besar per COA × Bulan (General Ledger monthly mutation)
-- JOIN: jurnalings → coas → header_coas → periodes
-- Agg: SUM(debit/kredit) GROUP BY kode_akun, bulan  HAVING mutasi !=0  ORDER
-- PG + MySQL: EXTRACT(MONTH FROM tanggal) works on both (PG-native per docs/postgres-migration.md:104)
-- ---------------------------------------------------------------------
-- Expected: ~100 COAs × months present (~12) but sparse — at most 100*12 rows, practically ~80-100 rows for seed year
SELECT
    c.kode_akun,
    c.nama_akun,
    h.nama_header,
    EXTRACT(MONTH FROM j.tanggal)::int AS bulan,          -- PG; MySQL: EXTRACT(MONTH FROM j.tanggal)
    p.nama_periode,
    COUNT(*)::int                 AS jml_baris,
    SUM(j.debit)::NUMERIC(15,2)   AS total_debit,
    SUM(j.kredit)::NUMERIC(15,2)  AS total_kredit,
    (SUM(j.debit) - SUM(j.kredit))::NUMERIC(15,2) AS mutasi_net
FROM jurnalings j
JOIN coas c        ON c.id = j.coa_id
JOIN header_coas h ON h.id = c.header_coa_id
JOIN periodes p    ON p.id = j.periode_id
WHERE j.periode_id = 1
GROUP BY c.kode_akun, c.nama_akun, h.nama_header, bulan, p.nama_periode
HAVING SUM(j.debit) - SUM(j.kredit) <> 0
ORDER BY c.kode_akun, bulan;

-- MySQL variant (drop casts):
-- SELECT c.kode_akun, c.nama_akun, h.nama_header, EXTRACT(MONTH FROM j.tanggal) AS bulan, p.nama_periode,
--        COUNT(*) AS jml_baris, SUM(j.debit) AS total_debit, SUM(j.kredit) AS total_kredit, SUM(j.debit)-SUM(j.kredit) AS mutasi_net
-- FROM jurnalings j JOIN coas c ON c.id=j.coa_id JOIN header_coas h ON h.id=c.header_coa_id JOIN periodes p ON p.id=j.periode_id
-- WHERE j.periode_id=1 GROUP BY c.kode_akun, bulan HAVING mutasi_net<>0 ORDER BY c.kode_akun, bulan;


-- ---------------------------------------------------------------------
-- Q2 — Neraca Saldo lengkap (Trial Balance with opening balance LEFT JOIN)
-- LEFT JOIN keeps COAs with zero mutation (COA tanpa mutasi tetap muncul) — recruiter signal: understanding LEFT JOIN vs INNER
-- Mirrors: NeracaSaldoController:107 (COALESCE saldo_awal) + saldo_akhir formula
-- ---------------------------------------------------------------------
-- Expected: 100 rows (all COAs) for periode 1 — 54 with saldo_awal, rest 0
SELECT
    c.kode_akun,
    c.nama_akun,
    c.saldo_normal,
    h.kode_header,
    COALESCE(sa.debit,0)::NUMERIC(15,2)  AS saldo_awal_debit,
    COALESCE(sa.kredit,0)::NUMERIC(15,2) AS saldo_awal_kredit,
    COALESCE(m.mutasi_debit,0)::NUMERIC(15,2)  AS mutasi_debit,
    COALESCE(m.mutasi_kredit,0)::NUMERIC(15,2) AS mutasi_kredit,
    (COALESCE(sa.debit,0)-COALESCE(sa.kredit,0)+COALESCE(m.mutasi_debit,0)-COALESCE(m.mutasi_kredit,0))::NUMERIC(15,2) AS saldo_akhir,
    CASE WHEN COALESCE(m.cnt,0) = 0 THEN 'TIDAK ADA MUTASI' ELSE 'ADA MUTASI' END AS status_mutasi
FROM coas c
JOIN header_coas h ON h.id = c.header_coa_id
LEFT JOIN saldo_awal sa ON sa.coa_id = c.id AND sa.periode_id = 1
LEFT JOIN (
    SELECT coa_id, COUNT(*) AS cnt, SUM(debit) AS mutasi_debit, SUM(kredit) AS mutasi_kredit
    FROM jurnalings WHERE periode_id = 1 GROUP BY coa_id
) m ON m.coa_id = c.id
ORDER BY c.kode_akun;

-- Verify totals tie: SUM(saldo_akhir) across ASET+KEWAJIBAN+MODAL should reconcile (accounting equation)


-- ---------------------------------------------------------------------
-- Q3 — Laba-Rugi (Income Statement) via header hierarchy
-- JOIN header_coas self-hierarchy: PENDAPATAN (4) & BEBAN (5) groups
-- Agg per kelompok akun level 1, then grand total — tests hierarchical JOIN understanding
-- ---------------------------------------------------------------------
-- Expected: 2 header rows (PENDAPATAN, BEBAN) + detail per COA; grand net = Laba/Rugi
WITH coa_mutasi AS (
    SELECT c.id, c.kode_akun, c.nama_akun, c.header_coa_id, SUM(j.debit) AS d, SUM(j.kredit) AS k
    FROM coas c LEFT JOIN jurnalings j ON j.coa_id=c.id AND j.periode_id=1
    GROUP BY c.id, c.kode_akun, c.nama_akun, c.header_coa_id
),
header_rollup AS (
    SELECT
        h.kode_header,
        h.nama_header,
        h.level,
        SUM(cm.k) AS total_pendapatan_kredit,   -- PENDAPATAN normal Kredit
        SUM(cm.d) AS total_beban_debit          -- BEBAN normal Debit
    FROM header_coas h
    LEFT JOIN coa_mutasi cm ON cm.header_coa_id = h.id
    WHERE h.kode_header IN ('4','5')  -- PENDAPATAN, BEBAN (seed_data_jurnal_coa.sql:11ff)
    GROUP BY h.kode_header, h.nama_header, h.level
)
-- Detail per akun within PENDAPATAN/BEBAN
SELECT cm.kode_akun, cm.nama_akun, h.nama_header,
       COALESCE(cm.d,0)::NUMERIC(15,2) AS debit,
       COALESCE(cm.k,0)::NUMERIC(15,2) AS kredit
FROM coa_mutasi cm JOIN header_coas h ON h.id=cm.header_coa_id
WHERE h.kode_header IN ('4.1','4.2','5.1','5.2','5.3')
ORDER BY cm.kode_akun
-- ; then rollup:
-- SELECT * FROM header_rollup;
-- Grand: SELECT SUM(total_pendapatan_kredit) - SUM(total_beban_debit) AS laba_rugi FROM header_rollup;

-- MySQL CTE variant identical (MySQL 8.4 supports WITH). Remove ::NUMERIC casts for MySQL.

-- ---------------------------------------------------------------------
-- Bonus — Voucher balance audit (every nomor_bukti must balance)
-- Finds data quality issues; should return 0 rows on clean seed
-- ---------------------------------------------------------------------
SELECT nomor_bukti, periode_id, SUM(debit)::NUMERIC(15,2) AS d, SUM(kredit)::NUMERIC(15,2) AS k
FROM jurnalings
GROUP BY nomor_bukti, periode_id
HAVING ABS(SUM(debit) - SUM(kredit)) > 0.005
ORDER BY nomor_bukti;
-- Expected: 0 rows (500 vouchers balanced). If >0, seed or trigger failed.
