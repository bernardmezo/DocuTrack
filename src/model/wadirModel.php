<?php
// File: src/models/wadirModel.php

class wadirModel {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di wadirModel.");
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    SUM(CASE WHEN posisiId = 3 THEN 1 ELSE 0 END) as total,
                    SUM(CASE WHEN posisiId = 5 AND statusUtamaId != 4 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN posisiId = 3 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * Mengambil daftar usulan (tugas aktif).
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
                WHERE k.posisiId = 3
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) $row['status'] = ucfirst($row['status']);
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Mengambil detail kegiatan.
     */
    public function getDetailKegiatan($kegiatanId) {
        $query = "SELECT k.*, kak.*, s.namaStatusUsulan as status_text
                FROM tbl_kegiatan k
                JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function getIndikatorByKAK($kakId) {
        $query = "SELECT bulan, indikatorKeberhasilan as nama, targetPersen as target FROM tbl_indikator_kak WHERE kakId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $d = []; while($r=mysqli_fetch_assoc($res)) $d[]=$r; return $d;
    }

    public function getTahapanByKAK($kakId) {
        $query = "SELECT namaTahapan FROM tbl_tahapan_pelaksanaan WHERE kakId = ? ORDER BY tahapanId ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $d = []; while($r=mysqli_fetch_assoc($res)) $d[]=$r['namaTahapan']; return $d;
    }

    public function getRABByKAK($kakId) {
        $query = "SELECT r.*, cat.namaKategori FROM tbl_rab r JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId WHERE r.kakId = ? ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $d = []; while($r=mysqli_fetch_assoc($res)) { $d[$r['namaKategori']][] = $r; } return $d;
    }

    /**
     * Menyetujui usulan (Wadir -> Bendahara).
     */
    public function approveUsulan($kegiatanId) {
        $nextPosisi = 5;  // BENDAHARA
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
            error_log("Wadir approveUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil riwayat Wadir.
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
                        WHEN k.posisiId = 5 AND k.statusUtamaId != 4 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE k.posisiId = 5 OR k.statusUtamaId = 4
                  ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        if ($result) { 
            while ($r = mysqli_fetch_assoc($result)) $data[] = $r; 
        }
        return $data;
    }

    /**
     * Mengambil data monitoring.
     */
    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter) {
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
                        ELSE 'In Process'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 1=1 ";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $query .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')";
        }

        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $query .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $query .= " AND k.posisiId = 5";
            } elseif ($statusFilter === 'menunggu') {
                $query .= " AND k.posisiId = 3";
            } elseif ($statusFilter === 'in process') {
                $query .= " AND k.statusUtamaId != 4 AND k.posisiId != 5";
            }
        }

        if ($jurusanFilter !== 'semua') {
            $jurusanFilter = mysqli_real_escape_string($this->db, $jurusanFilter);
            $query .= " AND k.jurusanPenyelenggara = '$jurusanFilter'";
        }

        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k WHERE 1=1 ";
        if (!empty($search)) { $countQuery .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')"; }
        if ($statusFilter !== 'semua') {
             if ($statusFilter === 'ditolak') $countQuery .= " AND k.statusUtamaId = 4";
             elseif ($statusFilter === 'approved') $countQuery .= " AND k.posisiId = 5";
             elseif ($statusFilter === 'menunggu') $countQuery .= " AND k.posisiId = 3";
             elseif ($statusFilter === 'in process') $countQuery .= " AND k.statusUtamaId != 4 AND k.posisiId != 5";
        }
        if ($jurusanFilter !== 'semua') { $countQuery .= " AND k.jurusanPenyelenggara = '$jurusanFilter'"; }

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
?>