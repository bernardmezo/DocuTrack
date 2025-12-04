-- ===============================================================================
-- Migration: Add JSON Column for Staged Disbursement Feature
-- ===============================================================================
-- Purpose: Support for multi-stage fund disbursement in DocuTrack system
-- 
-- Instead of creating a new table, we add a JSON column to existing tbl_kegiatan
-- to store disbursement stage details. This approach:
-- - Reduces database complexity
-- - Avoids unnecessary JOINs
-- - Keeps related data together
-- - Follows principle of data locality
--
-- JSON Structure:
-- [
--   {"tahap": 1, "tanggal": "2025-01-15", "persentase": 50, "jumlah": 5000000, "status": "scheduled"},
--   {"tahap": 2, "tanggal": "2025-02-15", "persentase": 50, "jumlah": 5000000, "status": "scheduled"}
-- ]
--
-- Author: DocuTrack Team
-- Date: December 3, 2025
-- Version: 1.0.0
-- ===============================================================================

-- Add JSON column to tbl_kegiatan for storing staged disbursement details
ALTER TABLE `tbl_kegiatan` 
ADD COLUMN `pencairan_tahap_json` TEXT NULL DEFAULT NULL 
COMMENT 'JSON array storing multi-stage disbursement details (tahap, tanggal, persentase, jumlah, status)' 
AFTER `metodePencairan`;

-- ===============================================================================
-- Sample Data (Optional - for testing purposes)
-- ===============================================================================
-- Uncomment below to insert sample data for testing

/*
-- Example: 2-stage disbursement for kegiatan ID 1
UPDATE tbl_kegiatan 
SET pencairan_tahap_json = '[
  {"tahap": 1, "tanggal": "2025-01-15", "persentase": 50, "jumlah": 5000000, "status": "scheduled"},
  {"tahap": 2, "tanggal": "2025-02-15", "persentase": 50, "jumlah": 5000000, "status": "scheduled"}
]'
WHERE kegiatanId = 1;

-- Example: 3-stage disbursement for kegiatan ID 2
UPDATE tbl_kegiatan 
SET pencairan_tahap_json = '[
  {"tahap": 1, "tanggal": "2025-01-10", "persentase": 30, "jumlah": 3000000, "status": "disbursed"},
  {"tahap": 2, "tanggal": "2025-02-10", "persentase": 40, "jumlah": 4000000, "status": "scheduled"},
  {"tahap": 3, "tanggal": "2025-03-10", "persentase": 30, "jumlah": 3000000, "status": "scheduled"}
]'
WHERE kegiatanId = 2;
*/

-- ===============================================================================
-- Verification Queries
-- ===============================================================================

-- Check if column was added successfully
-- DESCRIBE tbl_kegiatan;

-- View kegiatan with staged disbursement
-- SELECT 
--   kegiatanId,
--   namaKegiatan,
--   pemilikKegiatan,
--   metodePencairan,
--   pencairan_tahap_json
-- FROM tbl_kegiatan
-- WHERE pencairan_tahap_json IS NOT NULL;

-- Parse JSON to see disbursement stages (MySQL 5.7+)
-- SELECT 
--   kegiatanId,
--   namaKegiatan,
--   JSON_EXTRACT(pencairan_tahap_json, '$[0].tahap') as tahap_1,
--   JSON_EXTRACT(pencairan_tahap_json, '$[0].tanggal') as tanggal_1,
--   JSON_EXTRACT(pencairan_tahap_json, '$[0].persentase') as persentase_1
-- FROM tbl_kegiatan
-- WHERE pencairan_tahap_json IS NOT NULL;

-- ===============================================================================
-- Rollback (if needed)
-- ===============================================================================
-- To remove this column, uncomment and run:
-- ALTER TABLE tbl_kegiatan DROP COLUMN pencairan_tahap_json;

-- ===============================================================================
