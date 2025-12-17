-- Migration for creating tbl_tahapan_pencairan
-- Date: 2025-12-17

CREATE TABLE IF NOT EXISTS `tbl_tahapan_pencairan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idKegiatan` INT(11) NOT NULL,
  `tglPencairan` DATE NOT NULL,
  `termin` VARCHAR(50) NOT NULL COMMENT 'e.g., Termin 1, Termin 2',
  `nominal` DECIMAL(15,2) NOT NULL,
  `catatan` TEXT DEFAULT NULL,
  `createdBy` INT(11) DEFAULT NULL COMMENT 'User ID of Bendahara',
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kegiatan_pencairan` (`idKegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;