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
-- Struktur dari tabel `tbl_indikator_kak`
--

CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL,
  `kakId` int(11) DEFAULT NULL,
  `bulan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Bulan pelaksanaan (1-12)',
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL,
  `targetPersen` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Target pencapaian (0-100)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `penerimaMaanfaat` text DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `gambaranUmum` text DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `metodePelaksanaan` text DEFAULT NULL COMMENT 'Upgraded from VARCHAR(300)',
  `tglPembuatan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK_Nama Prodi penyelenggara',
  `pemilikKegiatan` varchar(150) DEFAULT NULL,
  `nimPelaksana` varchar(20) DEFAULT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `namaPJ` varchar(100) DEFAULT NULL,
  `danaDiCairkan` decimal(15,2) DEFAULT NULL,
  `buktiMAK` varchar(255) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `jurusanPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke Jurusan',
  `statusUtamaId` int(11) NOT NULL DEFAULT 1,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadAt` timestamp NULL DEFAULT NULL,
  `wadirTujuan` int(11) NOT NULL,
  `suratPengantar` varchar(255) DEFAULT NULL COMMENT 'Nama file surat pengantar (max 255 chars)',
  `tanggalMulai` date DEFAULT NULL,
  `tanggalSelesai` date DEFAULT NULL,
  `posisiId` int(11) NOT NULL DEFAULT 1 COMMENT 'Posisi workflow: 1=Admin, 2=Verifikator, 4=PPK, 3=Wadir, 5=Bendahara',
  `tanggalPencairan` datetime DEFAULT NULL COMMENT 'Tanggal dana dicairkan oleh Bendahara',
  `jumlahDicairkan` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `metodePencairan` varchar(50) DEFAULT NULL COMMENT 'Metode: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` text DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan',
  `umpanBalikVerifikator` text DEFAULT NULL COMMENT 'Umpan balik/instruksi dari Verifikator untuk Admin saat menyetujui usulan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tenggatLpj` date DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ',
  `grandTotal` decimal(15,2) DEFAULT 0.00 COMMENT 'Total keseluruhan realisasi LPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lpj_item`
--

CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL,
  `lpjId` int(11) NOT NULL,
  `jenisBelanja` varchar(100) DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `rincian` text DEFAULT NULL,
  `totalHarga` decimal(15,2) DEFAULT NULL,
  `subTotal` decimal(15,2) DEFAULT NULL,
  `fileBukti` varchar(255) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `sat1` varchar(50) DEFAULT NULL,
  `sat2` varchar(50) DEFAULT NULL,
  `vol1` decimal(10,2) DEFAULT NULL,
  `vol2` decimal(10,2) DEFAULT NULL
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
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `changedByUserId` int(11) DEFAULT NULL COMMENT 'User ID yang melakukan perubahan status'
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
  `namaRole` varchar(50) NOT NULL,
  `urutan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow (NULL jika bukan bagian workflow)',
  `deskripsi` varchar(200) DEFAULT NULL COMMENT 'Deskripsi peran dalam workflow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_role`
--

INSERT INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
(3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
(4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(5, 'Bendahara', 5, 'Pencairan dana'),
(6, 'Super Admin', NULL, 'Administrator sistem - tidak dalam workflow');

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
  `kakId` int(11) DEFAULT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL
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
  `namaJurusan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_user`
--

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
  ADD KEY `prodiPenyelenggara` (`prodiPenyelenggara`,`jurusanPenyelenggara`),
  ADD KEY `jurusanPenyelenggara` (`jurusanPenyelenggara`),
  ADD KEY `fk_wadir` (`wadirTujuan`),
  ADD KEY `idx_posisi` (`posisiId`),
  ADD KEY `idx_status` (`statusUtamaId`),
  ADD KEY `idx_created_at` (`createdAt`),
  ADD KEY `idx_jurusan_status` (`jurusanPenyelenggara`,`statusUtamaId`);

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
  ADD KEY `lpjId` (`lpjId`);

--
-- Indeks untuk tabel `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD PRIMARY KEY (`namaProdi`),
  ADD KEY `fk_namaJurusan` (`namaJurusan`);

--
-- Indeks untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD PRIMARY KEY (`progressHistoryId`),
  ADD KEY `fk_history_kegiatan` (`kegiatanId`),
  ADD KEY `fk_history_status` (`statusId`),
  ADD KEY `fk_history_user` (`changedByUserId`);

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
-- AUTO_INCREMENT untuk tabel `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  MODIFY `logId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=416;

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
-- AUTO_INCREMENT untuk tabel `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  MODIFY `lpjId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  MODIFY `lpjItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  MODIFY `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `tbl_rab`
--
ALTER TABLE `tbl_rab`
  MODIFY `rabItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  MODIFY `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `roleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_status_utama`
--
ALTER TABLE `tbl_status_utama`
  MODIFY `statusId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tbl_wadir`
--
ALTER TABLE `tbl_wadir`
  MODIFY `wadirId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `tbl_lpj_item_ibfk_1` FOREIGN KEY (`lpjId`) REFERENCES `tbl_lpj` (`lpjId`);

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
