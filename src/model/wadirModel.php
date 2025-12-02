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
     * 1. STATISTIK DASHBOARD
     */
    public function getDashboardStats() {
        // ID ROLE: 3=Wadir, 5=Bendahara
        
        $query = "SELECT 
                    SUM(CASE WHEN posisiId = 3 THEN 1 ELSE 0 END) as total,
                    -- Disetujui Wadir: Jika posisi sudah di Bendahara (5)
                    SUM(CASE WHEN posisiId = 5 AND statusUtamaId != 4 THEN 1 ELSE 0 END) as disetujui,
                    -- Menunggu: Sedang di meja Wadir (Posisi = 3)
                    SUM(CASE WHEN posisiId = 3 THEN 1 ELSE 0 END) as menunggu

                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * 2. LIST USULAN (ACTIVE TASKS)
     * Hanya mengambil yang posisiId = 3 (Wadir)
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
                WHERE k.posisiId = 3  -- KHUSUS WADIR
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) $row['status'] = ucfirst($row['status']);
                // Handle null
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * 3. DETAIL KEGIATAN (Generic)
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
     * 4. APPROVE USULAN (WADIR -> BENDAHARA)
     */
    public function approveUsulan($kegiatanId) {
        // LOGIKA ESTAFET WADIR:
        // Posisi: Pindah ke Bendahara (ID 5)
        // Status: Reset ke Menunggu (ID 1)
        
        $nextPosisi = 5;  // BENDAHARA
        $resetStatus = 1; // Menunggu
        $statusDisetujui = 3; // Status untuk history
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update posisi kegiatan ke Bendahara
            $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $nextPosisi, $resetStatus, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);
            
            // 2. Catat ke tbl_progress_history
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
     * 4B. REJECT USULAN (WADIR)
     * Kembalikan ke Admin (posisiId = 1) dengan status Revisi (2)
     * 
     * @param int $kegiatanId ID Kegiatan
     * @param string $alasanPenolakan Alasan penolakan (opsional)
     * @return bool
     */
    public function rejectUsulan($kegiatanId, $alasanPenolakan = '') {
        // LOGIKA REJECT WADIR:
        // Posisi: Kembalikan ke Admin (ID 1)
        // Status: Revisi (ID 2) agar bisa diperbaiki
        
        $backToPosisi = 1;    // Kembalikan ke Admin
        $statusRevisi = 2;    // Status Revisi
        $currentPosisi = 3;   // Wadir
        $userId = $_SESSION['user_id'] ?? null;
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update posisi kegiatan kembali ke Admin
            $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $backToPosisi, $statusRevisi, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);
            
            // 2. Catat ke tbl_progress_history
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, fromPosisi, toPosisi, userId, aksi, timestamp) 
                             VALUES (?, ?, ?, ?, ?, 'reject', NOW())";
            $stmtHistory = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHistory, "iiiii", $kegiatanId, $statusRevisi, $currentPosisi, $backToPosisi, $userId);
            mysqli_stmt_execute($stmtHistory);
            $historyId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmtHistory);
            
            // 3. Simpan komentar penolakan jika ada
            if (!empty($alasanPenolakan) && $historyId) {
                $commentQuery = "INSERT INTO tbl_komentar_revisi (historyId, userId, roleId, komentar, createdAt) 
                                 VALUES (?, ?, 3, ?, NOW())";
                $stmtComment = mysqli_prepare($this->db, $commentQuery);
                mysqli_stmt_bind_param($stmtComment, "iis", $historyId, $userId, $alasanPenolakan);
                mysqli_stmt_execute($stmtComment);
                mysqli_stmt_close($stmtComment);
            }
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("Wadir rejectUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 5. RIWAYAT WADIR
     * Mengambil yang sudah lewat Wadir (Posisi = 5) ATAU Ditolak
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
     * ====================================================
     * 6. MONITORING DATA (SAMA DENGAN PPK)
     * ====================================================
     */
    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter) {
        $offset = ($page - 1) * $perPage;
        
        // Base Query
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
                    
                    -- MAPPING PROGRESS (Sesuai Flowchart)
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.posisiId = 1 THEN 'Pengajuan'    
                        WHEN k.posisiId = 2 THEN 'Verifikasi'   
                        WHEN k.posisiId = 4 THEN 'ACC PPK'      
                        WHEN k.posisiId = 3 THEN 'ACC WD'       -- Konsisten dengan JS ('ACC WD')
                        WHEN k.posisiId = 5 THEN 'Dana Cair'    
                        ELSE 'Unknown'
                    END as tahap_sekarang,

                    -- Status Label
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.posisiId = 5 THEN 'Approved'
                        ELSE 'In Process'
                    END as status

                  FROM tbl_kegiatan k
                  WHERE 1=1 ";
        
        // Filter Search
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $query .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')";
        }

        // Filter Status
        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $query .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $query .= " AND k.posisiId = 5";
            } elseif ($statusFilter === 'menunggu') {
                $query .= " AND k.posisiId = 3"; // Menunggu di meja Wadir
            } elseif ($statusFilter === 'in process') {
                $query .= " AND k.statusUtamaId != 4 AND k.posisiId != 5";
            }
        }

        // Filter Jurusan
        if ($jurusanFilter !== 'semua') {
            $jurusanFilter = mysqli_real_escape_string($this->db, $jurusanFilter);
            $query .= " AND k.jurusanPenyelenggara = '$jurusanFilter'";
        }

        // Hitung Total Data
        $countQuery = str_replace("SELECT \n                    k.kegiatanId as id,", "SELECT COUNT(*) as total", $query);
        // Simplifikasi count query untuk performa (opsional, tapi string replace di atas kadang rawan jika select kompleks)
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k WHERE 1=1 ";
        // Re-apply logic (Simpelnya ambil logic WHERE saja jika mau)
        // Untuk amannya, copy logic WHERE di atas:
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