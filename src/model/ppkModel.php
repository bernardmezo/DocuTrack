<?php
// File: src/models/ppkModel.php

class ppkModel {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di ppkModel.");
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    sum(CASE WHEN posisiId = 4 THEN 1 ELSE 0 END) as total,
                    SUM(CASE WHEN posisiId IN (3, 5) THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN posisiId = 4 OR posisiId = 2 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return [
                'total' => $row['total'] ?? 0,
                'disetujui' => $row['disetujui'] ?? 0,
                'menunggu' => $row['menunggu'] ?? 0
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'menunggu' => 0];
    }

    /**
     * Mengambil daftar usulan untuk tabel dashboard.
     */
    public function getDashboardKAK() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    s.namaStatusUsulan as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 4
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']); 
                } else {
                    $row['status'] = 'Menunggu';
                }
                
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Mengambil detail utama kegiatan.
     */
    public function getDetailKegiatan($kegiatanId) {
        $query = "SELECT 
                    k.*, 
                    kak.*,
                    s.namaStatusUsulan as status_text
                FROM tbl_kegiatan k
                JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Mengambil indikator KAK.
     */
    public function getIndikatorByKAK($kakId) {
        $query = "SELECT bulan, indikatorKeberhasilan as nama, targetPersen as target FROM tbl_indikator_kak WHERE kakId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) { $data[] = $row; }
        return $data;
    }

    /**
     * Mengambil tahapan pelaksanaan.
     */
    public function getTahapanByKAK($kakId) {
        $query = "SELECT namaTahapan FROM tbl_tahapan_pelaksanaan WHERE kakId = ? ORDER BY tahapanId ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) { $data[] = $row['namaTahapan']; }
        return $data;
    }

    /**
     * Mengambil RAB (dikelompokkan berdasarkan kategori).
     */
    public function getRABByKAK($kakId) {
        $query = "SELECT r.*, cat.namaKategori 
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  WHERE r.kakId = ?
                  ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['namaKategori']][] = $row;
        }
        return $data;
    }

    /**
     * Menyetujui usulan.
     */
    public function approveUsulan($kegiatanId) {
        $nextPosisi = 3;  // WADIR
        $resetStatus = 1; // Menunggu
        $statusDisetujui = 3; // Status untuk history
        
        mysqli_begin_transaction($this->db);
        
        try {
            $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $nextPosisi, $resetStatus, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);
            
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, timestamp) VALUES (?, ?, NOW())";
            $stmtHistory = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHistory, "ii", $kegiatanId, $statusDisetujui);
            mysqli_stmt_execute($stmtHistory);
            mysqli_stmt_close($stmtHistory);
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("PPK approveUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil data riwayat PPK.
     */
    public function getRiwayat() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    CASE 
                        WHEN k.posisiId IN (3, 5) AND k.statusUtamaId != 4 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 
                    k.posisiId IN (3, 5) OR k.statusUtamaId = 4
                  ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Mengambil data monitoring untuk PPK dengan filtering dan pagination.
     *
     * Method ini mengambil data kegiatan untuk monitoring dengan berbagai filter:
     * - 'menunggu': Hanya usulan yang posisiId = 4 DAN statusUtamaId = 1 (menunggu approval PPK)
     * - 'approved': Usulan yang sudah disetujui (posisiId >= 5)
     * - 'ditolak': Usulan yang ditolak (statusUtamaId = 4)
     * - 'in process': Usulan yang masih dalam proses
     *
     * @param int $page Halaman saat ini untuk pagination
     * @param int $perPage Jumlah item per halaman
     * @param string $search Kata kunci pencarian (nama kegiatan atau pengusul)
     * @param string $statusFilter Filter status: 'semua', 'menunggu', 'approved', 'ditolak', 'in process'
     * @param string $jurusanFilter Filter jurusan: 'semua' atau nama jurusan spesifik
     * @return array Array dengan key 'data' (list kegiatan) dan 'totalItems' (total records)
     */
    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter)
    {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal,
                    k.posisiId,
                    k.statusUtamaId,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                        WHEN k.posisiId = 1 THEN 'Pengajuan'
                        WHEN k.posisiId = 2 THEN 'Verifikasi'
                        WHEN k.posisiId = 4 THEN 'ACC PPK'
                        WHEN k.posisiId = 3 THEN 'ACC WD'
                        WHEN k.posisiId = 5 THEN 'Dana Cair'
                        ELSE 'Unknown'
                    END as tahap_sekarang,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.posisiId = 5 THEN 'Approved'
                        WHEN k.posisiId = 4 AND k.statusUtamaId = 1 THEN 'Menunggu'
                        ELSE 'In Process'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 1=1 ";
        
        // Filter pencarian (escaped untuk mencegah SQL injection)
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $query .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')";
        }

        // Filter status dengan logic yang diperbaiki
        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $query .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $query .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                // FIXED: Menunggu = posisi di PPK (4) DAN statusUtama = 1 (menunggu approval)
                $query .= " AND k.posisiId = 4 AND k.statusUtamaId = 1";
            } elseif ($statusFilter === 'in process') {
                $query .= " AND k.statusUtamaId != 4 AND k.posisiId < 5";
            }
        }

        // Filter jurusan
        if ($jurusanFilter !== 'semua') {
            $jurusanFilter = mysqli_real_escape_string($this->db, $jurusanFilter);
            $query .= " AND k.jurusanPenyelenggara = '$jurusanFilter'";
        }

        // Build count query dengan filter yang sama
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k WHERE 1=1 ";
        
        if (!empty($search)) {
            $countQuery .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')";
        }
        
        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $countQuery .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $countQuery .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                // FIXED: Konsisten dengan main query
                $countQuery .= " AND k.posisiId = 4 AND k.statusUtamaId = 1";
            } elseif ($statusFilter === 'in process') {
                $countQuery .= " AND k.statusUtamaId != 4 AND k.posisiId < 5";
            }
        }
        
        if ($jurusanFilter !== 'semua') {
            $countQuery .= " AND k.jurusanPenyelenggara = '$jurusanFilter'";
        }

        $query .= " ORDER BY k.createdAt DESC LIMIT $perPage OFFSET $offset";

        $totalResult = mysqli_query($this->db, $countQuery);
        $totalItems = ($totalResult) ? mysqli_fetch_assoc($totalResult)['total'] : 0;

        $result = mysqli_query($this->db, $query);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return [
            'data' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getListJurusanDistinct() {
        $query = "SELECT DISTINCT jurusanPenyelenggara as jurusan FROM tbl_kegiatan WHERE jurusanPenyelenggara IS NOT NULL AND jurusanPenyelenggara != '' ORDER BY jurusanPenyelenggara ASC";
        $result = mysqli_query($this->db, $query);
        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['jurusan'];
            }
        }
        return $list;
    }
}
