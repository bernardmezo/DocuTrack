-- ============================================================
-- DATABASE DOCUTRACK 2 (FINAL VERSION)
-- ============================================================
-- Dibuat pada: 16 Desember 2025
-- Versi: 3.0 (Complete with Relations & Seed Data)
-- Database: db_docutrack2
-- 
-- CARA PENGGUNAAN (Untuk Teman):
-- 1. Buka phpMyAdmin.
-- 2. Buat database baru dengan nama: db_docutrack2
-- 3. Klik database tersebut, pilih tab 'SQL'.
-- 4. Copy semua kode di bawah ini, paste ke kolom SQL, lalu klik 'Go' / 'Kirim'.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0; -- Mematikan cek relasi sementara agar tidak error saat create table
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- 1. PEMBERSIHAN (Hapus tabel lama jika ada)
-- ============================================================
DROP TABLE IF EXISTS `tbl_log_status`;
DROP TABLE IF EXISTS `tbl_tahapan_pencairan`;
DROP TABLE IF EXISTS `tbl_revisi_comment`;
DROP TABLE IF EXISTS `tbl_progress_history`;
DROP TABLE IF EXISTS `tbl_lpj_item`;
DROP TABLE IF EXISTS `tbl_lpj`;
DROP TABLE IF EXISTS `tbl_rab`;
DROP TABLE IF EXISTS `tbl_tahapan_pelaksanaan`;
DROP TABLE IF EXISTS `tbl_indikator_kak`;
DROP TABLE IF EXISTS `tbl_kak`;
DROP TABLE IF EXISTS `tbl_kegiatan`;
DROP TABLE IF EXISTS `tbl_user`;
DROP TABLE IF EXISTS `tbl_prodi`;
DROP TABLE IF EXISTS `tbl_jurusan`;
DROP TABLE IF EXISTS `tbl_role`;
DROP TABLE IF EXISTS `tbl_status_utama`;
DROP TABLE IF EXISTS `tbl_wadir`;
DROP TABLE IF EXISTS `tbl_kategori_rab`;
DROP TABLE IF EXISTS `tbl_activity_logs`;

-- ============================================================
-- 2. TABEL MASTER (Data Referensi Utama)
-- ============================================================

