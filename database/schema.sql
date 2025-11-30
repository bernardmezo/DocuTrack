-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Nov 2025 pada 16.57
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
-- Struktur dari tabel `tbl_indikator_kak`
--

CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL,
  `kakId` int(11) DEFAULT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL,
  `targetPersen` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_indikator_kak`
--

INSERT INTO `tbl_indikator_kak` (`indikatorId`, `kakId`, `bulan`, `indikatorKeberhasilan`, `targetPersen`) VALUES
(40, 37, '1', 'build dari nol', '20'),
(41, 38, '2', 'build dari nol', '20'),
(42, 39, '1', 'build dari nol', '20'),
(43, 40, '1', 'build dari nol', '20'),
(44, 41, '1', 'build dari nol', '20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_jurusan`
--

CREATE TABLE `tbl_jurusan` (
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel master untuk data Jurusan';

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
  `iku` varchar(200) DEFAULT NULL,
  `penerimaMaanfaat` varchar(300) DEFAULT NULL,
  `gambaranUmum` varchar(300) DEFAULT NULL,
  `metodePelaksanaan` varchar(300) DEFAULT NULL,
  `tglPembuatan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_kak`
--

INSERT INTO `tbl_kak` (`kakId`, `kegiatanId`, `iku`, `penerimaMaanfaat`, `gambaranUmum`, `metodePelaksanaan`, `tglPembuatan`) VALUES
(37, 54, 'Prestasi', 'Banyak', 'Lomba G', 'Gas gas aja', '2025-11-30'),
(38, 55, 'Prestasi', 'Banyak banget', 'Lomba H', 'Gas gas aja', '2025-11-30'),
(39, 56, 'Prestasi', 'Banyak banget', 'Lomba I', 'Gas gas aja', '2025-11-30'),
(40, 57, 'Prestasi', 'Banyak banget', 'Lomba J', 'gas gas aja', '2025-11-30'),
(41, 58, 'Prestasi', 'banyak banget', 'Lomba K', 'gas gas aja', '2025-11-30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kategori_rab`
--

CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` int(11) NOT NULL,
  `namaKategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK_Nama Prodi penyelenggara, diisi via dropdown di UI',
  `pemilikKegiatan` varchar(255) DEFAULT NULL,
  `nimPelaksana` varchar(255) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `namaPJ` varchar(255) DEFAULT NULL,
  `danaDiCairkan` decimal(15,2) DEFAULT NULL,
  `buktiMAK` varchar(255) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `jurusanPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke Jurusan, menunjukkan asal jurusan pengusul',
  `statusUtamaId` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadAt` timestamp NULL DEFAULT NULL,
  `wadirTujuan` int(11) NOT NULL,
  `suratPengantar` varchar(50) DEFAULT NULL,
  `tanggalMulai` date DEFAULT NULL,
  `tanggalSelesai` date DEFAULT NULL,
  `posisiId` int(11) NOT NULL DEFAULT 1 COMMENT 'Mengacu pada roleId yang sedang bertugas memproses',
  `tanggalPencairan` datetime DEFAULT NULL COMMENT 'Tanggal dana dicairkan oleh Bendahara',
  `jumlahDicairkan` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `metodePencairan` varchar(50) DEFAULT NULL COMMENT 'Metode pencairan: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` text DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_kegiatan`
--

INSERT INTO `tbl_kegiatan` (`kegiatanId`, `namaKegiatan`, `prodiPenyelenggara`, `pemilikKegiatan`, `nimPelaksana`, `nip`, `namaPJ`, `danaDiCairkan`, `buktiMAK`, `userId`, `jurusanPenyelenggara`, `statusUtamaId`, `createdAt`, `uploadAt`, `wadirTujuan`, `suratPengantar`, `tanggalMulai`, `tanggalSelesai`, `posisiId`) VALUES
(54, 'Lomba G', 'D3 Teknik Mesin', 'Fadhil', '24074510155', '2407211018', 'Ilyas.MpD', NULL, '12121212121212121212', 1, 'Teknik Mesin', 1, '2025-11-29 21:16:28', NULL, 1, 'surat_54_1764472706.docx', '2025-11-28', '2025-11-29', 5),
(55, 'Lomba H', 'D3 Konstruksi Gedung', 'Dika', '24074510152', '2407211011', 'Riza.MpD', NULL, '12121212121', 1, 'Teknik Sipil', 1, '2025-11-29 22:11:19', NULL, 1, 'surat_55_1764479695.docx', '2025-11-28', '2025-11-29', 5),
(56, 'Lomba I', 'D3 Alat Berat', 'Raja', '24074510151', '2407211021', 'Riyan.MpD', NULL, '2121212121212121212121', 1, 'Teknik Mesin', 1, '2025-11-29 23:09:32', NULL, 1, 'surat_56_1764494433.docx', '0000-00-00', '0000-00-00', 5),
(57, 'Lomba J', 'D3 Konstruksi Sipil', 'Hana', '2407411070', '2407211027', 'Dffa.MpD', NULL, '436546823632463254', 1, 'Teknik Sipil', 1, '2025-11-30 05:05:04', NULL, 1, 'surat_57_1764500861.pdf', '2025-11-28', '2025-11-29', 3),
(58, 'Lomba K', 'D3 Teknik Konversi Energi', 'Lintang', '2407411038', NULL, NULL, NULL, NULL, 1, 'Teknik Mesin', 1, '2025-11-30 05:06:30', NULL, 1, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lpj`
--

CREATE TABLE `tbl_lpj` (
  `lpjId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `grandTotalRealisasi` decimal(15,2) DEFAULT NULL,
  `submittedAt` timestamp NULL DEFAULT NULL,
  `approvedAt` timestamp NULL DEFAULT NULL,
  `tenggatLpj` date DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lpj_item`
--

CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL,
  `lpjId` int(11) NOT NULL,
  `jenisBelanja` varchar(150) DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `rincian` text DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `totalHarga` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `fileBukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_prodi`
--

CREATE TABLE `tbl_prodi` (
  `namaProdi` varchar(50) NOT NULL,
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_rab`
--

CREATE TABLE `tbl_rab` (
  `rabItemId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `kategoriId` int(11) NOT NULL,
  `uraian` text DEFAULT NULL,
  `rincian` text DEFAULT NULL,
  `sat1` varchar(50) DEFAULT NULL,
  `sat2` varchar(50) DEFAULT NULL,
  `vol1` decimal(10,2) NOT NULL,
  `vol2` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `totalHarga` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_rab`
--

INSERT INTO `tbl_rab` (`rabItemId`, `kakId`, `kategoriId`, `uraian`, `rincian`, `sat1`, `sat2`, `vol1`, `vol2`, `harga`, `totalHarga`, `subtotal`) VALUES
(34, 37, 4, 'Konsumsi Rapat', 'Snack Box', 'Org', 'Kali', 50.00, 3.00, 15000.00, 2250000.00, NULL),
(35, 37, 6, 'Sewa Peralatan', 'Sewa Sound System', 'Paket', 'Hari', 1.00, 2.00, 500000.00, 1000000.00, NULL),
(36, 38, 4, 'Konsumsi Rapat', 'Snack Box', 'Org', 'Kali', 50.00, 3.00, 15000.00, 2250000.00, NULL),
(37, 38, 6, 'Sewa Peralatan', 'Sewa Sound System', 'Paket', 'Hari', 1.00, 2.00, 500000.00, 1000000.00, NULL),
(38, 39, 4, 'Konsumsi Rapat', 'Snack Box', 'Org', 'Kali', 50.00, 3.00, 15000.00, 2250000.00, NULL),
(39, 39, 6, 'Sewa Peralatan', 'Sewa Sound System', 'Paket', 'Hari', 1.00, 2.00, 500000.00, 1000000.00, NULL),
(40, 40, 4, 'Konsumsi Rapat', 'Snack Box', 'Org', 'Kali', 50.00, 3.00, 15000.00, 2250000.00, NULL),
(41, 40, 6, 'Sewa Peralatan', 'Sewa Sound System', 'Paket', 'Hari', 1.00, 2.00, 500000.00, 1000000.00, NULL),
(42, 41, 4, 'Konsumsi Rapat', 'Snack Box', 'Org', 'Kali', 50.00, 3.00, 15000.00, 2250000.00, NULL),
(43, 41, 6, 'Sewa Peralatan', 'Sewa Sound System', 'Paket', 'Hari', 1.00, 2.00, 500000.00, 1000000.00, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_rancangan_kegiatan`
--

CREATE TABLE `tbl_rancangan_kegiatan` (
  `kegiatanId` int(11) NOT NULL,
  `fileSuratPengantar` varchar(255) DEFAULT NULL,
  `tglMulai` date DEFAULT NULL,
  `tglSelesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_revisi_comment`
--

CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` int(11) NOT NULL,
  `progressHistoryId` int(11) NOT NULL,
  `komentarRevisi` text DEFAULT NULL,
  `targetTabel` varchar(100) DEFAULT NULL,
  `targetKolom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_role`
--

CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL,
  `namaRole` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_role`
--

INSERT INTO `tbl_role` (`roleId`, `namaRole`) VALUES
(1, 'Admin'),
(5, 'Bendahara'),
(4, 'PPK'),
(6, 'Super Admin'),
(2, 'Verifikator'),
(3, 'Wadir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_status_utama`
--

CREATE TABLE `tbl_status_utama` (
  `statusId` int(11) NOT NULL,
  `namaStatusUsulan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_status_utama`
--

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
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
  `kakId` int(11) DEFAULT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_tahapan_pelaksanaan`
--

INSERT INTO `tbl_tahapan_pelaksanaan` (`tahapanId`, `kakId`, `namaTahapan`) VALUES
(52, 37, 'ikuti lombanya'),
(53, 38, 'daftar lombanya'),
(54, 39, 'daftar lombanya'),
(55, 40, 'daftar lombanya'),
(56, 41, 'daftar lombanya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_user`
--

CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` int(11) NOT NULL,
  `namaJurusan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_user`
--

INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`) VALUES
(1, 'Admin TI', 'adminti@gmail.com', 'admincantik123', 1, 'Teknik Informatika dan Komputer'),
(2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', 'admincantik123', 1, 'Teknik Elektro'),
(3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', 'admincantik123', 1, 'Teknik Sipil'),
(4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', 'admincantik123', 1, 'Teknik Mesin'),
(5, 'Admin Grafika dan Penerbitan', 'admintgp@gmail.com', 'admincantik123', 1, 'Teknik Grafika dan Penerbitan'),
(6, 'Admin Akutansi', 'adminakt@gmail.com', 'admincantik123', 1, 'Akuntansi'),
(7, 'Admin Administrasi Niaga', 'adminan@gmail.com', 'admincantik123', 1, 'Administrasi Niaga'),
(8, 'Verifikator', 'verifikator@gmail.com', 'verifikatorganteng', 2, NULL),
(9, 'PPK', 'ppk@gmail.com', 'ppkganteng', 4, NULL),
(10, 'Wakil Direktur', 'wadir@gmail.com', 'wadirganteng', 3, NULL),
(11, 'Bendahara', 'bendahara@gmail.com', 'bendaharaganteng', 5, NULL),
(13, 'superAdmin', 'superadmin@gmail.com', 'superadmin', 6, 'Teknik Sipil');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_wadir`
--

CREATE TABLE `tbl_wadir` (
  `wadirId` int(11) NOT NULL,
  `namaWadir` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_wadir`
--

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES
(1, 'Wadir 1'),
(2, 'Wadir 2'),
(3, 'Wadir 3'),
(4, 'Wadir 4');

--
-- Indexes for dumped tables
--

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
  ADD UNIQUE KEY `nimPelaksana` (`nimPelaksana`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `fk_kegiatan_user` (`userId`),
  ADD KEY `fk_status_kegiatan` (`statusUtamaId`),
  ADD KEY `prodiPenyelenggara` (`prodiPenyelenggara`,`jurusanPenyelenggara`),
  ADD KEY `jurusanPenyelenggara` (`jurusanPenyelenggara`),
  ADD KEY `fk_wadir` (`wadirTujuan`),
  ADD KEY `posisiId` (`posisiId`);

--
-- Indeks untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD PRIMARY KEY (`lpjId`),
  ADD KEY `fk_lpj_kegiatan` (`kegiatanId`);

--
-- Indeks untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD PRIMARY KEY (`lpjItemId`),
  ADD KEY `fk_item_ke_lpj` (`lpjId`);

--
-- Indeks untuk tabel `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD PRIMARY KEY (`namaProdi`),
  ADD KEY `prodiId` (`namaProdi`),
  ADD KEY `fk_namaJurusan` (`namaJurusan`) USING BTREE;

--
-- Indeks untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD PRIMARY KEY (`progressHistoryId`),
  ADD KEY `fk_history_kegiatan` (`kegiatanId`),
  ADD KEY `fk_history_status` (`statusId`);

--
-- Indeks untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD PRIMARY KEY (`rabItemId`),
  ADD KEY `fk_rab_kak` (`kakId`),
  ADD KEY `fk_rab_kategori` (`kategoriId`);

--
-- Indeks untuk tabel `tbl_rancangan_kegiatan`
--
ALTER TABLE `tbl_rancangan_kegiatan`
  ADD PRIMARY KEY (`kegiatanId`);

--
-- Indeks untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD PRIMARY KEY (`revisiCommentId`),
  ADD KEY `fk_comment_to_history` (`progressHistoryId`);

--
-- Indeks untuk tabel `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`roleId`),
  ADD UNIQUE KEY `namaRole` (`namaRole`);

--
-- Indeks untuk tabel `tbl_status_utama`
--
ALTER TABLE `tbl_status_utama`
  ADD PRIMARY KEY (`statusId`),
  ADD UNIQUE KEY `namaStatusUsulan` (`namaStatusUsulan`);

--
-- Indeks untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD PRIMARY KEY (`tahapanId`),
  ADD KEY `fk_tahapan_kak` (`kakId`);

--
-- Indeks untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_role` (`roleId`),
  ADD KEY `namaJurusan` (`namaJurusan`);

--
-- Indeks untuk tabel `tbl_wadir`
--
ALTER TABLE `tbl_wadir`
  ADD PRIMARY KEY (`wadirId`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  MODIFY `indikatorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `tbl_kak`
--
ALTER TABLE `tbl_kak`
  MODIFY `kakId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `tbl_kategori_rab`
--
ALTER TABLE `tbl_kategori_rab`
  MODIFY `kategoriRabId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  MODIFY `kegiatanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  MODIFY `lpjId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  MODIFY `lpjItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  MODIFY `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  MODIFY `rabItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  MODIFY `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `roleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_status_utama`
--
ALTER TABLE `tbl_status_utama`
  MODIFY `statusId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  ADD CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_kak`
--
ALTER TABLE `tbl_kak`
  ADD CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  ADD CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_posisi_role` FOREIGN KEY (`posisiId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_kegiatan_ibfk_1` FOREIGN KEY (`prodiPenyelenggara`) REFERENCES `tbl_prodi` (`namaProdi`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_kegiatan_ibfk_2` FOREIGN KEY (`jurusanPenyelenggara`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_kegiatan_ibfk_3` FOREIGN KEY (`wadirTujuan`) REFERENCES `tbl_wadir` (`wadirId`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD CONSTRAINT `fk_item_ke_lpj` FOREIGN KEY (`lpjId`) REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD CONSTRAINT `tbl_prodi_ibfk_1` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_rancangan_kegiatan`
--
ALTER TABLE `tbl_rancangan_kegiatan`
  ADD CONSTRAINT `fk_rancangan_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD CONSTRAINT `fk_comment_to_history` FOREIGN KEY (`progressHistoryId`) REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_ibfk_1` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
