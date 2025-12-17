-- Migration for altering tbl_lpj_item
-- Date: 2025-12-17 (Adjust if needed for correct chronological order)

-- Add kategoriId, harga, and realisasi columns to tbl_lpj_item
ALTER TABLE `tbl_lpj_item`
  ADD COLUMN `kategoriId` INT(11) DEFAULT NULL COMMENT 'FK to tbl_kategori_rab' AFTER `lpjId`,
  ADD COLUMN `harga` DECIMAL(15,2) DEFAULT NULL COMMENT 'Harga Satuan (Plan)' AFTER `vol2`,
  ADD COLUMN `realisasi` DECIMAL(15,2) DEFAULT NULL COMMENT 'Nilai Realisasi' AFTER `totalHarga`;

-- Add foreign key constraint for kategoriId
-- Assuming tbl_kategori_rab exists and kategoriRabId is its primary key
ALTER TABLE `tbl_lpj_item`
  ADD CONSTRAINT `fk_lpj_item_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE ON DELETE SET NULL;
