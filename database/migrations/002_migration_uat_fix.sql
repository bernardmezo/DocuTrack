-- ============================================================
-- MIGRATION SCRIPT: DocuTrack UAT Preparation
-- ============================================================
-- Date: December 2, 2025
-- Author: Senior Backend Developer
-- Reference: DATABASE_AUDIT.md & ANALYSIS_REPORT.md
-- 
-- IMPORTANT: Backup database sebelum menjalankan script ini!
-- Command: mysqldump -u root -p db_docutrack2 > backup_before_uat.sql
-- ============================================================

-- Disable foreign key checks sementara untuk ALTER TABLE
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. FIX BUG "ONE-TIME SUBMISSION"
-- Problem: User hanya bisa submit 1 kegiatan karena UNIQUE constraint
--          pada nimPelaksana dan nip
-- Solution: Ubah UNIQUE INDEX menjadi regular INDEX
-- Ref: DATABASE_AUDIT.md - Poin 2.A
-- ============================================================

-- Hapus UNIQUE constraint pada nimPelaksana dan ganti dengan index biasa
-- Jalankan: SHOW INDEX FROM tbl_kegiatan; untuk cek index yang ada
ALTER TABLE `tbl_kegiatan` DROP INDEX `nimPelaksana`;
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_nimPelaksana` (`nimPelaksana`);

-- Hapus UNIQUE constraint pada nip dan ganti dengan index biasa
ALTER TABLE `tbl_kegiatan` DROP INDEX `nip`;
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_nip` (`nip`);

-- ============================================================
-- 2. FIX DATA TRUNCATION - Textarea Fields
-- Problem: VARCHAR(300) terlalu pendek untuk input textarea
--          User mengisi paragraf panjang, data terpotong
-- Solution: Ubah ke TEXT untuk kapasitas ~65KB
-- Ref: DATABASE_AUDIT.md - Poin 1 (Mapping Validation)
-- ============================================================

ALTER TABLE `tbl_kak` 
    MODIFY COLUMN `gambaranUmum` TEXT NULL 
    COMMENT 'Deskripsi gambaran umum kegiatan (upgraded from VARCHAR(300))';

ALTER TABLE `tbl_kak` 
    MODIFY COLUMN `penerimaMaanfaat` TEXT NULL 
    COMMENT 'Deskripsi penerima manfaat - Note: typo kolom dipertahankan untuk kompatibilitas backend';

ALTER TABLE `tbl_kak` 
    MODIFY COLUMN `metodePelaksanaan` TEXT NULL 
    COMMENT 'Deskripsi metode pelaksanaan kegiatan (upgraded from VARCHAR(300))';

-- ============================================================
-- 3. FIX FILENAME LENGTH
-- Problem: VARCHAR(50) terlalu pendek untuk filename dengan timestamp
-- Example: "surat_54_1764472706.docx" = 24 chars (OK)
--          "surat_pengantar_lomba_gemastik_universitas_2025.docx" = 52 chars (FAIL)
-- Solution: Perbesar ke VARCHAR(255)
-- Ref: DATABASE_AUDIT.md - Poin 2.B
-- ============================================================

ALTER TABLE `tbl_kegiatan` 
    MODIFY COLUMN `suratPengantar` VARCHAR(255) NULL 
    COMMENT 'Nama file surat pengantar (dengan timestamp prefix, max 255 chars)';

-- ============================================================
-- 4. DATA TYPE OPTIMIZATION - Performance
-- Problem: Angka disimpan sebagai VARCHAR, boros storage & lambat sorting
-- Solution: Gunakan TINYINT untuk nilai kecil
-- Ref: DATABASE_AUDIT.md - Poin 1 (Mapping Validation - OPTIMIZE)
-- ============================================================

-- Bulan: nilai 1-12
ALTER TABLE `tbl_indikator_kak` 
    MODIFY COLUMN `bulan` TINYINT UNSIGNED NULL 
    COMMENT 'Bulan pelaksanaan (1-12)';

-- Target: nilai 0-100 (persen)
ALTER TABLE `tbl_indikator_kak` 
    MODIFY COLUMN `targetPersen` TINYINT UNSIGNED NULL 
    COMMENT 'Target pencapaian dalam persen (0-100)';

-- ============================================================
-- 5. PERFORMANCE INDEXING
-- Tambahkan index untuk kolom yang sering digunakan filter/sort
-- Ref: DATABASE_AUDIT.md - Pilar 5 (Performance & Scalability)
-- ============================================================

-- Index untuk filter berdasarkan status
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_status` (`statusUtamaId`);

-- Index untuk sorting berdasarkan tanggal
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_created_at` (`createdAt`);

