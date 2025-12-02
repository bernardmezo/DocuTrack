<?php

Class verifikatorModel {
    private $db;

    function __construct()
    {
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die('Error: Koneksi database gagal di verifikatorModel.');
        }
    }
    /**
     * ====================================================
     * 1. MENGAMBIL DATA STATISTIK (LOGIKA ESTAFET)
     * ====================================================
     */
    public function getDashboardStats() {
        // ID ROLE: 2=Verifikator, 4=PPK, 3=Wadir, 5=Bendahara
        
        $query = "SELECT 
                    -- 1. Total Usulan: Semua yang sedang di Verifikator ATAU sudah lewat Verifikator
                    -- Kita hitung semua kecuali yang masih di Admin (1) dan belum dikirim
                    COUNT(*) as total,

                    -- 2. Disetujui: Jika posisiId SUDAH MELEWATI Verifikator (ada di PPK, Wadir, atau Bendahara)
                    -- DAN statusnya tidak Ditolak
                    SUM(CASE 
                        WHEN posisiId IN (4, 3, 5) AND statusUtamaId != 4 THEN 1 
                        ELSE 0 
                    END) as disetujui,

                    -- 3. Ditolak: Status Ditolak (Global)
                    SUM(CASE 
                        WHEN statusUtamaId = 4 THEN 1 
                        ELSE 0 
                    END) as ditolak,

                    -- 4. Pending: Yang BERADA di meja Verifikator (Posisi = 2)
                    -- Baik statusnya 'Menunggu' atau 'Revisi'
                    SUM(CASE 
                        WHEN posisiId = 2 THEN 1 
                        ELSE 0 
                    END) as waiting  -- Saya ganti alias jadi 'waiting' agar beda dikit (atau tetap 'menunggu')

                FROM tbl_kegiatan 
                -- Opsional: Tambahkan filter jika ingin membatasi hanya jurusan tertentu
                -- WHERE ... 
                ";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            // Mapping ulang agar sesuai dengan view dashboard.php
            return [
                'total' => $data['total'],
                'disetujui' => $data['disetujui'],
                'ditolak' => $data['ditolak'],
                'pending' => $data['waiting'] // Mapping 'waiting' ke 'pending'
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'pending' => 0];
    }

    /**
     * ====================================================
     * 2. MENGAMBIL LIST KAK (UNTUK TABEL KAK)
     * ====================================================
     */
    public function getDashboardKAK() {
        // Mengambil data kegiatan join dengan status
        // Menggunakan CONCAT untuk menggabungkan nama pengusul sesuai format tabel dashboard
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.pemilikKegiatan as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    
                    -- Ambil nama status dari tabel relasi tbl_status_utama
                    s.namaStatusUsulan as status

                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 2  -- posisi verifikator (kegiatan yang sudah disubmit Admin)
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Opsional: Memperbaiki format status (Huruf Besar Awal)
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']); 
                } else {
                    $row['status'] = 'Menunggu'; // Default jika null
                }
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * ====================================================
     * 3. GET DETAIL KEGIATAN (UNTUK HALAMAN TELAAH)
     * ====================================================
     */

    // A. Ambil Data Utama Kegiatan
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

    // B. Ambil Indikator KAK
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

    // C. Ambil Tahapan Pelaksanaan
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

    // D. Ambil RAB (Grouped by Kategori)
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
     * ====================================================
     * 4. UPDATE STATUS (UNTUK TOMBOL AKSI)
     * ====================================================
     */
    
    // NOTE: Fungsi approveUsulan() dipindahkan ke bawah dengan logic yang benar (estafet ke PPK)

    // Update Status (Reject/Revisi) - Legacy, gunakan rejectUsulan() atau reviseUsulan()
    public function updateStatus($kegiatanId, $statusId) {
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $statusId, $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * ====================================================
     * 5. DAFTAR JURUSAN BUAT DIPAKE FILTER
     * ====================================================
     */

    public function getListJurusan() {
        $query = "SELECT * FROM tbl_jurusan j";
        $stmt = mysqli_prepare($this->db, $query);
        
        // Error handling: cek jika prepare gagal
        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->db));
            return [];
        }
        
        // Execute query
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }
        
        // Ambil result set dari statement (PENTING: gunakan mysqli_stmt_get_result)
        $result = mysqli_stmt_get_result($stmt);
        $jurusan = [];

        // Fetch semua baris
        while ($row = mysqli_fetch_assoc($result)) {
            $jurusan[] = $row;
        }
        
        // Bersihkan statement
        mysqli_stmt_close($stmt);
        return $jurusan;
    }

    /**
     * ====================================================
     * 6. AMBIL DATA RIWAYAT (HISTORY) - BARU
     * ====================================================
     * Mengambil data yang SUDAH DIPROSES oleh Verifikator
     * Logic: Ambil yang statusUtamaId-nya BUKAN 1 (Menunggu)
     */
    public function getRiwayat() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal_pengajuan,
                    
                    -- Status Tampilan
                    CASE 
                        WHEN k.posisiId IN (3, 4, 5) AND k.statusUtamaId != 4 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status

                  FROM tbl_kegiatan k
                  WHERE 
                    -- Ambil semua KECUALI yang masih 'Menunggu' (ID 1) di meja Verifikator (Posisi 2)
                    -- Artinya: Ambil yang sudah Disetujui (3), Revisi (2), atau Ditolak (4)
                    k.posisiId IN (3, 4, 5)
                  
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
     * Approve Usulan oleh Verifikator
     * Alur: Verifikator (2) -> PPK (4)
     * 
     * @param int $kegiatanId ID Kegiatan
     * @param string $kodeMak Kode MAK yang diinput Verifikator
     * @return bool
     */
    public function approveUsulan($kegiatanId, $kodeMak) {
        // =====================================================
        // LOGIKA ESTAFET YANG BENAR:
        // Verifikator approve -> Pindah ke PPK (posisiId = 4)
        // Status tetap Menunggu (1) karena PPK belum proses
        // =====================================================
        
        $currentPosisi = 2;  // Posisi sekarang: Verifikator
        $nextPosisi = 4;     // ✅ BENAR: Pindah ke PPK
        $statusMennggu = 1;  // Status: Menunggu (PPK belum proses)
        $userId = $_SESSION['user_id'] ?? null;
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update posisi kegiatan ke PPK
            $query = "UPDATE tbl_kegiatan 
                      SET statusUtamaId = ?, posisiId = ?, buktiMAK = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iisi", $statusMennggu, $nextPosisi, $kodeMak, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);
            
            // 2. Catat ke tbl_progress_history
            $this->insertProgressHistory($kegiatanId, $statusMennggu, $currentPosisi, $nextPosisi, $userId, 'approve');
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("approveUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject Usulan oleh Verifikator
     * Status berubah ke Ditolak (4), posisi tetap di Verifikator
     * 
     * @param int $kegiatanId ID Kegiatan
     * @param string $alasanPenolakan Komentar alasan ditolak
     * @return bool
     */
    public function rejectUsulan($kegiatanId, $alasanPenolakan = '') {
        $statusDitolak = 4;
        $currentPosisi = 2;
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = 2; // Role Verifikator
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update status jadi Ditolak
            $query = "UPDATE tbl_kegiatan 
                      SET statusUtamaId = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $statusDitolak, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update status");
            }
            mysqli_stmt_close($stmt);
            
            // 2. Catat history
            $historyId = $this->insertProgressHistory($kegiatanId, $statusDitolak, $currentPosisi, $currentPosisi, $userId, 'reject');
            
            // 3. Simpan komentar penolakan
            if (!empty($alasanPenolakan) && $historyId) {
                $this->insertRevisiComment($historyId, $userId, $roleId, $alasanPenolakan, null, null);
            }
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("rejectUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Revisi Usulan oleh Verifikator
     * Kembalikan ke Admin (posisiId = 1) dengan status Revisi (2)
     * 
     * @param int $kegiatanId ID Kegiatan
     * @param array $komentarRevisi Array komentar per field [['targetKolom' => 'nama', 'komentar' => '...'], ...]
     * @return bool
     */
    public function reviseUsulan($kegiatanId, $komentarRevisi = []) {
        $statusRevisi = 2;
        $currentPosisi = 2;  // Verifikator
        $backToPosisi = 1;   // Kembalikan ke Admin
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = 2;
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update status & posisi
            $query = "UPDATE tbl_kegiatan 
                      SET statusUtamaId = ?, posisiId = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $statusRevisi, $backToPosisi, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update status");
            }
            mysqli_stmt_close($stmt);
            
            // 2. Catat history
            $historyId = $this->insertProgressHistory($kegiatanId, $statusRevisi, $currentPosisi, $backToPosisi, $userId, 'revise');
            
            // 3. Simpan SEMUA komentar revisi
            if (!empty($komentarRevisi) && $historyId) {
                foreach ($komentarRevisi as $komentar) {
                    $this->insertRevisiComment(
                        $historyId, 
                        $userId, 
                        $roleId, 
                        $komentar['komentar'] ?? '',
                        $komentar['targetTabel'] ?? 'tbl_kegiatan',
                        $komentar['targetKolom'] ?? null
                    );
                }
            }
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("reviseUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    // =====================================================
    // HELPER METHODS (Tambahkan di bawah class)
    // =====================================================

    /**
     * Insert record ke tbl_progress_history
     * Schema: progressHistoryId, kegiatanId, statusId, timestamp
     */
    private function insertProgressHistory($kegiatanId, $statusId, $fromPosisi, $toPosisi, $userId, $actionType) {
        $query = "INSERT INTO tbl_progress_history 
                  (kegiatanId, statusId, timestamp) 
                  VALUES (?, ?, NOW())";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $kegiatanId, $statusId);
        
        if (mysqli_stmt_execute($stmt)) {
            $insertId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $insertId;
        }
        
        mysqli_stmt_close($stmt);
        return false;
    }

    /**
     * Insert komentar revisi ke tbl_revisi_comment
     * Schema: revisiCommentId, progressHistoryId, komentarRevisi, targetTabel, targetKolom
     */
    private function insertRevisiComment($historyId, $userId, $roleId, $komentar, $targetTabel = null, $targetKolom = null) {
        $query = "INSERT INTO tbl_revisi_comment 
                  (progressHistoryId, komentarRevisi, targetTabel, targetKolom) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "isss", $historyId, $komentar, $targetTabel, $targetKolom);
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    /**
     * ====================================================
     * MONITORING: MENGAMBIL DATA PROPOSAL UNTUK MONITORING
     * ====================================================
     */
    public function getProposalMonitoring() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.jurusanPenyelenggara as jurusan,
                    s.namaStatusUsulan as status,
                    p.namaPosisi as tahap_sekarang
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                LEFT JOIN tbl_posisi p ON k.posisiId = p.posisiId
                ORDER BY k.createdAt DESC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Format status dengan huruf kapital awal
                if (isset($row['status'])) {
                    $row['status'] = ucfirst(strtolower($row['status']));
                } else {
                    $row['status'] = 'Menunggu';
                }
                // Format tahap_sekarang
                if (!isset($row['tahap_sekarang']) || empty($row['tahap_sekarang'])) {
                    $row['tahap_sekarang'] = 'Admin';
                }
                $data[] = $row;
            }
        }
        return $data;
    }
}   


?>