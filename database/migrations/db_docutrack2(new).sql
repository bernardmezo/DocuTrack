-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Des 2025 pada 10.59
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_docutrack2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ai_log_summaries`
--

CREATE TABLE `ai_log_summaries` (
  `id` int(11) NOT NULL,
  `summary_text` text NOT NULL,
  `error_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ai_security_alerts`
--

CREATE TABLE `ai_security_alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(255) NOT NULL,
  `input_payload` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL,
  `detection_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_activity_logs`
--

CREATE TABLE `tbl_activity_logs` (
  `logId` int(10) UNSIGNED NOT NULL,
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
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified audit trail - semua aktivitas user tercatat di sini';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_iku`
--

CREATE TABLE `tbl_iku` (
  `id` int(11) NOT NULL,
  `kode_iku` varchar(50) DEFAULT NULL,
  `indikator_kinerja` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `realisasi` varchar(100) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_indikator_kak`
--

CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `bulan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Bulan pelaksanaan (1-12)',
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL COMMENT 'Deskripsi indikator keberhasilan',
  `targetPersen` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Target pencapaian (0-100)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Monthly success indicators for KAK';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_jurusan`
--

CREATE TABLE `tbl_jurusan` (
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for academic departments';

--
-- Dumping data untuk tabel `tbl_jurusan`
--

INSERT INTO `tbl_jurusan` (`namaJurusan`) VALUES
('Administrasi Niaga'),
('Akuntansi'),
('Pascasarjana'),
('Teknik Elektro'),
('Teknik Grafika dan Penerbitan'),
('Teknik Informatika dan Komputer'),
('Teknik Mesin'),
('Teknik Sipil');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kak`
--

CREATE TABLE `tbl_kak` (
  `kakId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `iku` varchar(200) DEFAULT NULL COMMENT 'Indikator Kinerja Utama',
  `penerimaManfaat` text DEFAULT NULL COMMENT 'Penerima manfaat kegiatan',
  `gambaranUmum` text DEFAULT NULL COMMENT 'Gambaran umum kegiatan',
  `metodePelaksanaan` text DEFAULT NULL COMMENT 'Metode pelaksanaan kegiatan',
  `tglPembuatan` date DEFAULT NULL COMMENT 'Tanggal pembuatan KAK'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Terms of Reference (Kerangka Acuan Kerja)';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kategori_rab`
--

CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` int(11) NOT NULL,
  `namaKategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Budget expense categories';

--
-- Dumping data untuk tabel `tbl_kategori_rab`
--

INSERT INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES
(4, 'Belanja Barang'),
(5, 'Belanja Perjalanan'),
(6, 'Belanja Jasa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kegiatan`
--

CREATE TABLE `tbl_kegiatan` (
  `kegiatanId` int(11) NOT NULL,
  `namaKegiatan` varchar(255) NOT NULL,
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke tbl_prodi',
  `pemilikKegiatan` varchar(150) DEFAULT NULL COMMENT 'Nama pemilik/pelaksana kegiatan',
  `nimPelaksana` varchar(20) DEFAULT NULL COMMENT 'NIM pelaksana',
  `nip` varchar(30) DEFAULT NULL COMMENT 'NIP penanggung jawab',
  `namaPJ` varchar(100) DEFAULT NULL COMMENT 'Nama penanggung jawab',
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
  `tanggalPencairan` datetime DEFAULT NULL COMMENT 'Tanggal dana dicairkan (full/first disbursement)',
  `jumlahDicairkan` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `danaDisetujui` decimal(15,2) DEFAULT NULL COMMENT 'Total dana yang sudah disetujui verifikator atas grand total RAB pengusul',
  `metodePencairan` varchar(50) DEFAULT NULL COMMENT 'Metode: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` text DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan',
  `pencairan_tahap_json` text DEFAULT NULL COMMENT 'JSON array untuk pencairan bertahap: [{tahap, tanggal, persentase, jumlah, status}]',
  `umpanBalikVerifikator` text DEFAULT NULL COMMENT 'Umpan balik dari Verifikator saat approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Main activity/proposal table with workflow tracking';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_log_status`
--

CREATE TABLE `tbl_log_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipe_log` varchar(50) NOT NULL COMMENT 'Tipe: NOTIFIKASI_APPROVAL, REMINDER_LPJ, BOOKMARK',
  `id_referensi` int(11) DEFAULT NULL COMMENT 'ID kegiatan, ID LPJ, dll',
  `status` varchar(20) NOT NULL COMMENT 'Status: BELUM_DIBACA, DIBACA, AKTIF',
  `konten_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Konten notifikasi dalam format JSON' CHECK (json_valid(`konten_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Notification system and user-specific status tracking';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lpj`
--

CREATE TABLE `tbl_lpj` (
  `lpjId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `grandTotalRealisasi` int(11) DEFAULT NULL COMMENT 'Total realisasi dari semua item LPJ',
  `submittedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal submit LPJ',
  `approvedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal approve LPJ',
  `tenggatLpj` date DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ',
  `statusId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status LPJ: 1=Menunggu, 2=Revisi, 3=Disetujui, 4=Ditolak',
  `komentarPenolakan` text DEFAULT NULL COMMENT 'Komentar jika LPJ ditolak',
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar untuk revisi LPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Accountability reports (Laporan Pertanggungjawaban)';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lpj_item`
--

CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL,
  `lpjId` int(11) NOT NULL,
  `rabItemId` int(11) DEFAULT NULL COMMENT 'Original ID from tbl_rab',
  `kategoriId` int(11) DEFAULT NULL COMMENT 'FK to tbl_kategori_rab',
  `jenisBelanja` varchar(100) DEFAULT NULL COMMENT 'Jenis belanja/expense type',
  `uraian` text DEFAULT NULL COMMENT 'Deskripsi item',
  `rincian` text DEFAULT NULL COMMENT 'Rincian detail',
  `totalHarga` decimal(15,2) DEFAULT NULL COMMENT 'Total harga item',
  `realisasi` int(11) DEFAULT NULL COMMENT 'Nilai Realisasi',
  `subTotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal',
  `fileBukti` varchar(255) DEFAULT NULL COMMENT 'Nama file bukti/evidence',
  `komentar` text DEFAULT NULL COMMENT 'Komentar untuk item',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `sat1` varchar(50) DEFAULT NULL COMMENT 'Satuan 1',
  `sat2` varchar(50) DEFAULT NULL COMMENT 'Satuan 2',
  `vol1` decimal(10,2) DEFAULT NULL COMMENT 'Volume 1',
  `vol2` decimal(10,2) DEFAULT NULL COMMENT 'Volume 2',
  `harga` decimal(15,2) DEFAULT NULL COMMENT 'Harga Satuan (Plan)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Line items for accountability reports';

--
-- Trigger `tbl_lpj_item`
--
DELIMITER $$
CREATE TRIGGER `trg_lpj_item_calculate_total` BEFORE INSERT ON `tbl_lpj_item` FOR EACH ROW BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL AND NEW.totalHarga IS NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lpj_item_update_total` BEFORE UPDATE ON `tbl_lpj_item` FOR EACH ROW BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_prodi`
--

CREATE TABLE `tbl_prodi` (
  `namaProdi` varchar(50) NOT NULL,
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Study programs under academic departments';

--
-- Dumping data untuk tabel `tbl_prodi`
--

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_progress_history`
--

CREATE TABLE `tbl_progress_history` (
  `progressHistoryId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `statusId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `changedByUserId` int(11) DEFAULT NULL COMMENT 'User ID yang melakukan perubahan status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='History of status changes for activities';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_rab`
--

CREATE TABLE `tbl_rab` (
  `rabItemId` int(11) NOT NULL,
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
  `subtotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal untuk kategori'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Budget plan items (Rencana Anggaran Biaya)';

--
-- Trigger `tbl_rab`
--
DELIMITER $$
CREATE TRIGGER `trg_rab_calculate_total` BEFORE INSERT ON `tbl_rab` FOR EACH ROW BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_rab_update_total` BEFORE UPDATE ON `tbl_rab` FOR EACH ROW BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_revisi_comment`
--

CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` int(11) NOT NULL,
  `progressHistoryId` int(11) NOT NULL,
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar revisi dari approver',
  `targetTabel` varchar(100) DEFAULT NULL COMMENT 'Target table yang perlu direvisi',
  `targetKolom` varchar(100) DEFAULT NULL COMMENT 'Target column yang perlu direvisi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Revision comments for workflow feedback';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_role`
--

CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL,
  `namaRole` varchar(50) NOT NULL,
  `urutan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow (NULL jika bukan bagian workflow)',
  `deskripsi` varchar(200) DEFAULT NULL COMMENT 'Deskripsi peran dalam workflow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for user roles and workflow positions';

--
-- Dumping data untuk tabel `tbl_role`
--

INSERT INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
(3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
(4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(5, 'Bendahara', 5, 'Pencairan dana'),
(6, 'Super Admin', NULL, 'Administrator sistem - tidak dalam workflow'),
(7, 'direktur', NULL, 'direkture');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_status_utama`
--

CREATE TABLE `tbl_status_utama` (
  `statusId` int(11) NOT NULL,
  `namaStatusUsulan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for proposal status';

--
-- Dumping data untuk tabel `tbl_status_utama`
--

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
(5, 'Dana diberikan'),
(3, 'Disetujui'),
(4, 'Ditolak'),
(1, 'Menunggu'),
(2, 'Revisi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_tahapan_pelaksanaan`
--

CREATE TABLE `tbl_tahapan_pelaksanaan` (
  `tahapanId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL COMMENT 'Nama tahapan pelaksanaan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Implementation stages for activities';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_tahapan_pencairan`
--

CREATE TABLE `tbl_tahapan_pencairan` (
  `tahapanId` int(11) NOT NULL,
  `idKegiatan` int(11) NOT NULL,
  `tglPencairan` date NOT NULL,
  `termin` varchar(100) NOT NULL COMMENT 'Nama termin (e.g. Termin 1, Tahap Awal)',
  `nominal` decimal(15,2) NOT NULL,
  `catatan` text DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL COMMENT 'User ID Bendahara',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_user`
--

CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` int(11) NOT NULL,
  `namaJurusan` varchar(50) DEFAULT NULL COMMENT 'Departemen untuk Admin, NULL untuk peran lain',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='System users with role-based access control';

--
-- Dumping data untuk tabel `tbl_user`
--

INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`, `created_at`, `status`) VALUES
(1, 'Admin TIK', 'adminti@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Informatika dan Komputer', '2025-12-14 03:30:47', 'Aktif'),
(3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Sipil', '2025-12-14 03:30:47', 'Aktif'),
(4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Mesin', '2025-12-14 03:30:47', 'Aktif'),
(5, 'Admin Grafika dan Penerbitan', 'admintgp@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Grafika dan Penerbitan', '2025-12-14 03:30:47', 'Aktif'),
(6, 'Admin Akuntansi', 'adminakt@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Akuntansi', '2025-12-14 03:30:47', 'Aktif'),
(7, 'Admin Administrasi Niaga', 'adminan@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Administrasi Niaga', '2025-12-14 03:30:47', 'Aktif'),
(8, 'Admin Pascasarjana', 'adminpasca@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Pascasarjana', '2025-12-14 03:30:47', 'Aktif'),
(9, 'Verifikator', 'verifikator@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 2, NULL, '2025-12-14 03:30:47', 'Aktif'),
(10, 'Wakil Direktur', 'wadir@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 3, NULL, '2025-12-14 03:30:47', 'Aktif'),
(11, 'PPK', 'ppk@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 4, NULL, '2025-12-14 03:30:47', 'Aktif'),
(12, 'Bendahara', 'bendahara@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 5, NULL, '2025-12-14 03:30:47', 'Aktif'),
(13, 'Super Admin', 'superadmin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 6, NULL, '2025-12-14 03:30:47', 'Aktif'),
(17, 'direktur', 'direktur@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 7, 'Administrasi Niaga', '2025-12-18 07:35:39', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_wadir`
--

CREATE TABLE `tbl_wadir` (
  `wadirId` int(11) NOT NULL,
  `namaWadir` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deputy directors for approval routing';

--
-- Dumping data untuk tabel `tbl_wadir`
--

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES
(1, 'Wadir 1'),
(2, 'Wadir 2'),
(3, 'Wadir 3'),
(4, 'Wadir 4');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_kegiatan_detail`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_kegiatan_detail` (
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_lpj_status`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_lpj_status` (
`lpjId` int(11)
,`kegiatanId` int(11)
,`grandTotalRealisasi` int(11)
,`submittedAt` timestamp
,`approvedAt` timestamp
,`tenggatLpj` date
,`statusId` int(11)
,`komentarPenolakan` text
,`komentarRevisi` text
,`namaKegiatan` varchar(255)
,`jurusanPenyelenggara` varchar(50)
,`prodiPenyelenggara` varchar(50)
,`status_lpj` varchar(100)
,`deadline_status` varchar(9)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_kegiatan_detail`
--
DROP TABLE IF EXISTS `vw_kegiatan_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_kegiatan_detail`  AS SELECT `k`.`kegiatanId` AS `kegiatanId`, `k`.`namaKegiatan` AS `namaKegiatan`, `k`.`prodiPenyelenggara` AS `prodiPenyelenggara`, `k`.`pemilikKegiatan` AS `pemilikKegiatan`, `k`.`nimPelaksana` AS `nimPelaksana`, `k`.`nip` AS `nip`, `k`.`namaPJ` AS `namaPJ`, `k`.`danaDiCairkan` AS `danaDiCairkan`, `k`.`buktiMAK` AS `buktiMAK`, `k`.`userId` AS `userId`, `k`.`jurusanPenyelenggara` AS `jurusanPenyelenggara`, `k`.`statusUtamaId` AS `statusUtamaId`, `k`.`createdAt` AS `createdAt`, `k`.`uploadAt` AS `uploadAt`, `k`.`wadirTujuan` AS `wadirTujuan`, `k`.`suratPengantar` AS `suratPengantar`, `k`.`tanggalMulai` AS `tanggalMulai`, `k`.`tanggalSelesai` AS `tanggalSelesai`, `k`.`posisiId` AS `posisiId`, `k`.`tanggalPencairan` AS `tanggalPencairan`, `k`.`jumlahDicairkan` AS `jumlahDicairkan`, `k`.`metodePencairan` AS `metodePencairan`, `k`.`catatanBendahara` AS `catatanBendahara`, `k`.`pencairan_tahap_json` AS `pencairan_tahap_json`, `k`.`umpanBalikVerifikator` AS `umpanBalikVerifikator`, `u`.`nama` AS `admin_nama`, `u`.`email` AS `admin_email`, `s`.`namaStatusUsulan` AS `status_nama`, `r`.`namaRole` AS `posisi_nama`, `w`.`namaWadir` AS `wadir_nama` FROM ((((`tbl_kegiatan` `k` left join `tbl_user` `u` on(`k`.`userId` = `u`.`userId`)) left join `tbl_status_utama` `s` on(`k`.`statusUtamaId` = `s`.`statusId`)) left join `tbl_role` `r` on(`k`.`posisiId` = `r`.`roleId`)) left join `tbl_wadir` `w` on(`k`.`wadirTujuan` = `w`.`wadirId`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_lpj_status`
--
DROP TABLE IF EXISTS `vw_lpj_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_lpj_status`  AS SELECT `l`.`lpjId` AS `lpjId`, `l`.`kegiatanId` AS `kegiatanId`, `l`.`grandTotalRealisasi` AS `grandTotalRealisasi`, `l`.`submittedAt` AS `submittedAt`, `l`.`approvedAt` AS `approvedAt`, `l`.`tenggatLpj` AS `tenggatLpj`, `l`.`statusId` AS `statusId`, `l`.`komentarPenolakan` AS `komentarPenolakan`, `l`.`komentarRevisi` AS `komentarRevisi`, `k`.`namaKegiatan` AS `namaKegiatan`, `k`.`jurusanPenyelenggara` AS `jurusanPenyelenggara`, `k`.`prodiPenyelenggara` AS `prodiPenyelenggara`, `s`.`namaStatusUsulan` AS `status_lpj`, CASE WHEN `l`.`tenggatLpj` < curdate() AND `l`.`statusId` = 1 THEN 'OVERDUE' WHEN `l`.`tenggatLpj` = curdate() AND `l`.`statusId` = 1 THEN 'DUE_TODAY' WHEN `l`.`tenggatLpj` > curdate() AND `l`.`statusId` = 1 THEN 'PENDING' ELSE 'COMPLETED' END AS `deadline_status` FROM ((`tbl_lpj` `l` join `tbl_kegiatan` `k` on(`l`.`kegiatanId` = `k`.`kegiatanId`)) left join `tbl_status_utama` `s` on(`l`.`statusId` = `s`.`statusId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ai_log_summaries`
--
ALTER TABLE `ai_log_summaries`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ai_security_alerts`
--
ALTER TABLE `ai_security_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_security_alerts_created_at` (`created_at`);

--
-- Indeks untuk tabel `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `idx_user_action` (`userId`,`action`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_entity` (`entityType`,`entityId`),
  ADD KEY `idx_created_at` (`createdAt`),
  ADD KEY `idx_action` (`action`);

--
-- Indeks untuk tabel `tbl_iku`
--
ALTER TABLE `tbl_iku`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  ADD PRIMARY KEY (`indikatorId`),
  ADD KEY `fk_indikator_kak` (`kakId`);

--
-- Indeks untuk tabel `tbl_jurusan`
--
ALTER TABLE `tbl_jurusan`
  ADD PRIMARY KEY (`namaJurusan`);

--
-- Indeks untuk tabel `tbl_kak`
--
ALTER TABLE `tbl_kak`
  ADD PRIMARY KEY (`kakId`),
  ADD KEY `fk_kak_kegiatan` (`kegiatanId`);

--
-- Indeks untuk tabel `tbl_kategori_rab`
--
ALTER TABLE `tbl_kategori_rab`
  ADD PRIMARY KEY (`kategoriRabId`);

--
-- Indeks untuk tabel `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  ADD PRIMARY KEY (`kegiatanId`),
  ADD KEY `idx_nimPelaksana` (`nimPelaksana`),
  ADD KEY `idx_nip` (`nip`),
  ADD KEY `fk_kegiatan_user` (`userId`),
  ADD KEY `fk_status_kegiatan` (`statusUtamaId`),
  ADD KEY `fk_kegiatan_jurusan` (`jurusanPenyelenggara`),
  ADD KEY `fk_wadir` (`wadirTujuan`),
  ADD KEY `idx_posisi` (`posisiId`),
  ADD KEY `idx_status` (`statusUtamaId`),
  ADD KEY `idx_created_at` (`createdAt`),
  ADD KEY `idx_tanggal_pencairan` (`tanggalPencairan`),
  ADD KEY `idx_workflow_position` (`posisiId`,`statusUtamaId`,`createdAt`),
  ADD KEY `idx_user_status` (`userId`,`statusUtamaId`),
  ADD KEY `idx_jurusan_status` (`jurusanPenyelenggara`,`statusUtamaId`);

--
-- Indeks untuk tabel `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_user` (`user_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_tipe_log` (`tipe_log`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD PRIMARY KEY (`lpjId`),
  ADD UNIQUE KEY `idx_kegiatan_lpj` (`kegiatanId`),
  ADD KEY `fk_lpj_kegiatan` (`kegiatanId`),
  ADD KEY `fk_lpj_status` (`statusId`),
  ADD KEY `idx_status_tengat` (`statusId`,`tenggatLpj`);

--
-- Indeks untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD PRIMARY KEY (`lpjItemId`),
  ADD KEY `fk_lpj_item_lpj` (`lpjId`),
  ADD KEY `fk_lpj_item_kategori` (`kategoriId`),
  ADD KEY `idx_lpj_rab_item` (`lpjId`,`rabItemId`),
  ADD KEY `rabItemId` (`rabItemId`);

--
-- Indeks untuk tabel `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD PRIMARY KEY (`namaProdi`),
  ADD KEY `fk_prodi_jurusan` (`namaJurusan`);

--
-- Indeks untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD PRIMARY KEY (`progressHistoryId`),
  ADD KEY `fk_history_kegiatan` (`kegiatanId`),
  ADD KEY `fk_history_status` (`statusId`),
  ADD KEY `fk_history_user` (`changedByUserId`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_kegiatan_timestamp` (`kegiatanId`,`timestamp`);

--
-- Indeks untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD PRIMARY KEY (`rabItemId`),
  ADD KEY `fk_rab_kak` (`kakId`),
  ADD KEY `fk_rab_kategori` (`kategoriId`);

--
-- Indeks untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD PRIMARY KEY (`revisiCommentId`),
  ADD KEY `fk_comment_history` (`progressHistoryId`);

--
-- Indeks untuk tabel `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`roleId`),
  ADD UNIQUE KEY `idx_namaRole` (`namaRole`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indeks untuk tabel `tbl_status_utama`
--
ALTER TABLE `tbl_status_utama`
  ADD PRIMARY KEY (`statusId`),
  ADD UNIQUE KEY `idx_namaStatus` (`namaStatusUsulan`);

--
-- Indeks untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD PRIMARY KEY (`tahapanId`),
  ADD KEY `fk_tahapan_kak` (`kakId`);

--
-- Indeks untuk tabel `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  ADD PRIMARY KEY (`tahapanId`),
  ADD KEY `fk_tahapan_kegiatan` (`idKegiatan`);

--
-- Indeks untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `fk_user_role` (`roleId`),
  ADD KEY `fk_user_jurusan` (`namaJurusan`);

--
-- Indeks untuk tabel `tbl_wadir`
--
ALTER TABLE `tbl_wadir`
  ADD PRIMARY KEY (`wadirId`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ai_log_summaries`
--
ALTER TABLE `ai_log_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `ai_security_alerts`
--
ALTER TABLE `ai_security_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  MODIFY `logId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=521;

--
-- AUTO_INCREMENT untuk tabel `tbl_iku`
--
ALTER TABLE `tbl_iku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  MODIFY `indikatorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `tbl_kak`
--
ALTER TABLE `tbl_kak`
  MODIFY `kakId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `tbl_kategori_rab`
--
ALTER TABLE `tbl_kategori_rab`
  MODIFY `kategoriRabId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  MODIFY `kegiatanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  MODIFY `lpjId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  MODIFY `lpjItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  MODIFY `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  MODIFY `rabItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  MODIFY `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `roleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  ADD CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_kak`
--
ALTER TABLE `tbl_kak`
  ADD CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  ADD CONSTRAINT `fk_kegiatan_jurusan` FOREIGN KEY (`jurusanPenyelenggara`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wadir` FOREIGN KEY (`wadirTujuan`) REFERENCES `tbl_wadir` (`wadirId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`userId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lpj_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD CONSTRAINT `fk_lpj_item_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lpj_item_lpj` FOREIGN KEY (`lpjId`) REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_lpj_item_ibfk_1` FOREIGN KEY (`rabItemId`) REFERENCES `tbl_rab` (`rabItemId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`changedByUserId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD CONSTRAINT `fk_comment_history` FOREIGN KEY (`progressHistoryId`) REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  ADD CONSTRAINT `fk_tahapan_kegiatan` FOREIGN KEY (`idKegiatan`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_user_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
