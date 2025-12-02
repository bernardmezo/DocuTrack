-- ============================================================
-- DocuTrack Database Schema + Seed (Complete)
-- ============================================================
-- Date: December 2, 2025
-- Version: 2.0 (UAT Ready)
-- Database: db_docutrack2
-- 
-- Includes:
-- - All table structures (original + migration fixes)
-- - All indexes for performance
-- - All foreign key constraints
-- - Seed data with hashed passwords (password123)
-- 
-- USAGE:
-- 1. Drop existing database: DROP DATABASE IF EXISTS db_docutrack2;
-- 2. Create fresh: CREATE DATABASE db_docutrack2;
-- 3. Use: USE db_docutrack2;
-- 4. Import this file: source schema_with_seed.sql;
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- DROP EXISTING TABLES (Fresh Install)
-- ============================================================
DROP TABLE IF EXISTS `tbl_revisi_comment`;
DROP TABLE IF EXISTS `tbl_progress_history`;
DROP TABLE IF EXISTS `tbl_lpj_item`;
DROP TABLE IF EXISTS `tbl_lpj`;
DROP TABLE IF EXISTS `tbl_rab`;
DROP TABLE IF EXISTS `tbl_tahapan_pelaksanaan`;
DROP TABLE IF EXISTS `tbl_indikator_kak`;
DROP TABLE IF EXISTS `tbl_kak`;
-- DROP TABLE IF EXISTS `tbl_rancangan_kegiatan`; -- REMOVED: Duplikasi dengan kolom di tbl_kegiatan
DROP TABLE IF EXISTS `tbl_kegiatan`;
DROP TABLE IF EXISTS `tbl_user`;
DROP TABLE IF EXISTS `tbl_prodi`;
DROP TABLE IF EXISTS `tbl_jurusan`;
DROP TABLE IF EXISTS `tbl_role`;
DROP TABLE IF EXISTS `tbl_status_utama`;
DROP TABLE IF EXISTS `tbl_wadir`;
DROP TABLE IF EXISTS `tbl_kategori_rab`;
DROP TABLE IF EXISTS `tbl_activity_logs`;
-- DROP TABLE IF EXISTS `tbl_log_actions`; -- REMOVED: Merged into tbl_activity_logs
-- DROP TABLE IF EXISTS `tbl_posisi`; -- REMOVED: Merged into tbl_role

