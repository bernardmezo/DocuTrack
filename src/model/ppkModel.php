<?php
// File: src/models/ppkModel.php

class ppkModel {
    private $db;

    public function __construct() {
        // Hubungkan ke database
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di ppkModel.");
        }
    }

    /**
     * ====================================================
     * 1. MENGAMBIL DATA STATISTIK (KARTU ATAS DASHBOARD)
     * ====================================================
     */
    public function getDashboardStats() {
        // Menghitung total berdasarkan statusUtamaId di tbl_kegiatan
        // Asumsi ID Status: 
        // 1 = Menunggu, 2 = Revisi
        // 3 = Disetujui
        // 4 = Ditolak
        
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
     * ====================================================
     * 2. MENGAMBIL LIST USULAN (UNTUK TABEL DASHBOARD)
     * ====================================================
     */
    public function getDashboardKAK() {
        // Mengambil data kegiatan join dengan status
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    
                    -- Ambil nama status dari tabel relasi tbl_status_utama
                    s.namaStatusUsulan as status

                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 4  -- TAMBAHKAN INI
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Formatting Status agar konsisten (Huruf Besar Awal)
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']); 
                } else {
                    $row['status'] = 'Menunggu'; // Default
                }
                
                // Pastikan Jurusan & Prodi tidak null untuk tampilan
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                
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
     * 4. UPDATE STATUS (APPROVE PPK)
     * ====================================================
     */
    public function approveUsulan($kegiatanId) {
        // Update status menjadi Disetujui (ID 3)
        // Jika ingin menandai bahwa ini disetujui PPK, bisa ditambahkan kolom khusus
        // Untuk sekarang, kita update statusUtamaId saja.
        
        $query = "UPDATE tbl_kegiatan SET posisiId = 3 WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * ====================================================
     * 5. AMBIL DATA RIWAYAT (HISTORY)
     * ====================================================
     * Mengambil data yang SUDAH LEWAT dari meja PPK
     */
    public function getRiwayat() {
        // Logika:
        // 1. Sudah Disetujui: Posisi ada di Wadir (3) atau Bendahara (5)
        // 2. Ditolak: Status Utama = 4 (Ditolak)
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    
                    -- Tentukan Status Tampilan untuk Riwayat
                    CASE 
                        -- Jika posisi sudah di Wadir/Bendahara => Berarti 'Disetujui' oleh PPK
                        WHEN k.posisiId IN (3, 5) AND k.statusUtamaId != 4 THEN 'Disetujui'
                        -- Jika statusnya 4 => 'Ditolak'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        -- Sisa (jarang terjadi di query ini)
                        ELSE 'Diproses'
                    END as status

                  FROM tbl_kegiatan k
                  WHERE 
                    -- Filter: Ambil yang posisinya SUDAH MAJU ke Wadir(3)/Bendahara(5)
                    -- ATAU yang statusnya Ditolak(4) tapi pernah lewat PPK (Logika sederhana: semua yg ditolak)
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
     * ====================================================
     * 6. MONITORING DATA (Untuk Halaman Monitoring)
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
                    
                    -- MAPPING PROGRESS BERDASARKAN POSISI ID
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                        WHEN k.posisiId = 1 THEN 'Pengajuan'    -- Di Admin
                        WHEN k.posisiId = 2 THEN 'Verifikasi'   -- Di Verifikator
                        WHEN k.posisiId = 4 THEN 'ACC PPK'      -- Di PPK
                        WHEN k.posisiId = 3 THEN 'ACC WD'    -- Di Wadir
                        WHEN k.posisiId = 5 THEN 'Dana Cair'    -- Di Bendahara
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
                $query .= " AND k.posisiId = 4"; // Menunggu di meja PPK
            } elseif ($statusFilter === 'in process') {
                $query .= " AND k.statusUtamaId != 4 AND k.posisiId != 5";
            }
        }

        // Filter Jurusan
        if ($jurusanFilter !== 'semua') {
            $jurusanFilter = mysqli_real_escape_string($this->db, $jurusanFilter);
            $query .= " AND k.jurusanPenyelenggara = '$jurusanFilter'";
        }

        // Hitung Total Data (Untuk Pagination)
        $countQuery = str_replace("SELECT \n                    k.kegiatanId as id,", "SELECT COUNT(*) as total", $query);
        // Hapus bagian column yang panjang di count query agar valid SQL (simple regex replace or substr if needed, but manual replacement above is safer logic structure, simplified here:)
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k WHERE 1=1 ";
        // Re-apply filters to count query (Copy paste logic above or restructure code, for brevity I'll rewrite logic inside logic)
        if (!empty($search)) { $countQuery .= " AND (k.namaKegiatan LIKE '%$search%' OR k.pemilikKegiatan LIKE '%$search%')"; }
        if ($statusFilter !== 'semua') {
             if ($statusFilter === 'ditolak') $countQuery .= " AND k.statusUtamaId = 4";
             elseif ($statusFilter === 'approved') $countQuery .= " AND k.posisiId = 5";
             elseif ($statusFilter === 'menunggu') $countQuery .= " AND k.posisiId = 4";
             elseif ($statusFilter === 'in process') $countQuery .= " AND k.statusUtamaId != 4 AND k.posisiId != 5";
        }
        if ($jurusanFilter !== 'semua') { $countQuery .= " AND k.jurusanPenyelenggara = '$jurusanFilter'"; }


        // Order & Limit
        $query .= " ORDER BY k.createdAt DESC LIMIT $perPage OFFSET $offset";

        // Execute Count
        $totalResult = mysqli_query($this->db, $countQuery);
        $totalItems = ($totalResult) ? mysqli_fetch_assoc($totalResult)['total'] : 0;

        // Execute Main Query
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

    // Helper untuk Filter Dropdown
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