-- Composite index untuk filter kombinasi (sering digunakan di dashboard)
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_jurusan_status` (`jurusanPenyelenggara`, `statusUtamaId`);

-- Index untuk posisi (workflow tracking)
ALTER TABLE `tbl_kegiatan` ADD INDEX `idx_posisi` (`posisiId`);

-- ============================================================
-- 6. FIX: Tambah kolom changedByUserId di tbl_progress_history
-- Problem: Tidak tercatat SIAPA yang mengubah status
-- Ref: DATABASE_AUDIT.md - Pilar 3 (Auditability)
-- ============================================================

-- Tambah kolom untuk tracking user yang melakukan perubahan
ALTER TABLE `tbl_progress_history`
    ADD COLUMN `changedByUserId` INT NULL 
    COMMENT 'User ID yang melakukan perubahan status' 
    AFTER `timestamp`;

-- ============================================================
-- 7. CREATE AUDIT LOG TABLE (New Feature for Accountability)
-- Untuk tracking aktivitas user sesuai requirement UAT
-- Ref: DATABASE_AUDIT.md - Pilar 3 & ANALYSIS_REPORT.md - Poin 3.C
-- ============================================================

CREATE TABLE IF NOT EXISTS `tbl_activity_logs` (
    `logId` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `userId` INT UNSIGNED NOT NULL COMMENT 'ID user yang melakukan aksi',
    `action` VARCHAR(50) NOT NULL COMMENT 'Kode aksi (LOGIN, APPROVE, REJECT, PENCAIRAN, dll)',
    `entityType` VARCHAR(50) NULL COMMENT 'Tipe entity (kegiatan, pencairan, user, dll)',
    `entityId` INT UNSIGNED NULL COMMENT 'ID entity yang dimodifikasi',
    `description` TEXT NULL COMMENT 'Deskripsi detail aksi',
    `oldValue` JSON NULL COMMENT 'Nilai sebelum perubahan (untuk audit trail)',
    `newValue` JSON NULL COMMENT 'Nilai setelah perubahan',
    `ipAddress` VARCHAR(45) NULL COMMENT 'IP Address client (support IPv6)',
    `userAgent` VARCHAR(500) NULL COMMENT 'Browser/client user agent',
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_user_action` (`userId`, `action`),
    INDEX `idx_entity` (`entityType`, `entityId`),
    INDEX `idx_created_at` (`createdAt`),
    INDEX `idx_action` (`action`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Audit trail untuk tracking semua aktivitas penting user (UAT Accountability)';

-- ============================================================
-- 8. INSERT PREDEFINED LOG ACTION TYPES (Reference Table)
-- ============================================================

CREATE TABLE IF NOT EXISTS `tbl_log_actions` (
    `actionCode` VARCHAR(50) PRIMARY KEY,
    `actionName` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL COMMENT 'auth, approval, data, system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `tbl_log_actions` (`actionCode`, `actionName`, `category`) VALUES
('LOGIN_SUCCESS', 'Login Berhasil', 'auth'),
('LOGIN_FAILED', 'Login Gagal', 'auth'),
('LOGOUT', 'Logout', 'auth'),
('SESSION_EXPIRED', 'Sesi Kadaluarsa', 'auth'),
('CREATE_KEGIATAN', 'Membuat Pengajuan Kegiatan', 'data'),
('UPDATE_KEGIATAN', 'Mengupdate Pengajuan Kegiatan', 'data'),
('DELETE_KEGIATAN', 'Menghapus Pengajuan Kegiatan', 'data'),
('UPLOAD_DOCUMENT', 'Upload Dokumen', 'data'),
('VERIFIKATOR_APPROVE', 'Verifikator Menyetujui', 'approval'),
('VERIFIKATOR_REJECT', 'Verifikator Menolak', 'approval'),
('ADMIN_APPROVE', 'Admin Menyetujui', 'approval'),
('ADMIN_REJECT', 'Admin Menolak', 'approval'),
('PPK_APPROVE', 'PPK Menyetujui', 'approval'),
('PPK_REJECT', 'PPK Menolak', 'approval'),
('WADIR_APPROVE', 'Wadir Menyetujui', 'approval'),
('WADIR_REJECT', 'Wadir Menolak', 'approval'),
('PENCAIRAN_PROSES', 'Proses Pencairan Dana', 'approval'),
('PENCAIRAN_SUCCESS', 'Pencairan Dana Berhasil', 'approval'),
('PENCAIRAN_REJECT', 'Pencairan Dana Ditolak', 'approval'),
('LPJ_SUBMIT', 'Submit LPJ', 'data'),
('LPJ_APPROVE', 'LPJ Disetujui', 'approval'),
('LPJ_REJECT', 'LPJ Ditolak', 'approval'),
('USER_CREATE', 'Membuat User Baru', 'data'),
('USER_UPDATE', 'Mengupdate User', 'data'),
('USER_DELETE', 'Menghapus User', 'data'),
('USER_RESET_PASSWORD', 'Reset Password User', 'data'),
('SECURITY_VIOLATION', 'Pelanggaran Keamanan Terdeteksi', 'system'),
('PATH_TRAVERSAL_ATTEMPT', 'Percobaan Path Traversal', 'system');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 9. CREATE TBL_POSISI (Reference Table for Workflow Positions)
-- Problem: Query di verifikatorModel.php line 494 mereferensi tbl_posisi yang tidak ada
-- ============================================================

CREATE TABLE IF NOT EXISTS `tbl_posisi` (
    `posisiId` INT PRIMARY KEY,
    `namaPosisi` VARCHAR(50) NOT NULL,
    `roleId` INT NOT NULL COMMENT 'Mapping ke role yang bertanggung jawab di posisi ini',
    `urutan` INT NOT NULL COMMENT 'Urutan dalam workflow estafet',
    `deskripsi` VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Reference table untuk posisi dalam workflow approval';

INSERT IGNORE INTO `tbl_posisi` (`posisiId`, `namaPosisi`, `roleId`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 2, 'Verifikasi dokumen dan kelengkapan'),
(4, 'PPK', 4, 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(3, 'Wadir', 3, 4, 'Wakil Direktur - approval tingkat direktur'),
(5, 'Bendahara', 5, 5, 'Pencairan dana');

-- ============================================================
-- VERIFICATION QUERIES
-- Jalankan untuk memastikan migrasi berhasil
-- ============================================================
-- SHOW INDEX FROM tbl_kegiatan;
-- DESCRIBE tbl_kak;
-- SHOW TABLES LIKE 'tbl_activity_logs';
-- DESCRIBE tbl_activity_logs;
-- SELECT * FROM tbl_posisi ORDER BY urutan;
