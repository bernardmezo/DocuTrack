-- ===============================================================================
-- DOCUTRACK OPTIMAL DATABASE SCHEMA
-- ===============================================================================
-- Database: db_docutrack2
-- Version: 3.0.0 - Merged & Optimized Schema
-- Created: December 8, 2025
-- 
-- Description:
-- Optimal merged schema combining schemaRevision.sql, revisionSchema_plan.sql,
-- and migration files. This schema is designed to work seamlessly with existing
-- backend logic without requiring major refactoring.
--
-- Key Features:
-- 1. Complete workflow support (Admin → Verifikator → PPK → Wadir → Bendahara)
-- 2. Multi-stage disbursement (JSON-based in tbl_kegiatan)
-- 3. Notification system (tbl_log_status)
-- 4. Activity logging (tbl_activity_logs)
-- 5. LPJ status tracking with comments
-- 6. Progress history and revision comments
-- ===============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ===============================================================================
-- DROP EXISTING TABLES (CASCADE ORDER)
-- ===============================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `tbl_lpj_item`;
DROP TABLE IF EXISTS `tbl_lpj`;
DROP TABLE IF EXISTS `tbl_rab`;
DROP TABLE IF EXISTS `tbl_tahapan_pelaksanaan`;
DROP TABLE IF EXISTS `tbl_indikator_kak`;
DROP TABLE IF EXISTS `tbl_kak`;
DROP TABLE IF EXISTS `tbl_revisi_comment`;
DROP TABLE IF EXISTS `tbl_progress_history`;
DROP TABLE IF EXISTS `tbl_kegiatan`;
DROP TABLE IF EXISTS `tbl_activity_logs`;
DROP TABLE IF EXISTS `tbl_log_status`;
DROP TABLE IF EXISTS `tbl_user`;
DROP TABLE IF EXISTS `tbl_prodi`;
DROP TABLE IF EXISTS `tbl_jurusan`;
DROP TABLE IF EXISTS `tbl_wadir`;
DROP TABLE IF EXISTS `tbl_role`;
DROP TABLE IF EXISTS `tbl_status_utama`;
DROP TABLE IF EXISTS `tbl_kategori_rab`;

SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================================================
-- MASTER TABLES (NO FOREIGN KEYS)
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_role
-- Purpose: User roles in the system and workflow positions
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL AUTO_INCREMENT,
  `namaRole` varchar(50) NOT NULL,
  `urutan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow (NULL jika bukan bagian workflow)',
  `deskripsi` varchar(200) DEFAULT NULL COMMENT 'Deskripsi peran dalam workflow',
  PRIMARY KEY (`roleId`),
  UNIQUE KEY `idx_namaRole` (`namaRole`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Master table for user roles and workflow positions';

INSERT INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
(3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
(4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(5, 'Bendahara', 5, 'Pencairan dana'),
(6, 'Super Admin', NULL, 'Administrator sistem - tidak dalam workflow');

-- -----------------------------------------------------------------------------
-- Table: tbl_status_utama
-- Purpose: Main status of proposals/activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_status_utama` (
  `statusId` int(11) NOT NULL,
  `namaStatusUsulan` varchar(100) NOT NULL,
  PRIMARY KEY (`statusId`),
  UNIQUE KEY `idx_namaStatus` (`namaStatusUsulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Master table for proposal status';

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
(1, 'Menunggu'),
(2, 'Revisi'),
(3, 'Disetujui'),
(4, 'Ditolak'),
(5, 'Dana diberikan');

-- -----------------------------------------------------------------------------
-- Table: tbl_jurusan
-- Purpose: Academic departments
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_jurusan` (
  `namaJurusan` varchar(50) NOT NULL,
  PRIMARY KEY (`namaJurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Master table for academic departments';

INSERT INTO `tbl_jurusan` (`namaJurusan`) VALUES
('Administrasi Niaga'),
('Akuntansi'),
('Pascasarjana'),
('Teknik Elektro'),
('Teknik Grafika dan Penerbitan'),
('Teknik Informatika dan Komputer'),
('Teknik Mesin'),
('Teknik Sipil');

-- -----------------------------------------------------------------------------
-- Table: tbl_prodi
-- Purpose: Study programs under departments
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_prodi` (
  `namaProdi` varchar(50) NOT NULL,
  `namaJurusan` varchar(50) NOT NULL,
  PRIMARY KEY (`namaProdi`),
  KEY `fk_prodi_jurusan` (`namaJurusan`),
  CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Study programs under academic departments';

INSERT INTO `tbl_prodi` (`namaProdi`, `namaJurusan`) VALUES
('D3 Administrasi Bisnis', 'Administrasi Niaga'),
('D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'),
('D4 Bahasa Inggris untuk Komunikasi Bisnis dan Prof', 'Administrasi Niaga'),
('D4 Meeting, Incentive, Convention, and Exhibition ', 'Administrasi Niaga'),
('D3 Akuntansi', 'Akuntansi'),
('D3 Keuangan dan Perbankan', 'Akuntansi'),
('D4 Akuntansi Keuangan', 'Akuntansi'),
('D4 Keuangan dan Perbankan Syariah', 'Akuntansi'),
('D4 Manajemen Keuangan', 'Akuntansi'),
('S2 Magister Terapan Rekayasa Teknologi Manufaktur', 'Pascasarjana'),
('S2 Magister Terapan Teknik Elektro', 'Pascasarjana'),
('D3 Teknik Elektronika Industri', 'Teknik Elektro'),
('D3 Teknik Listrik', 'Teknik Elektro'),
('D3 Teknik Telekomunikasi', 'Teknik Elektro'),
('D4 Broadband Multimedia', 'Teknik Elektro'),
('D4 Teknik Instrumentasi dan Kontrol Industri', 'Teknik Elektro'),
('D4 Teknik Otomasi Listrik Industri', 'Teknik Elektro'),
('D3 Penerbitan (Jurnalistik)', 'Teknik Grafika dan Penerbitan'),
('D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'),
('D4 Desain Grafis', 'Teknik Grafika dan Penerbitan'),
('D4 Teknologi Industri Cetak Kemasan', 'Teknik Grafika dan Penerbitan'),
('D1 Teknik Komputer dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Informatika', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia Digital', 'Teknik Informatika dan Komputer'),
('D3 Alat Berat', 'Teknik Mesin'),
('D3 Teknik Konversi Energi', 'Teknik Mesin'),
('D3 Teknik Mesin', 'Teknik Mesin'),
('D4 Pembangkit Tenaga Listrik', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Konversi Energi', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Manufaktur', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Perawatan Alat Berat', 'Teknik Mesin'),
('D3 Konstruksi Gedung', 'Teknik Sipil'),
('D3 Konstruksi Sipil', 'Teknik Sipil'),
('D4 Manajemen Konstruksi', 'Teknik Sipil'),
('D4 Perancangan Jalan dan Jembatan', 'Teknik Sipil');

-- -----------------------------------------------------------------------------
-- Table: tbl_wadir
-- Purpose: Deputy directors (Wakil Direktur)
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_wadir` (
  `wadirId` int(11) NOT NULL,
  `namaWadir` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`wadirId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Deputy directors for approval routing';

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES
(1, 'Wadir 1'),
(2, 'Wadir 2'),
(3, 'Wadir 3'),
(4, 'Wadir 4');

-- -----------------------------------------------------------------------------
-- Table: tbl_kategori_rab
-- Purpose: Budget categories for RAB items
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` int(11) NOT NULL AUTO_INCREMENT,
  `namaKategori` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`kategoriRabId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Budget expense categories';

INSERT INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES
(4, 'Belanja Barang'),
(5, 'Belanja Perjalanan'),
(6, 'Belanja Jasa');

-- ===============================================================================
-- USER & AUTHENTICATION TABLES
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_user
-- Purpose: System users with roles and department assignments
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` int(11) NOT NULL,
  `namaJurusan` varchar(50) DEFAULT NULL COMMENT 'Departemen untuk Admin, NULL untuk peran lain',
  PRIMARY KEY (`userId`),
  UNIQUE KEY `idx_email` (`email`),
  KEY `fk_user_role` (`roleId`),
  KEY `fk_user_jurusan` (`namaJurusan`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_user_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='System users with role-based access control';

INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`) VALUES
(1, 'Admin TI', 'adminti@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Informatika dan Komputer'),
(2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Elektro'),
(3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Sipil'),
(4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Mesin'),
(5, 'Admin Grafika dan Penerbitan', 'admintgp@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Grafika dan Penerbitan'),
(6, 'Admin Akuntansi', 'adminakt@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Akuntansi'),
(7, 'Admin Administrasi Niaga', 'adminan@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Administrasi Niaga'),
(8, 'Admin Pascasarjana', 'adminpasca@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Pascasarjana'),
(9, 'Verifikator', 'verifikator@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 2, NULL),
(10, 'Wakil Direktur', 'wadir@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 3, NULL),
(11, 'PPK', 'ppk@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 4, NULL),
(12, 'Bendahara', 'bendahara@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 5, NULL),
(13, 'Super Admin', 'superadmin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 6, NULL);

-- ===============================================================================
-- CORE WORKFLOW TABLES
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_kegiatan
-- Purpose: Main activity/proposal table - central to the workflow
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_kegiatan` (
  `kegiatanId` int(11) NOT NULL AUTO_INCREMENT,
  `namaKegiatan` varchar(255) NOT NULL,
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke tbl_prodi',
  `pemilikKegiatan` varchar(150) DEFAULT NULL COMMENT 'Nama pemilik/pelaksana kegiatan',
  `nimPelaksana` varchar(20) DEFAULT NULL COMMENT 'NIM pelaksana',
  `nip` varchar(30) DEFAULT NULL COMMENT 'NIP penanggung jawab',
  `namaPJ` varchar(100) DEFAULT NULL COMMENT 'Nama penanggung jawab',
  `danaDiCairkan` decimal(15,2) DEFAULT NULL COMMENT 'Total dana yang sudah dicairkan (legacy)',
  `buktiMAK` varchar(255) DEFAULT NULL COMMENT 'Kode MAK atau file bukti MAK',
  `userId` int(11) NOT NULL COMMENT 'User yang membuat kegiatan (Admin)',
  `jurusanPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke tbl_jurusan',
  `statusUtamaId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status usulan: 1=Menunggu, 2=Revisi, 3=Disetujui, 4=Ditolak, 5=Dana diberikan',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadAt` timestamp NULL DEFAULT NULL COMMENT 'Waktu upload dokumen',
  `wadirTujuan` int(11) NOT NULL COMMENT 'Wadir yang dituju untuk approval',
  `suratPengantar` varchar(255) DEFAULT NULL COMMENT 'Nama file surat pengantar',
  `tanggalMulai` date DEFAULT NULL COMMENT 'Tanggal mulai kegiatan',
  `tanggalSelesai` date DEFAULT NULL COMMENT 'Tanggal selesai kegiatan',
  `posisiId` int(11) NOT NULL DEFAULT 1 COMMENT 'Posisi workflow: 1=Admin, 2=Verifikator, 3=Wadir, 4=PPK, 5=Bendahara',
  
  -- Disbursement fields
  `tanggalPencairan` datetime DEFAULT NULL COMMENT 'Tanggal dana dicairkan (full/first disbursement)',
  `jumlahDicairkan` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `metodePencairan` varchar(50) DEFAULT NULL COMMENT 'Metode: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` text DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan',
  `pencairan_tahap_json` text DEFAULT NULL COMMENT 'JSON array untuk pencairan bertahap: [{tahap, tanggal, persentase, jumlah, status}]',
  
  -- Feedback fields
  `umpanBalikVerifikator` text DEFAULT NULL COMMENT 'Umpan balik dari Verifikator saat approval',
  
  PRIMARY KEY (`kegiatanId`),
  KEY `idx_nimPelaksana` (`nimPelaksana`),
  KEY `idx_nip` (`nip`),
  KEY `fk_kegiatan_user` (`userId`),
  KEY `fk_status_kegiatan` (`statusUtamaId`),
  KEY `fk_kegiatan_jurusan` (`jurusanPenyelenggara`),
  KEY `fk_wadir` (`wadirTujuan`),
  KEY `idx_posisi` (`posisiId`),
  KEY `idx_status` (`statusUtamaId`),
  KEY `idx_created_at` (`createdAt`),
  KEY `idx_tanggal_pencairan` (`tanggalPencairan`),
  CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_kegiatan_jurusan` FOREIGN KEY (`jurusanPenyelenggara`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wadir` FOREIGN KEY (`wadirTujuan`) REFERENCES `tbl_wadir` (`wadirId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Main activity/proposal table with workflow tracking';

-- -----------------------------------------------------------------------------
-- Table: tbl_progress_history
-- Purpose: Track status changes and workflow progression
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_progress_history` (
  `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `statusId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `changedByUserId` int(11) DEFAULT NULL COMMENT 'User ID yang melakukan perubahan status',
  PRIMARY KEY (`progressHistoryId`),
  KEY `fk_history_kegiatan` (`kegiatanId`),
  KEY `fk_history_status` (`statusId`),
  KEY `fk_history_user` (`changedByUserId`),
  KEY `idx_timestamp` (`timestamp`),
  CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_history_user` FOREIGN KEY (`changedByUserId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='History of status changes for activities';

-- -----------------------------------------------------------------------------
-- Table: tbl_revisi_comment
-- Purpose: Store revision comments linked to progress history
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT,
  `progressHistoryId` int(11) NOT NULL,
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar revisi dari approver',
  `targetTabel` varchar(100) DEFAULT NULL COMMENT 'Target table yang perlu direvisi',
  `targetKolom` varchar(100) DEFAULT NULL COMMENT 'Target column yang perlu direvisi',
  PRIMARY KEY (`revisiCommentId`),
  KEY `fk_comment_history` (`progressHistoryId`),
  CONSTRAINT `fk_comment_history` FOREIGN KEY (`progressHistoryId`) REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Revision comments for workflow feedback';

-- ===============================================================================
-- KAK (KERANGKA ACUAN KERJA) TABLES
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_kak
-- Purpose: Terms of Reference for activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_kak` (
  `kakId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `iku` varchar(200) DEFAULT NULL COMMENT 'Indikator Kinerja Utama',
  `penerimaManfaat` text DEFAULT NULL COMMENT 'Penerima manfaat kegiatan',
  `gambaranUmum` text DEFAULT NULL COMMENT 'Gambaran umum kegiatan',
  `metodePelaksanaan` text DEFAULT NULL COMMENT 'Metode pelaksanaan kegiatan',
  `tglPembuatan` date DEFAULT NULL COMMENT 'Tanggal pembuatan KAK',
  PRIMARY KEY (`kakId`),
  KEY `fk_kak_kegiatan` (`kegiatanId`),
  CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Terms of Reference (Kerangka Acuan Kerja)';

-- -----------------------------------------------------------------------------
-- Table: tbl_indikator_kak
-- Purpose: Success indicators for KAK by month
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) NOT NULL,
  `bulan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Bulan pelaksanaan (1-12)',
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL COMMENT 'Deskripsi indikator keberhasilan',
  `targetPersen` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Target pencapaian (0-100)',
  PRIMARY KEY (`indikatorId`),
  KEY `fk_indikator_kak` (`kakId`),
  CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Monthly success indicators for KAK';

-- -----------------------------------------------------------------------------
-- Table: tbl_tahapan_pelaksanaan
-- Purpose: Implementation stages/phases for activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_tahapan_pelaksanaan` (
  `tahapanId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) NOT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL COMMENT 'Nama tahapan pelaksanaan',
  PRIMARY KEY (`tahapanId`),
  KEY `fk_tahapan_kak` (`kakId`),
  CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Implementation stages for activities';

-- ===============================================================================
-- RAB (RENCANA ANGGARAN BIAYA) TABLES
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_rab
-- Purpose: Budget plan items for activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_rab` (
  `rabItemId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) NOT NULL,
  `kategoriId` int(11) NOT NULL,
  `uraian` text DEFAULT NULL COMMENT 'Deskripsi item anggaran',
  `rincian` text DEFAULT NULL COMMENT 'Rincian detail item',
  `sat1` varchar(50) DEFAULT NULL COMMENT 'Satuan 1 (misal: orang, paket)',
  `sat2` varchar(50) DEFAULT NULL COMMENT 'Satuan 2 (misal: hari, bulan)',
  `vol1` decimal(10,2) NOT NULL COMMENT 'Volume 1',
  `vol2` decimal(10,2) NOT NULL COMMENT 'Volume 2',
  `harga` decimal(15,2) NOT NULL COMMENT 'Harga satuan',
  `totalHarga` decimal(15,2) DEFAULT NULL COMMENT 'Total harga item (vol1 * vol2 * harga)',
  `subtotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal untuk kategori',
  PRIMARY KEY (`rabItemId`),
  KEY `fk_rab_kak` (`kakId`),
  KEY `fk_rab_kategori` (`kategoriId`),
  CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE,
  CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Budget plan items (Rencana Anggaran Biaya)';

-- ===============================================================================
-- LPJ (LAPORAN PERTANGGUNGJAWABAN) TABLES
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_lpj
-- Purpose: Accountability reports for completed activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_lpj` (
  `lpjId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `grandTotalRealisasi` decimal(15,2) DEFAULT NULL COMMENT 'Total realisasi dari semua item LPJ',
  `submittedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal submit LPJ',
  `approvedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal approve LPJ',
  `tenggatLpj` date DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ',
  `statusId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status LPJ: 1=Menunggu, 2=Revisi, 3=Disetujui, 4=Ditolak',
  `komentarPenolakan` text DEFAULT NULL COMMENT 'Komentar jika LPJ ditolak',
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar untuk revisi LPJ',
  PRIMARY KEY (`lpjId`),
  UNIQUE KEY `idx_kegiatan_lpj` (`kegiatanId`),
  KEY `fk_lpj_kegiatan` (`kegiatanId`),
  KEY `fk_lpj_status` (`statusId`),
  CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  CONSTRAINT `fk_lpj_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Accountability reports (Laporan Pertanggungjawaban)';

-- -----------------------------------------------------------------------------
-- Table: tbl_lpj_item
-- Purpose: Line items for LPJ reports with evidence files
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL AUTO_INCREMENT,
  `lpjId` int(11) NOT NULL,
  `jenisBelanja` varchar(100) DEFAULT NULL COMMENT 'Jenis belanja/expense type',
  `uraian` text DEFAULT NULL COMMENT 'Deskripsi item',
  `rincian` text DEFAULT NULL COMMENT 'Rincian detail',
  `totalHarga` decimal(15,2) DEFAULT NULL COMMENT 'Total harga item',
  `subTotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal',
  `fileBukti` varchar(255) DEFAULT NULL COMMENT 'Nama file bukti/evidence',
  `komentar` text DEFAULT NULL COMMENT 'Komentar untuk item',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `sat1` varchar(50) DEFAULT NULL COMMENT 'Satuan 1',
  `sat2` varchar(50) DEFAULT NULL COMMENT 'Satuan 2',
  `vol1` decimal(10,2) DEFAULT NULL COMMENT 'Volume 1',
  `vol2` decimal(10,2) DEFAULT NULL COMMENT 'Volume 2',
  PRIMARY KEY (`lpjItemId`),
  KEY `fk_lpj_item_lpj` (`lpjId`),
  CONSTRAINT `fk_lpj_item_lpj` FOREIGN KEY (`lpjId`) REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Line items for accountability reports';

-- ===============================================================================
-- INDEXES & PERFORMANCE OPTIMIZATION
-- ===============================================================================

-- Additional composite indexes for common queries
ALTER TABLE `tbl_kegiatan` 
  ADD KEY `idx_workflow_position` (`posisiId`, `statusUtamaId`, `createdAt`),
  ADD KEY `idx_user_status` (`userId`, `statusUtamaId`),
  ADD KEY `idx_jurusan_status` (`jurusanPenyelenggara`, `statusUtamaId`);

ALTER TABLE `tbl_progress_history`
  ADD KEY `idx_kegiatan_timestamp` (`kegiatanId`, `timestamp` DESC);

ALTER TABLE `tbl_lpj`
  ADD KEY `idx_status_tengat` (`statusId`, `tenggatLpj`);

-- ===============================================================================
-- LOGGING & NOTIFICATION TABLES (Created after workflow tables)
-- ===============================================================================

-- -----------------------------------------------------------------------------
-- Table: tbl_activity_logs
-- Purpose: Unified audit trail for all user activities
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_activity_logs` (
  `logId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(10) UNSIGNED NOT NULL COMMENT 'ID user yang melakukan aksi',
  `action` varchar(50) NOT NULL COMMENT 'Kode aksi (LOGIN_SUCCESS, PPK_APPROVE, dll)',
  `category` enum('authentication','workflow','document','financial','user_management','security') NOT NULL DEFAULT 'workflow' COMMENT 'Kategori aksi untuk grouping dan filtering',
  `entityType` varchar(50) DEFAULT NULL COMMENT 'Tipe entity (kegiatan, lpj, user, dll)',
  `entityId` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID entity yang dimodifikasi',
  `description` text DEFAULT NULL COMMENT 'Deskripsi detail aksi',
  `oldValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Nilai sebelum perubahan' CHECK (json_valid(`oldValue`)),
  `newValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Nilai setelah perubahan' CHECK (json_valid(`newValue`)),
  `ipAddress` varchar(45) DEFAULT NULL COMMENT 'IP Address client (IPv6 support)',
  `userAgent` varchar(500) DEFAULT NULL COMMENT 'Browser/client user agent',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logId`),
  KEY `idx_user_action` (`userId`,`action`),
  KEY `idx_category` (`category`),
  KEY `idx_entity` (`entityType`,`entityId`),
  KEY `idx_created_at` (`createdAt`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Unified audit trail - semua aktivitas user tercatat di sini';

-- -----------------------------------------------------------------------------
-- Table: tbl_log_status
-- Purpose: User-specific notifications and status tracking
-- -----------------------------------------------------------------------------
CREATE TABLE `tbl_log_status` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `tipe_log` varchar(50) NOT NULL COMMENT 'Tipe: NOTIFIKASI_APPROVAL, REMINDER_LPJ, BOOKMARK',
    `id_referensi` int(11) DEFAULT NULL COMMENT 'ID kegiatan, ID LPJ, dll',
    `status` varchar(20) NOT NULL COMMENT 'Status: BELUM_DIBACA, DIBACA, AKTIF',
    `konten_json` JSON DEFAULT NULL COMMENT 'Konten notifikasi dalam format JSON',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `fk_log_user` (`user_id`),
    KEY `idx_user_status` (`user_id`, `status`),
    KEY `idx_tipe_log` (`tipe_log`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Notification system and user-specific status tracking';

-- ===============================================================================
-- VIEWS FOR COMMON QUERIES (OPTIONAL)
-- ===============================================================================

-- View: Complete activity information with user and status details
CREATE OR REPLACE VIEW `vw_kegiatan_detail` AS
SELECT 
    k.*,
    u.nama as admin_nama,
    u.email as admin_email,
    s.namaStatusUsulan as status_nama,
    r.namaRole as posisi_nama,
    w.namaWadir as wadir_nama
FROM tbl_kegiatan k
LEFT JOIN tbl_user u ON k.userId = u.userId
LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
LEFT JOIN tbl_role r ON k.posisiId = r.roleId
LEFT JOIN tbl_wadir w ON k.wadirTujuan = w.wadirId;

-- View: LPJ with status and deadline information
CREATE OR REPLACE VIEW `vw_lpj_status` AS
SELECT 
    l.*,
    k.namaKegiatan,
    k.jurusanPenyelenggara,
    k.prodiPenyelenggara,
    s.namaStatusUsulan as status_lpj,
    CASE 
        WHEN l.tenggatLpj < CURDATE() AND l.statusId = 1 THEN 'OVERDUE'
        WHEN l.tenggatLpj = CURDATE() AND l.statusId = 1 THEN 'DUE_TODAY'
        WHEN l.tenggatLpj > CURDATE() AND l.statusId = 1 THEN 'PENDING'
        ELSE 'COMPLETED'
    END as deadline_status
FROM tbl_lpj l
JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
LEFT JOIN tbl_status_utama s ON l.statusId = s.statusId;

-- ===============================================================================
-- TRIGGERS FOR AUTOMATED CALCULATIONS (OPTIONAL)
-- ===============================================================================

-- Trigger: Auto-calculate totalHarga in tbl_rab
DELIMITER $$
CREATE TRIGGER `trg_rab_calculate_total` 
BEFORE INSERT ON `tbl_rab`
FOR EACH ROW
BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_rab_update_total` 
BEFORE UPDATE ON `tbl_rab`
FOR EACH ROW
BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END$$
DELIMITER ;

-- Trigger: Auto-calculate totalHarga in tbl_lpj_item
DELIMITER $$
CREATE TRIGGER `trg_lpj_item_calculate_total` 
BEFORE INSERT ON `tbl_lpj_item`
FOR EACH ROW
BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL AND NEW.totalHarga IS NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_lpj_item_update_total` 
BEFORE UPDATE ON `tbl_lpj_item`
FOR EACH ROW
BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END$$
DELIMITER ;

-- ===============================================================================
-- COMPLETION
-- ===============================================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ===============================================================================
-- END OF SCHEMA
-- ===============================================================================
-- 
-- Notes:
-- 1. This schema is backward compatible with existing backend code
-- 2. All foreign keys are properly defined with CASCADE options
-- 3. Indexes are optimized for common query patterns
-- 4. JSON column (pencairan_tahap_json) supports multi-stage disbursement
-- 5. Notification system via tbl_log_status is fully integrated
-- 6. Activity logging via tbl_activity_logs provides complete audit trail
-- 7. LPJ status tracking with comments is supported
-- 8. Views provide convenient access to commonly queried data
-- 9. Triggers automate calculation of totals
-- 10. All column names and data types match backend expectations
--
-- To apply this schema:
-- 1. Backup your existing database
-- 2. Run: mysql -u root -p db_docutrack2 < merged_schema.sql
-- 3. Verify all tables are created correctly
-- 4. Test with your existing application code
-- ===============================================================================
