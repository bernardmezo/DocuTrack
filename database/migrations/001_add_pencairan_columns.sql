-- ============================================================
-- MIGRATION: Add Pencairan Dana Columns
-- Date: 2025-12-01
-- Description: Menambahkan kolom yang diperlukan untuk fitur
--              pencairan dana Bendahara dan LPJ
-- ============================================================

-- 1. Tambahkan kolom pencairan ke tbl_kegiatan
ALTER TABLE `tbl_kegiatan` 
ADD COLUMN `tanggalPencairan` DATETIME DEFAULT NULL COMMENT 'Tanggal dana dicairkan oleh Bendahara',
ADD COLUMN `jumlahDicairkan` DECIMAL(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
ADD COLUMN `metodePencairan` VARCHAR(50) DEFAULT NULL COMMENT 'Metode pencairan: uang_muka, dana_penuh, bertahap',
ADD COLUMN `catatanBendahara` TEXT DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan';

-- 2. Tambahkan kolom tenggat LPJ ke tbl_lpj
ALTER TABLE `tbl_lpj` 
ADD COLUMN `tenggatLpj` DATE DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ' AFTER `approvedAt`;

-- ============================================================
-- VERIFY: Jalankan query ini untuk memastikan kolom sudah ada
-- ============================================================
-- SHOW COLUMNS FROM tbl_kegiatan LIKE '%Pencairan%';
-- SHOW COLUMNS FROM tbl_kegiatan LIKE '%Bendahara%';
-- SHOW COLUMNS FROM tbl_lpj LIKE 'tenggatLpj';