-- ============================================================
-- TABLE: tbl_role (Master - Roles + Workflow Position)
-- OPTIMIZED: Merged with tbl_posisi - urutan & deskripsi added
-- ============================================================
CREATE TABLE `tbl_role` (
  `roleId` INT NOT NULL AUTO_INCREMENT,
  `namaRole` VARCHAR(50) NOT NULL,
  `urutan` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow (NULL jika bukan bagian workflow)',
  `deskripsi` VARCHAR(200) DEFAULT NULL COMMENT 'Deskripsi peran dalam workflow',
  PRIMARY KEY (`roleId`),
  UNIQUE KEY `namaRole` (`namaRole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
(3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
(4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(5, 'Bendahara', 5, 'Pencairan dana'),
(6, 'Super Admin', NULL, 'Administrator sistem - tidak dalam workflow');

-- ============================================================
-- TABLE: tbl_status_utama (Master - Status)
-- ============================================================
CREATE TABLE `tbl_status_utama` (
  `statusId` INT NOT NULL AUTO_INCREMENT,
  `namaStatusUsulan` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`statusId`),
  UNIQUE KEY `namaStatusUsulan` (`namaStatusUsulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
(1, 'Menunggu'),
(2, 'Revisi'),
(3, 'Disetujui'),
(4, 'Ditolak');

-- ============================================================
-- TABLE: tbl_wadir (Master - Wadir Options)
-- ============================================================
CREATE TABLE `tbl_wadir` (
  `wadirId` INT NOT NULL AUTO_INCREMENT,
  `namaWadir` VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`wadirId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES
(1, 'Wadir 1'),
(2, 'Wadir 2'),
(3, 'Wadir 3'),
(4, 'Wadir 4');

-- ============================================================
-- TABLE: tbl_kategori_rab (Master - RAB Categories)
-- ============================================================
CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` INT NOT NULL AUTO_INCREMENT,
  `namaKategori` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`kategoriRabId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES
(4, 'Belanja Barang'),
(5, 'Belanja Perjalanan'),
(6, 'Belanja Jasa');

-- ============================================================
-- TABLE: tbl_jurusan (Master - Departments)
-- ============================================================
CREATE TABLE `tbl_jurusan` (
  `namaJurusan` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`namaJurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Tabel master untuk data Jurusan';

INSERT INTO `tbl_jurusan` (`namaJurusan`) VALUES
('Administrasi Niaga'),
('Akuntansi'),
('Pascasarjana'),
('Teknik Elektro'),
('Teknik Grafika dan Penerbitan'),
('Teknik Informatika dan Komputer'),
('Teknik Mesin'),
('Teknik Sipil');

-- ============================================================
-- TABLE: tbl_prodi (Master - Study Programs)
-- ============================================================
CREATE TABLE `tbl_prodi` (
  `namaProdi` VARCHAR(50) NOT NULL,
  `namaJurusan` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`namaProdi`),
  KEY `fk_namaJurusan` (`namaJurusan`),
  CONSTRAINT `tbl_prodi_ibfk_1` FOREIGN KEY (`namaJurusan`) 
    REFERENCES `tbl_jurusan` (`namaJurusan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_prodi` (`namaProdi`, `namaJurusan`) VALUES
-- Administrasi Niaga
('D3 Administrasi Bisnis', 'Administrasi Niaga'),
('D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'),
('D4 Bahasa Inggris untuk Komunikasi Bisnis dan Prof', 'Administrasi Niaga'),
('D4 Meeting, Incentive, Convention, and Exhibition ', 'Administrasi Niaga'),
-- Akuntansi
('D3 Akuntansi', 'Akuntansi'),
('D3 Keuangan dan Perbankan', 'Akuntansi'),
('D4 Akuntansi Keuangan', 'Akuntansi'),
('D4 Keuangan dan Perbankan Syariah', 'Akuntansi'),
('D4 Manajemen Keuangan', 'Akuntansi'),
-- Pascasarjana
('S2 Magister Terapan Rekayasa Teknologi Manufaktur', 'Pascasarjana'),
('S2 Magister Terapan Teknik Elektro', 'Pascasarjana'),
-- Teknik Elektro
('D3 Teknik Elektronika Industri', 'Teknik Elektro'),
('D3 Teknik Listrik', 'Teknik Elektro'),
('D3 Teknik Telekomunikasi', 'Teknik Elektro'),
('D4 Broadband Multimedia', 'Teknik Elektro'),
('D4 Teknik Instrumentasi dan Kontrol Industri', 'Teknik Elektro'),
('D4 Teknik Otomasi Listrik Industri', 'Teknik Elektro'),
-- Teknik Grafika dan Penerbitan
('D3 Penerbitan (Jurnalistik)', 'Teknik Grafika dan Penerbitan'),
('D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'),
('D4 Desain Grafis', 'Teknik Grafika dan Penerbitan'),
('D4 Teknologi Industri Cetak Kemasan', 'Teknik Grafika dan Penerbitan'),
-- Teknik Informatika dan Komputer
('D1 Teknik Komputer dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Informatika', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia Digital', 'Teknik Informatika dan Komputer'),
-- Teknik Mesin
('D3 Alat Berat', 'Teknik Mesin'),
('D3 Teknik Konversi Energi', 'Teknik Mesin'),
('D3 Teknik Mesin', 'Teknik Mesin'),
('D4 Pembangkit Tenaga Listrik', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Konversi Energi', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Manufaktur', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Perawatan Alat Berat', 'Teknik Mesin'),
-- Teknik Sipil
('D3 Konstruksi Gedung', 'Teknik Sipil'),
('D3 Konstruksi Sipil', 'Teknik Sipil'),
('D4 Manajemen Konstruksi', 'Teknik Sipil'),
('D4 Perancangan Jalan dan Jembatan', 'Teknik Sipil');

-- ============================================================
-- TABLE: tbl_user (Users with Hashed Passwords)
-- Password: password123
-- Hash generated with: password_hash('password123', PASSWORD_BCRYPT)
-- ============================================================
CREATE TABLE `tbl_user` (
  `userId` INT NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `roleId` INT NOT NULL,
  `namaJurusan` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_role` (`roleId`),
  KEY `namaJurusan` (`namaJurusan`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) 
    REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_user_ibfk_1` FOREIGN KEY (`namaJurusan`) 
    REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password: password123 (hashed with bcrypt, cost 10)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`) VALUES
-- Admin per Jurusan (roleId = 1)
(1, 'Admin TI', 'adminti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Teknik Informatika dan Komputer'),
(2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Teknik Elektro'),
(3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Teknik Sipil'),
(4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Teknik Mesin'),
(5, 'Admin Grafika dan Penerbitan', 'admintgp@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Teknik Grafika dan Penerbitan'),
(6, 'Admin Akuntansi', 'adminakt@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Akuntansi'),
(7, 'Admin Administrasi Niaga', 'adminan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Administrasi Niaga'),
(8, 'Admin Pascasarjana', 'adminpasca@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Pascasarjana'),
-- Verifikator (roleId = 2)
(9, 'Verifikator', 'verifikator@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL),
-- Wadir (roleId = 3)
(10, 'Wakil Direktur', 'wadir@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL),
-- PPK (roleId = 4)
(11, 'PPK', 'ppk@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL),
-- Bendahara (roleId = 5)
(12, 'Bendahara', 'bendahara@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, NULL),
-- Super Admin (roleId = 6)
(13, 'Super Admin', 'superadmin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, NULL);

-- ============================================================
-- TABLE: tbl_kegiatan (Main Transaction Table)
-- FIXED: nimPelaksana & nip changed from UNIQUE to INDEX
-- FIXED: suratPengantar expanded to VARCHAR(255)
-- ADDED: umpanBalikVerifikator untuk menyimpan instruksi dari verifikator ke admin
-- ============================================================
CREATE TABLE `tbl_kegiatan` (
  `kegiatanId` INT NOT NULL AUTO_INCREMENT,
  `namaKegiatan` VARCHAR(255) NOT NULL,
  `prodiPenyelenggara` VARCHAR(50) DEFAULT NULL COMMENT 'FK_Nama Prodi penyelenggara',
  `pemilikKegiatan` VARCHAR(150) DEFAULT NULL,
  `nimPelaksana` VARCHAR(20) DEFAULT NULL,
  `nip` VARCHAR(30) DEFAULT NULL,
  `namaPJ` VARCHAR(100) DEFAULT NULL,
  `danaDiCairkan` DECIMAL(15,2) DEFAULT NULL,
  `buktiMAK` VARCHAR(255) DEFAULT NULL,
  `userId` INT NOT NULL,
  `jurusanPenyelenggara` VARCHAR(50) DEFAULT NULL COMMENT 'FK ke Jurusan',
  `statusUtamaId` INT NOT NULL DEFAULT 1,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploadAt` TIMESTAMP NULL DEFAULT NULL,
  `wadirTujuan` INT NOT NULL,
  `suratPengantar` VARCHAR(255) DEFAULT NULL COMMENT 'Nama file surat pengantar (max 255 chars)',
  `tanggalMulai` DATE DEFAULT NULL,
  `tanggalSelesai` DATE DEFAULT NULL,
  `posisiId` INT NOT NULL DEFAULT 1 COMMENT 'Posisi workflow: 1=Admin, 2=Verifikator, 4=PPK, 3=Wadir, 5=Bendahara',
  `tanggalPencairan` DATETIME DEFAULT NULL COMMENT 'Tanggal dana dicairkan oleh Bendahara',
  `jumlahDicairkan` DECIMAL(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `metodePencairan` VARCHAR(50) DEFAULT NULL COMMENT 'Metode: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` TEXT DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan',
  `umpanBalikVerifikator` TEXT DEFAULT NULL COMMENT 'Umpan balik/instruksi dari Verifikator untuk Admin saat menyetujui usulan',
  PRIMARY KEY (`kegiatanId`),
  -- FIXED: Changed from UNIQUE to INDEX to allow multiple submissions per user
  KEY `idx_nimPelaksana` (`nimPelaksana`),
  KEY `idx_nip` (`nip`),
  KEY `fk_kegiatan_user` (`userId`),
  KEY `fk_status_kegiatan` (`statusUtamaId`),
  KEY `prodiPenyelenggara` (`prodiPenyelenggara`, `jurusanPenyelenggara`),
  KEY `jurusanPenyelenggara` (`jurusanPenyelenggara`),
  KEY `fk_wadir` (`wadirTujuan`),
  KEY `idx_posisi` (`posisiId`),
  -- Performance indexes
  KEY `idx_status` (`statusUtamaId`),
  KEY `idx_created_at` (`createdAt`),
  KEY `idx_jurusan_status` (`jurusanPenyelenggara`, `statusUtamaId`),
  CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) 
    REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_posisi_role` FOREIGN KEY (`posisiId`) 
    REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) 
    REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_1` FOREIGN KEY (`prodiPenyelenggara`) 
    REFERENCES `tbl_prodi` (`namaProdi`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_2` FOREIGN KEY (`jurusanPenyelenggara`) 
    REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_3` FOREIGN KEY (`wadirTujuan`) 
    REFERENCES `tbl_wadir` (`wadirId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_kak (Kerangka Acuan Kegiatan)
-- FIXED: Text fields changed from VARCHAR(300) to TEXT
-- ============================================================
CREATE TABLE `tbl_kak` (
  `kakId` INT NOT NULL AUTO_INCREMENT,
  `kegiatanId` INT NOT NULL,
  `iku` VARCHAR(200) DEFAULT NULL,
  `penerimaMaanfaat` TEXT DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `gambaranUmum` TEXT DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `metodePelaksanaan` TEXT DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `tglPembuatan` DATE DEFAULT NULL,
  PRIMARY KEY (`kakId`),
  KEY `fk_kak_kegiatan` (`kegiatanId`),
  CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_indikator_kak (KAK Indicators)
-- FIXED: bulan & targetPersen changed to TINYINT
-- ============================================================
CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` INT NOT NULL AUTO_INCREMENT,
  `kakId` INT DEFAULT NULL,
  `bulan` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Bulan pelaksanaan (1-12)',
  `indikatorKeberhasilan` VARCHAR(250) DEFAULT NULL,
  `targetPersen` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Target pencapaian (0-100)',
  PRIMARY KEY (`indikatorId`),
  KEY `fk_indikator_kak` (`kakId`),
  CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_tahapan_pelaksanaan (Implementation Stages)
-- ============================================================
CREATE TABLE `tbl_tahapan_pelaksanaan` (
  `tahapanId` INT NOT NULL AUTO_INCREMENT,
  `kakId` INT DEFAULT NULL,
  `namaTahapan` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`tahapanId`),
  KEY `fk_tahapan_kak` (`kakId`),
  CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_rab (Rencana Anggaran Biaya)
-- ============================================================
CREATE TABLE `tbl_rab` (
  `rabItemId` INT NOT NULL AUTO_INCREMENT,
  `kakId` INT NOT NULL,
  `kategoriId` INT NOT NULL,
  `uraian` TEXT DEFAULT NULL,
  `rincian` TEXT DEFAULT NULL,
  `sat1` VARCHAR(50) DEFAULT NULL,
  `sat2` VARCHAR(50) DEFAULT NULL,
  `vol1` DECIMAL(10,2) NOT NULL,
  `vol2` DECIMAL(10,2) NOT NULL,
  `harga` DECIMAL(15,2) NOT NULL,
  `totalHarga` DECIMAL(15,2) DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT NULL,
  PRIMARY KEY (`rabItemId`),
  KEY `fk_rab_kak` (`kakId`),
  KEY `fk_rab_kategori` (`kategoriId`),
  CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) 
    REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_rancangan_kegiatan - REMOVED (Optimized)
-- ============================================================
-- REASON: 100% duplikasi dengan kolom di tbl_kegiatan:
--   - fileSuratPengantar → tbl_kegiatan.suratPengantar
--   - tglMulai → tbl_kegiatan.tanggalMulai
--   - tglSelesai → tbl_kegiatan.tanggalSelesai
-- Model rancanganganKegModel.php tidak pernah dipanggil dari controller

-- ============================================================
-- TABLE: tbl_lpj (Laporan Pertanggungjawaban)
-- ============================================================
CREATE TABLE `tbl_lpj` (
  `lpjId` INT NOT NULL AUTO_INCREMENT,
  `kegiatanId` INT NOT NULL,
  `grandTotalRealisasi` DECIMAL(15,2) DEFAULT NULL,
  `submittedAt` TIMESTAMP NULL DEFAULT NULL,
  `approvedAt` TIMESTAMP NULL DEFAULT NULL,
  `tenggatLpj` DATE DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ',
  PRIMARY KEY (`lpjId`),
  KEY `fk_lpj_kegiatan` (`kegiatanId`),
  CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_lpj_item (LPJ Line Items)
-- ============================================================
CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` INT NOT NULL AUTO_INCREMENT,
  `lpjId` INT NOT NULL,
  `jenisBelanja` VARCHAR(150) DEFAULT NULL,
  `uraian` TEXT DEFAULT NULL,
  `rincian` TEXT DEFAULT NULL,
  `satuan` VARCHAR(50) DEFAULT NULL,
  `totalHarga` DECIMAL(15,2) DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT NULL,
  `fileBukti` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`lpjItemId`),
  KEY `fk_item_ke_lpj` (`lpjId`),
  CONSTRAINT `fk_item_ke_lpj` FOREIGN KEY (`lpjId`) 
    REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_progress_history (Workflow History)
-- ADDED: changedByUserId for audit trail
-- ============================================================
CREATE TABLE `tbl_progress_history` (
  `progressHistoryId` INT NOT NULL AUTO_INCREMENT,
  `kegiatanId` INT NOT NULL,
  `statusId` INT NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changedByUserId` INT DEFAULT NULL COMMENT 'User ID yang melakukan perubahan status',
  PRIMARY KEY (`progressHistoryId`),
  KEY `fk_history_kegiatan` (`kegiatanId`),
  KEY `fk_history_status` (`statusId`),
  KEY `fk_history_user` (`changedByUserId`),
  CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) 
    REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_revisi_comment (Revision Comments)
-- ============================================================
CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` INT NOT NULL AUTO_INCREMENT,
  `progressHistoryId` INT NOT NULL,
  `komentarRevisi` TEXT DEFAULT NULL,
  `targetTabel` VARCHAR(100) DEFAULT NULL,
  `targetKolom` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`revisiCommentId`),
  KEY `fk_comment_to_history` (`progressHistoryId`),
  CONSTRAINT `fk_comment_to_history` FOREIGN KEY (`progressHistoryId`) 
    REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tbl_posisi - REMOVED (Optimized)
-- ============================================================
-- REASON: posisiId === roleId (1:1 mapping)
-- Data telah di-merge ke tbl_role dengan kolom tambahan:
--   - urutan: Urutan dalam workflow
--   - deskripsi: Deskripsi peran
-- 
-- Workflow order (by urutan):
-- 1. Admin (roleId=1) → Membuat pengajuan
-- 2. Verifikator (roleId=2) → Verifikasi dokumen
-- 3. PPK (roleId=4) → Approval anggaran
-- 4. Wadir (roleId=3) → Approval direktur
-- 5. Bendahara (roleId=5) → Pencairan dana
-- Note: Super Admin (roleId=6) tidak dalam workflow (urutan=NULL)

-- ============================================================
-- TABLE: tbl_activity_logs (Audit Trail - OPTIMIZED)
-- MERGED: Combined with tbl_log_actions for simpler structure
-- ============================================================
CREATE TABLE `tbl_activity_logs` (
  `logId` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `userId` INT UNSIGNED NOT NULL COMMENT 'ID user yang melakukan aksi',
  `action` VARCHAR(50) NOT NULL COMMENT 'Kode aksi (LOGIN_SUCCESS, PPK_APPROVE, dll)',
  `category` ENUM('authentication', 'workflow', 'document', 'financial', 'user_management', 'security') NOT NULL DEFAULT 'workflow' 
    COMMENT 'Kategori aksi untuk grouping dan filtering',
  `entityType` VARCHAR(50) DEFAULT NULL COMMENT 'Tipe entity (kegiatan, lpj, user, dll)',
  `entityId` INT UNSIGNED DEFAULT NULL COMMENT 'ID entity yang dimodifikasi',
  `description` TEXT DEFAULT NULL COMMENT 'Deskripsi detail aksi',
  `oldValue` JSON DEFAULT NULL COMMENT 'Nilai sebelum perubahan',
  `newValue` JSON DEFAULT NULL COMMENT 'Nilai setelah perubahan',
  `ipAddress` VARCHAR(45) DEFAULT NULL COMMENT 'IP Address client (IPv6 support)',
  `userAgent` VARCHAR(500) DEFAULT NULL COMMENT 'Browser/client user agent',
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_action` (`userId`, `action`),
  KEY `idx_category` (`category`),
  KEY `idx_entity` (`entityType`, `entityId`),
  KEY `idx_created_at` (`createdAt`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Unified audit trail - semua aktivitas user tercatat di sini';

-- ============================================================
-- CATEGORY DEFINITIONS:
-- ============================================================
-- authentication  : Login, logout, session management
-- workflow        : Approval flow (verifikator, PPK, wadir, bendahara)
-- document        : CRUD kegiatan, KAK, RAB, upload files
-- financial       : Pencairan dana, LPJ, transaksi keuangan
-- user_management : CRUD user, reset password, role changes
-- security        : Violation attempts, unauthorized access
-- ============================================================

-- ============================================================
-- PREDEFINED ACTION CODES (Reference - tidak disimpan di tabel terpisah)
-- ============================================================
-- 
-- AUTHENTICATION CATEGORY:
-- LOGIN_SUCCESS, LOGIN_FAILED, LOGOUT, SESSION_EXPIRED, PASSWORD_CHANGE
-- 
-- WORKFLOW CATEGORY:
-- VERIFIKATOR_APPROVE, VERIFIKATOR_REJECT, VERIFIKATOR_REVISE
-- PPK_APPROVE, PPK_REJECT, PPK_REVISE
-- WADIR_APPROVE, WADIR_REJECT, WADIR_REVISE
-- STATUS_CHANGE, WORKFLOW_FORWARD, WORKFLOW_RETURN
-- 
-- DOCUMENT CATEGORY:
-- CREATE_KEGIATAN, UPDATE_KEGIATAN, DELETE_KEGIATAN
-- CREATE_KAK, UPDATE_KAK, CREATE_RAB, UPDATE_RAB
-- UPLOAD_DOCUMENT, DELETE_DOCUMENT, SUBMIT_RINCIAN
-- 
-- FINANCIAL CATEGORY:
-- PENCAIRAN_PROCESS, PENCAIRAN_SUCCESS, PENCAIRAN_REJECT
-- LPJ_SUBMIT, LPJ_APPROVE, LPJ_REJECT, LPJ_REVISE
-- BUDGET_UPDATE, REALISASI_UPDATE
-- 
-- USER_MANAGEMENT CATEGORY:
-- USER_CREATE, USER_UPDATE, USER_DELETE
-- USER_RESET_PASSWORD, USER_ROLE_CHANGE, USER_ACTIVATE, USER_DEACTIVATE
-- 
-- SECURITY CATEGORY:
-- SECURITY_VIOLATION, PATH_TRAVERSAL_ATTEMPT, SQL_INJECTION_ATTEMPT
-- UNAUTHORIZED_ACCESS, INVALID_TOKEN, BRUTE_FORCE_DETECTED
-- ============================================================

-- ============================================================
-- SAMPLE DATA: tbl_kegiatan (Optional - for testing)
-- ============================================================
-- Uncomment if you want sample kegiatan data
/*
INSERT INTO `tbl_kegiatan` (`kegiatanId`, `namaKegiatan`, `prodiPenyelenggara`, `pemilikKegiatan`, `nimPelaksana`, `nip`, `namaPJ`, `userId`, `jurusanPenyelenggara`, `statusUtamaId`, `createdAt`, `wadirTujuan`, `posisiId`) VALUES
(1, 'Lomba Programming Nasional', 'D4 Teknik Informatika', 'Budi Santoso', '24074510001', NULL, NULL, 1, 'Teknik Informatika dan Komputer', 1, NOW(), 1, 2);
*/

-- ============================================================
-- RE-ENABLE FOREIGN KEY CHECKS & COMMIT
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================
-- VERIFICATION QUERIES (Run after import to verify)
-- ============================================================
-- SELECT * FROM tbl_user;
-- SELECT * FROM tbl_role;
-- SELECT * FROM tbl_role ORDER BY urutan;
-- SHOW TABLES;
-- DESCRIBE tbl_kegiatan;
-- SHOW INDEX FROM tbl_kegiatan;

-- ============================================================
-- USER CREDENTIALS (All passwords: password123)
-- ============================================================
-- | Email                      | Role         | Jurusan                         |
-- |----------------------------|--------------|----------------------------------|
-- | adminti@gmail.com          | Admin        | Teknik Informatika dan Komputer |
-- | adminelektro@gmail.com     | Admin        | Teknik Elektro                  |
-- | adminsipil@gmail.com       | Admin        | Teknik Sipil                    |
-- | adminmesin@gmail.com       | Admin        | Teknik Mesin                    |
-- | admintgp@gmail.com         | Admin        | Teknik Grafika dan Penerbitan   |
-- | adminakt@gmail.com         | Admin        | Akuntansi                       |
-- | adminan@gmail.com          | Admin        | Administrasi Niaga              |
-- | adminpasca@gmail.com       | Admin        | Pascasarjana                    |
-- | verifikator@gmail.com      | Verifikator  | -                               |
-- | wadir@gmail.com            | Wadir        | -                               |
-- | ppk@gmail.com              | PPK          | -                               |
-- | bendahara@gmail.com        | Bendahara    | -                               |
-- | superadmin@gmail.com       | Super Admin  | -                               |
-- ============================================================
