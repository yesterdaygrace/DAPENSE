-- =====================================================================
-- 03_procedures.sql — SYNTHETIC DEMO — sp_posting_periode
-- Period closing: validates all vouchers balanced (SUM debit = SUM kredit per nomor_bukti)
-- then snapshots Neraca Saldo (neraca_saldos) — mirrors app/Http/Controllers/rootsuperuser/PostingControllerRootSuperuser.php:29,88-95
--   saldo_akhir = (saldo_awal.debit - saldo_awal.kredit) + (SUM(j.debit) - SUM(j.kredit))
-- Safe for public review: operates on dummy seed_data_jurnal_coa.sql only.
-- =====================================================================

-- =====================================================================
-- [MySQL 8.4]
-- =====================================================================
DROP PROCEDURE IF EXISTS sp_posting_periode;

DELIMITER $$

CREATE PROCEDURE sp_posting_periode(IN p_periode_id BIGINT)
BEGIN
    DECLARE unbalanced INT DEFAULT 0;
    -- Validate: every voucher (nomor_bukti) in this periode must balance
    SELECT COUNT(*) INTO unbalanced
    FROM (
        SELECT nomor_bukti, SUM(debit) AS d, SUM(kredit) AS k
        FROM jurnalings
        WHERE periode_id = p_periode_id
        GROUP BY nomor_bukti
        HAVING ABS(SUM(debit) - SUM(kredit)) > 0.005
    ) v;

    IF unbalanced > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posting aborted: one or more vouchers not balanced (debit != kredit)';
    END IF;

    START TRANSACTION;
        -- Upsert neraca_saldos snapshot per COA for this periode
        -- Delete old snapshot for idempotency, then insert fresh
        DELETE FROM neraca_saldos WHERE periode_id = p_periode_id;

        INSERT INTO neraca_saldos (coa_id, periode_id, saldo_awal, mutasi_debit, mutasi_kredit, saldo_akhir, created_at, updated_at)
        SELECT
            c.kode_akun,
            p_periode_id,
            COALESCE(sa.debit,0) - COALESCE(sa.kredit,0),
            COALESCE(m.mutasi_debit,0),
            COALESCE(m.mutasi_kredit,0),
            (COALESCE(sa.debit,0)-COALESCE(sa.kredit,0)) + (COALESCE(m.mutasi_debit,0)-COALESCE(m.mutasi_kredit,0)),
            NOW(), NOW()
        FROM coas c
        LEFT JOIN saldo_awal sa ON sa.coa_id = c.id AND sa.periode_id = p_periode_id
        LEFT JOIN (
            SELECT coa_id, SUM(debit) AS mutasi_debit, SUM(kredit) AS mutasi_kredit
            FROM jurnalings WHERE periode_id = p_periode_id GROUP BY coa_id
        ) m ON m.coa_id = c.id;
    COMMIT;
END$$

DELIMITER ;

-- Verify MySQL: CALL sp_posting_periode(1);  SELECT periode_id, COUNT(*) FROM neraca_saldos GROUP BY periode_id;


-- =====================================================================
-- [PostgreSQL 16]
-- =====================================================================
DROP PROCEDURE IF EXISTS sp_posting_periode(BIGINT);
DROP FUNCTION IF EXISTS sp_posting_periode(BIGINT);

CREATE OR REPLACE PROCEDURE sp_posting_periode(p_periode_id BIGINT)
LANGUAGE plpgsql AS $$
DECLARE unbalanced INT;
BEGIN
    SELECT COUNT(*) INTO unbalanced
    FROM (
        SELECT nomor_bukti
        FROM jurnalings WHERE periode_id = p_periode_id
        GROUP BY nomor_bukti
        HAVING ABS(SUM(debit) - SUM(kredit)) > 0.005
    ) v;

    IF unbalanced > 0 THEN
        RAISE EXCEPTION 'Posting aborted: % voucher(s) not balanced in periode %', unbalanced, p_periode_id;
    END IF;

    DELETE FROM neraca_saldos WHERE periode_id = p_periode_id;

    INSERT INTO neraca_saldos (coa_id, periode_id, saldo_awal, mutasi_debit, mutasi_kredit, saldo_akhir, created_at, updated_at)
    SELECT
        c.kode_akun,
        p_periode_id,
        COALESCE(sa.debit,0) - COALESCE(sa.kredit,0),
        COALESCE(m.mutasi_debit,0),
        COALESCE(m.mutasi_kredit,0),
        (COALESCE(sa.debit,0)-COALESCE(sa.kredit,0)) + (COALESCE(m.mutasi_debit,0)-COALESCE(m.mutasi_kredit,0)),
        NOW(), NOW()
    FROM coas c
    LEFT JOIN saldo_awal sa ON sa.coa_id=c.id AND sa.periode_id=p_periode_id
    LEFT JOIN (SELECT coa_id, SUM(debit) AS mutasi_debit, SUM(kredit) AS mutasi_kredit FROM jurnalings WHERE periode_id=p_periode_id GROUP BY coa_id) m ON m.coa_id=c.id;
    -- PROCEDURE in PG is transactional by caller; COMMIT handled by caller or use CALL
END;
$$;

-- Verify PG: CALL sp_posting_periode(1);  SELECT periode_id, COUNT(*) FROM neraca_saldos GROUP BY periode_id;
-- For older PG function style: SELECT sp_posting_periode(1);
