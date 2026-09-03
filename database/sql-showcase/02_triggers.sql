-- =====================================================================
-- 02_triggers.sql — SYNTHETIC DEMO — anonymized from DAPENSE schema
-- Trigger: trg_jurnal_balance_check — validates double-entry per row (debit XOR kredit)
-- Replaces app-layer bccomp guard at app/Http/Controllers/Base/JurnalingController.php:467-468
--   if (bccomp(debitSum, kreditSum, 2) !== 0) throw
-- Here enforced at DB layer: one of debit/kredit must be >0 and the other =0, both NOT NULL, NUMERIC(15,2)
-- Prereq: jurnalings.debit/kredit already NUMERIC(15,2) NOT NULL DEFAULT 0 (2026_08_03_000001)
-- Dialects: MySQL 8.4 and PostgreSQL 16 — both blocks included, run the one for your engine
-- =====================================================================

-- =====================================================================
-- [MySQL 8.4]  (docker-compose.yml:47)
-- =====================================================================
DROP TRIGGER IF EXISTS trg_jurnal_balance_check;

DELIMITER $$

CREATE TRIGGER trg_jurnal_balance_check
BEFORE INSERT ON jurnalings
FOR EACH ROW
BEGIN
    -- enforce NOT NULL (defensive; column is NOT NULL but allow explicit check)
    IF NEW.debit IS NULL OR NEW.kredit IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'debit/kredit must be NOT NULL (NUMERIC 15,2)';
    END IF;
    -- enforce XOR: exactly one side > 0
    IF NOT ((NEW.debit > 0 AND NEW.kredit = 0) OR (NEW.debit = 0 AND NEW.kredit > 0)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Double-entry violation: exactly one of debit/kredit must be >0, other =0';
    END IF;
    -- enforce non-negative
    IF NEW.debit < 0 OR NEW.kredit < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'debit/kredit must be >= 0';
    END IF;
END$$

DELIMITER ;

-- Also guard UPDATEs
DROP TRIGGER IF EXISTS trg_jurnal_balance_check_upd;
DELIMITER $$
CREATE TRIGGER trg_jurnal_balance_check_upd
BEFORE UPDATE ON jurnalings
FOR EACH ROW
BEGIN
    IF NOT ((NEW.debit > 0 AND NEW.kredit = 0) OR (NEW.debit = 0 AND NEW.kredit > 0)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Double-entry violation on UPDATE';
    END IF;
END$$
DELIMITER ;

-- Verify MySQL:
-- INSERT INTO jurnalings (nomor_bukti, coa_id, periode_id, debit, kredit, tanggal, keterangan) VALUES ('TEST-001', 1, 1, 100000, 0, '2025-01-15', 'ok'); -- succeeds
-- INSERT INTO jurnalings (nomor_bukti, coa_id, periode_id, debit, kredit, tanggal, keterangan) VALUES ('TEST-002', 1, 1, 100000, 100000, '2025-01-15', 'fail'); -- SIGNAL 45000
-- INSERT INTO jurnalings (nomor_bukti, coa_id, periode_id, debit, kredit, tanggal, keterangan) VALUES ('TEST-003', 1, 1, 0, 0, '2025-01-15', 'fail'); -- SIGNAL 45000


-- =====================================================================
-- [PostgreSQL 16]  (docker-compose.pgsql.yml:56)
-- =====================================================================
-- Run this block on PostgreSQL instead of / in addition to MySQL block above.
-- PG needs a function + trigger.

DROP TRIGGER IF EXISTS trg_jurnal_balance_check ON jurnalings;
DROP FUNCTION IF EXISTS fn_jurnal_balance_check();

CREATE OR REPLACE FUNCTION fn_jurnal_balance_check()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.debit IS NULL OR NEW.kredit IS NULL THEN
        RAISE EXCEPTION 'debit/kredit must be NOT NULL (NUMERIC 15,2)';
    END IF;
    IF NOT ((NEW.debit > 0 AND NEW.kredit = 0) OR (NEW.debit = 0 AND NEW.kredit > 0)) THEN
        RAISE EXCEPTION 'Double-entry violation: exactly one of debit/kredit must be >0, other =0 (got debit=%, kredit=%)', NEW.debit, NEW.kredit;
    END IF;
    IF NEW.debit < 0 OR NEW.kredit < 0 THEN
        RAISE EXCEPTION 'debit/kredit must be >= 0';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_jurnal_balance_check
BEFORE INSERT OR UPDATE ON jurnalings
FOR EACH ROW EXECUTE FUNCTION fn_jurnal_balance_check();

-- Verify PG:
-- INSERT INTO jurnalings (nomor_bukti, coa_id, periode_id, debit, kredit, tanggal, keterangan) VALUES ('TEST-001', 1, 1, 100000, 0, '2025-01-15', 'ok'); -- ok
-- INSERT INTO jurnalings (nomor_bukti, coa_id, periode_id, debit, kredit, tanggal, keterangan) VALUES ('TEST-002', 1, 1, 100, 100, '2025-01-15', 'fail'); -- RAISE
