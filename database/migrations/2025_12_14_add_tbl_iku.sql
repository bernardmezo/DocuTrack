-- Create tbl_iku for Indikator Kinerja Utama
CREATE TABLE IF NOT EXISTS `tbl_iku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_iku` varchar(50) DEFAULT NULL,
  `indikator_kinerja` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `realisasi` varchar(100) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ensure tbl_user has created_at
-- Using a stored procedure to check if column exists to avoid errors on re-run
DROP PROCEDURE IF EXISTS `upgrade_tbl_user`;

DELIMITER //

CREATE PROCEDURE `upgrade_tbl_user`()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'tbl_user' 
        AND COLUMN_NAME = 'created_at'
    ) THEN
        ALTER TABLE `tbl_user` ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp();
    END IF;
    
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'tbl_user' 
        AND COLUMN_NAME = 'status'
    ) THEN
         ALTER TABLE `tbl_user` ADD COLUMN `status` ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif';
    END IF;
END //

DELIMITER ;

CALL `upgrade_tbl_user`();
DROP PROCEDURE `upgrade_tbl_user`;