-- [1] Tabel Jurusan
-- Menyimpan daftar jurusan yang ada di politeknik
CREATE TABLE `tbl_jurusan` (
  `namaJurusan` varchar(50) NOT NULL,
  PRIMARY KEY (`namaJurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_jurusan` (`namaJurusan`) VALUES
('Administrasi Niaga'), ('Akuntansi'), ('Pascasarjana'), ('Teknik Elektro'),
('Teknik Grafika dan Penerbitan'), ('Teknik Informatika dan Komputer'), ('Teknik Mesin'), ('Teknik Sipil');

-- [2] Tabel Role
-- Menyimpan hak akses user (Admin, Wadir, PPK, dll)
CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL AUTO_INCREMENT,
  `namaRole` varchar(50) NOT NULL,
  `urutan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Urutan workflow (1=Admin, 2=Verif, dst)',
  `deskripsi` varchar(200) DEFAULT NULL,
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

-- [3] Tabel Status Utama
-- Status global untuk kegiatan (Menunggu, Disetujui, Ditolak, dll)
CREATE TABLE `tbl_status_utama` (
  `statusId` int(11) NOT NULL AUTO_INCREMENT,
  `namaStatusUsulan` varchar(100) NOT NULL,
  PRIMARY KEY (`statusId`),
  UNIQUE KEY `namaStatusUsulan` (`namaStatusUsulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
(1, 'Menunggu'), (2, 'Revisi'), (3, 'Disetujui'), (4, 'Ditolak'),
(5, 'Dana diberikan'), (6, 'Dana belum diberikan semua');

-- [4] Tabel Wadir
-- Daftar Wakil Direktur untuk tujuan disposisi
CREATE TABLE `tbl_wadir` (
  `wadirId` int(11) NOT NULL AUTO_INCREMENT,
  `namaWadir` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`wadirId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES (1, 'Wadir 1'), (2, 'Wadir 2'), (3, 'Wadir 3'), (4, 'Wadir 4');

-- [5] Tabel Kategori RAB
-- Kategori belanja (Barang, Jasa, Perjalanan)
CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` int(11) NOT NULL AUTO_INCREMENT,
  `namaKategori` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`kategoriRabId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES
(4, 'Belanja Barang'), (5, 'Belanja Perjalanan'), (6, 'Belanja Jasa');

-- [6] Tabel Prodi
-- Daftar program studi, terhubung ke Jurusan
CREATE TABLE `tbl_prodi` (
  `namaProdi` varchar(50) NOT NULL,
  `namaJurusan` varchar(50) NOT NULL,
  PRIMARY KEY (`namaProdi`),
  KEY `fk_namaJurusan` (`namaJurusan`),
  CONSTRAINT `tbl_prodi_ibfk_1` FOREIGN KEY (`namaJurusan`) 
    REFERENCES `tbl_jurusan` (`namaJurusan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_prodi` (`namaProdi`, `namaJurusan`) VALUES
('D3 Administrasi Bisnis', 'Administrasi Niaga'), ('D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'),
('D3 Akuntansi', 'Akuntansi'), ('D4 Akuntansi Keuangan', 'Akuntansi'),
('S2 Magister Terapan Teknik Elektro', 'Pascasarjana'),
('D3 Teknik Listrik', 'Teknik Elektro'), ('D4 Broadband Multimedia', 'Teknik Elektro'),
('D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'),
('D4 Teknik Informatika', 'Teknik Informatika dan Komputer'), ('D4 Teknik Multimedia Digital', 'Teknik Informatika dan Komputer'),
('D3 Teknik Mesin', 'Teknik Mesin'), ('D4 Teknik Sipil', 'Teknik Sipil'); 
-- (Sebagian data prodi disederhanakan untuk preview, import file asli jika butuh lengkap)

-- ============================================================
-- 3. TABEL USER & AUTENTIKASI
-- ============================================================

-- [7] Tabel User
-- Menyimpan data login pengguna (Password hash: $2y$10$...)
CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` int(11) NOT NULL,
  `namaJurusan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_role` (`roleId`),
  KEY `namaJurusan` (`namaJurusan`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_user_ibfk_1` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`) VALUES
(1, 'Admin TI', 'adminti@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Informatika dan Komputer'),
(2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Elektro'),
(9, 'Verifikator', 'verifikator@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 2, NULL),
(10, 'Wakil Direktur', 'wadir@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 3, NULL),
(11, 'PPK', 'ppk@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 4, NULL),
(12, 'Bendahara', 'bendahara@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 5, NULL),
(13, 'Super Admin', 'superadmin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 6, NULL);

-- ============================================================
-- 4. TABEL TRANSAKSI UTAMA (Kegiatan & Workflow)
-- ============================================================

-- [8] Tabel Kegiatan
-- Tabel inti, mencatat pengajuan kegiatan dari awal sampai akhir
CREATE TABLE `tbl_kegiatan` (
  `kegiatanId` int(11) NOT NULL AUTO_INCREMENT,
  `namaKegiatan` varchar(255) NOT NULL,
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK Nama Prodi',
  `pemilikKegiatan` varchar(150) DEFAULT NULL,
  `nimPelaksana` varchar(20) DEFAULT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `namaPJ` varchar(100) DEFAULT NULL,
  `buktiMAK` varchar(255) DEFAULT NULL,
  `userId` int(11) NOT NULL COMMENT 'Pembuat kegiatan',
  `jurusanPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK Jurusan',
  `statusUtamaId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status keseluruhan',
  `statusPencairanId` int(11) NOT NULL DEFAULT 6 COMMENT '6: Belum cair semua, 5: Sudah cair',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadAt` timestamp NULL DEFAULT NULL,
  `wadirTujuan` int(11) NOT NULL,
  `suratPengantar` varchar(255) DEFAULT NULL,
  `tanggalMulai` date DEFAULT NULL,
  `tanggalSelesai` date DEFAULT NULL,
  `posisiId` int(11) NOT NULL DEFAULT 1 COMMENT 'Posisi dokumen saat ini',
  `tanggalPencairan` datetime DEFAULT NULL,
  `jumlahDicairkan` int(11) DEFAULT NULL COMMENT 'Dana disetujui bendahara',
  `totalDicairkan` int(11) DEFAULT NULL COMMENT 'Dana real cair',
  `metodePencairan` varchar(50) DEFAULT NULL,
  `catatanBendahara` text DEFAULT NULL,
  `umpanBalikVerifikator` text DEFAULT NULL,
  PRIMARY KEY (`kegiatanId`),
  KEY `idx_user` (`userId`),
  KEY `idx_status` (`statusUtamaId`),
  KEY `idx_posisi` (`posisiId`),
  
  -- Definisi Relasi (Constraints) Langsung Disini --
  CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_posisi_role` FOREIGN KEY (`posisiId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_1` FOREIGN KEY (`prodiPenyelenggara`) REFERENCES `tbl_prodi` (`namaProdi`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_2` FOREIGN KEY (`jurusanPenyelenggara`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_3` FOREIGN KEY (`wadirTujuan`) REFERENCES `tbl_wadir` (`wadirId`) ON DELETE CASCADE,
  CONSTRAINT `tbl_kegiatan_ibfk_4` FOREIGN KEY (`statusPencairanId`) REFERENCES `tbl_status_utama` (`statusId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [9] Tabel Progress History
-- Mencatat riwayat perpindahan status kegiatan (Tracking)
CREATE TABLE `tbl_progress_history` (
  `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `statusId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `changedByUserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`progressHistoryId`),
  KEY `fk_history_kegiatan` (`kegiatanId`),
  CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) 
    REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [10] Tabel Revisi Comment
-- Mencatat detail revisi jika status kegiatan ditolak/revisi
CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT,
  `progressHistoryId` int(11) NOT NULL,
  `komentarRevisi` text DEFAULT NULL,
  `targetTabel` varchar(100) DEFAULT NULL,
  `targetKolom` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`revisiCommentId`),
  CONSTRAINT `fk_comment_to_history` FOREIGN KEY (`progressHistoryId`) 
    REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [11] Tabel Tahapan Pencairan (Baru)
-- Mencatat termin pencairan dana (bertahap)
CREATE TABLE `tbl_tahapan_pencairan` (
  `idTahapan` int(11) NOT NULL AUTO_INCREMENT,
  `idKegiatan` int(11) NOT NULL,
  `tglPencairan` date NOT NULL,
  `termin` varchar(50) NOT NULL COMMENT 'Contoh: Termin 1, Termin 2',
  `nominal` decimal(15,2) NOT NULL,
  `catatan` text NOT NULL,
  `createdBy` int(11) NOT NULL,
  `creatAt` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idTahapan`),
  KEY `fk_tahapan_kegiatan` (`idKegiatan`),
  CONSTRAINT `fk_tahapan_kegiatan` FOREIGN KEY (`idKegiatan`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 5. TABEL DOKUMEN (KAK, RAB, LPJ)
-- ============================================================

-- [12] Tabel KAK (Kerangka Acuan Kerja)
CREATE TABLE `tbl_kak` (
  `kakId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `iku` varchar(200) DEFAULT NULL,
  `penerimaManfaat` text DEFAULT NULL,
  `gambaranUmum` text DEFAULT NULL,
  `metodePelaksanaan` text DEFAULT NULL,
  `tglPembuatan` date DEFAULT NULL,
  PRIMARY KEY (`kakId`),
  CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [13] Tabel Indikator KAK
CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) DEFAULT NULL,
  `bulan` tinyint(3) UNSIGNED DEFAULT NULL,
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL,
  `targetPersen` tinyint(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`indikatorId`),
  CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [14] Tabel Tahapan Pelaksanaan
CREATE TABLE `tbl_tahapan_pelaksanaan` (
  `tahapanId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) DEFAULT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tahapanId`),
  CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [15] Tabel RAB (Rencana Anggaran Biaya)
CREATE TABLE `tbl_rab` (
  `rabItemId` int(11) NOT NULL AUTO_INCREMENT,
  `kakId` int(11) NOT NULL,
  `kategoriId` int(11) NOT NULL,
  `uraian` text DEFAULT NULL,
  `rincian` text DEFAULT NULL,
  `sat1` varchar(50) DEFAULT NULL,
  `sat2` varchar(50) DEFAULT NULL,
  `vol1` decimal(10,2) NOT NULL,
  `vol2` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `totalHarga` int(11) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`rabItemId`),
  CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) 
    REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) 
    REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [16] Tabel LPJ (Laporan Pertanggungjawaban)
CREATE TABLE `tbl_lpj` (
  `lpjId` int(11) NOT NULL AUTO_INCREMENT,
  `kegiatanId` int(11) NOT NULL,
  `grandTotalRealisasi` decimal(15,2) DEFAULT NULL,
  `submittedAt` timestamp NULL DEFAULT NULL,
  `approvedAt` timestamp NULL DEFAULT NULL,
  `tenggatLpj` date DEFAULT NULL,
  PRIMARY KEY (`lpjId`),
  CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) 
    REFERENCES `tbl_kegiatan` (`kegiatanId`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [17] Tabel LPJ Item
-- Rincian belanja realisasi
CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL AUTO_INCREMENT,
  `lpjId` int(11) NOT NULL,
  `kategoriId` int(11) DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `rincian` text DEFAULT NULL,
  `sat1` varchar(50) DEFAULT NULL,
  `sat2` int(50) NOT NULL,
  `vol1` decimal(10,2) NOT NULL,
  `vol2` decimal(10,2) NOT NULL,
  `harga` int(11) DEFAULT NULL,
  `totalHarga` int(11) DEFAULT NULL,
  `fileBukti` varchar(255) DEFAULT NULL,
  `realisasi` int(11) NOT NULL,
  `createAt` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`lpjItemId`),
  CONSTRAINT `fk_item_ke_lpj` FOREIGN KEY (`lpjId`) 
    REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_lpj_item_ibfk_1` FOREIGN KEY (`kategoriId`) 
    REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 6. TABEL LOG & AUDIT
-- ============================================================

-- [18] Tabel Activity Logs
-- Audit trail sistem (Log aktivitas user)
CREATE TABLE `tbl_activity_logs` (
  `logId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(10) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `category` enum('authentication','workflow','document','financial','user_management','security') NOT NULL DEFAULT 'workflow',
  `entityType` varchar(50) DEFAULT NULL,
  `entityId` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `oldValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`oldValue`)),
  `newValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`newValue`)),
  `ipAddress` varchar(45) DEFAULT NULL,
  `userAgent` varchar(500) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logId`),
  KEY `idx_user_action` (`userId`,`action`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- [19] Tabel Log Status (Baru)
-- Notifikasi atau status log spesifik user
CREATE TABLE `tbl_log_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tipe_log` varchar(50) NOT NULL COMMENT 'cth: NOTIFIKASI, REMINDER',
  `id_referensi` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL COMMENT 'cth: BELUM_DIBACA',
  `konten_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`konten_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `tbl_log_status_ibfk_1` FOREIGN KEY (`user_id`) 
    REFERENCES `tbl_user` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- AKHIR SCRIPT
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1; -- Aktifkan kembali cek relasi
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;