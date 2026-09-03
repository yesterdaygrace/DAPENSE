-- =====================================================================
-- 01_views.sql — SYNTHETIC DEMO — anonymized from DAPENSE schema
-- Safe for public recruiter review. No PII / production data.
-- Source schema: database/seed_data_jurnal_coa.sql (17 header_coas, 100 coas, 54 saldo_awal, 1000 jurnalings)
--                + database/migrations/* (8 FKs, 3 UQs, NUMERIC(15,2) at 2026_08_03_000001)
-- Prereq: run migrations + seed (php artisan migrate --seed / psql -f seed_data_jurnal_coa.sql)
-- Dialects: MySQL 8.4 (docker-compose.yml:47) and PostgreSQL 16 (docker-compose.pgsql.yml:56) — blocks marked [PG] / [MySQL]
-- Docs companion: docs/sql-showcase.md, docs/database-deep-dive.md
-- =====================================================================

-- ---------------------------------------------------------------------
-- v_neraca_saldo — Trial Balance view (Neraca Saldo)
-- Mirrors: app/Http/Controllers/Base/NeracaSaldoController.php:107,135
--   SELECT coa_id, SUM(debit), SUM(kredit) GROUP BY coa_id
--   + saldo_awal left join for saldo_akhir = (debit-kredit)awal + (debit-kredit)mutasi
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS v_neraca_saldo;

-- [PostgreSQL 16]
CREATE OR REPLACE VIEW v_neraca_saldo AS
SELECT
    c.kode_akun,
    c.nama_akun,
    c.saldo_normal,
    h.kode_header,
    h.nama_header,
    j.periode_id,
    p.nama_periode,
    COALESCE(sa.debit, 0)::NUMERIC(15,2)  AS saldo_awal_debit,
    COALESCE(sa.kredit, 0)::NUMERIC(15,2) AS saldo_awal_kredit,
    SUM(j.debit)::NUMERIC(15,2)           AS mutasi_debit,
    SUM(j.kredit)::NUMERIC(15,2)          AS mutasi_kredit,
    (COALESCE(sa.debit,0) - COALESCE(sa.kredit,0) + SUM(j.debit) - SUM(j.kredit))::NUMERIC(15,2) AS saldo_akhir
FROM coas c
JOIN header_coas h        ON h.id = c.header_coa_id
JOIN jurnalings j         ON j.coa_id = c.id
JOIN periodes p           ON p.id = j.periode_id
LEFT JOIN saldo_awal sa   ON sa.coa_id = c.id AND sa.periode_id = j.periode_id
GROUP BY c.kode_akun, c.nama_akun, c.saldo_normal, h.kode_header, h.nama_header, j.periode_id, p.nama_periode, sa.debit, sa.kredit;

-- [MySQL 8.4] — same DDL without ::NUMERIC casts (NUMERIC already). If PG block above ran, drop/recreate without casts:
-- DROP VIEW IF EXISTS v_neraca_saldo;
-- CREATE VIEW v_neraca_saldo AS
-- SELECT c.kode_akun, c.nama_akun, c.saldo_normal, h.kode_header, h.nama_header,
--        j.periode_id, p.nama_periode,
--        COALESCE(sa.debit,0) AS saldo_awal_debit, COALESCE(sa.kredit,0) AS saldo_awal_kredit,
--        SUM(j.debit) AS mutasi_debit, SUM(j.kredit) AS mutasi_kredit,
--        (COALESCE(sa.debit,0)-COALESCE(sa.kredit,0)+SUM(j.debit)-SUM(j.kredit)) AS saldo_akhir
-- FROM coas c JOIN header_coas h ON h.id=c.header_coa_id
-- JOIN jurnalings j ON j.coa_id=c.id JOIN periodes p ON p.id=j.periode_id
-- LEFT JOIN saldo_awal sa ON sa.coa_id=c.id AND sa.periode_id=j.periode_id
-- GROUP BY c.kode_akun, c.nama_akun, c.saldo_normal, h.kode_header, h.nama_header, j.periode_id, p.nama_periode, sa.debit, sa.kredit;

-- Verify (expect <=100 rows — one per COA that has journals in periode 1):
-- SELECT * FROM v_neraca_saldo WHERE periode_id = 1 ORDER BY kode_akun LIMIT 20;


-- ---------------------------------------------------------------------
-- v_buku_besar — General Ledger view (Buku Besar) with running balance
-- Mirrors: app/Http/Controllers/Base/BukuBesarController.php:56,119,189 (EXTRACT MONTH, SUM(debit-kredit) window)
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS v_buku_besar;

-- [PostgreSQL 16] — window running balance per COA ordered by tanggal
CREATE OR REPLACE VIEW v_buku_besar AS
SELECT
    j.id,
    j.nomor_bukti,
    j.periode_id,
    p.nama_periode,
    j.tanggal,
    EXTRACT(MONTH FROM j.tanggal)::int AS bulan,
    c.kode_akun,
    c.nama_akun,
    h.nama_header,
    j.keterangan,
    j.debit::NUMERIC(15,2)  AS debit,
    j.kredit::NUMERIC(15,2) AS kredit,
    SUM(j.debit - j.kredit) OVER (PARTITION BY j.coa_id ORDER BY j.tanggal, j.id)::NUMERIC(15,2) AS saldo_berjalan
FROM jurnalings j
JOIN coas c        ON c.id = j.coa_id
JOIN header_coas h ON h.id = c.header_coa_id
JOIN periodes p    ON p.id = j.periode_id;

-- [MySQL 8.4] — same, without :: casts (window functions supported since 8.0):
-- DROP VIEW IF EXISTS v_buku_besar;
-- CREATE VIEW v_buku_besar AS
-- SELECT j.id, j.nomor_bukti, j.periode_id, p.nama_periode, j.tanggal,
--        EXTRACT(MONTH FROM j.tanggal) AS bulan, c.kode_akun, c.nama_akun, h.nama_header, j.keterangan,
--        j.debit AS debit, j.kredit AS kredit,
--        SUM(j.debit - j.kredit) OVER (PARTITION BY j.coa_id ORDER BY j.tanggal, j.id) AS saldo_berjalan
-- FROM jurnalings j JOIN coas c ON c.id=j.coa_id JOIN header_coas h ON h.id=c.header_coa_id JOIN periodes p ON p.id=j.periode_id;

-- Verify (expect 1000 rows = all journals):
-- SELECT kode_akun, COUNT(*) FROM v_buku_besar GROUP BY kode_akun ORDER BY kode_akun LIMIT 20;
-- SELECT * FROM v_buku_besar WHERE kode_akun='10010001' ORDER BY tanggal, id;
