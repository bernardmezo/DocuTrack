-- Migration: Fix UNIQUE constraint on tbl_tahapan_pencairan
-- Date: 2025-12-13
-- Issue: FK_idKegiatan should NOT be UNIQUE because one kegiatan can have multiple stages

-- 1. Drop existing UNIQUE constraint/index on idKegiatan
ALTER TABLE `tbl_tahapan_pencairan` 
DROP INDEX `FK_idKegiatan`;

-- 2. Re-add as regular FOREIGN KEY (without UNIQUE)
-- First, check if the foreign key constraint already exists
-- If it exists as FK, we don't need to re-add it
-- But if it was only a UNIQUE index, we need to add the FK constraint

-- Add foreign key constraint if it doesn't exist
ALTER TABLE `tbl_tahapan_pencairan`
ADD CONSTRAINT `fk_tahapan_kegiatan` 
FOREIGN KEY (`idKegiatan`) 
REFERENCES `tbl_kegiatan`(`kegiatanId`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- 3. Verify the change
-- SHOW CREATE TABLE `tbl_tahapan_pencairan`;
