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
     * 1. MENGAMBIL DATA STATISTIK (KARTU ATAS DASHBOARD)
     * ====================================================
     */
    public function getDashboardStats() {
        // Menghitung total berdasarkan statusUtamaId di tbl_kegiatan
        // Asumsi ID Status: 
        // 1 = Menunggu, 2 = Revisi (Pending Group)
        // 3 = Disetujui
        // 4 = Ditolak
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 3 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId = 1 OR statusUtamaId = 2 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
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
            // Mapping volume agar view tidak error
            $row['vol1'] = $row['volume']; 
            $row['sat1'] = $row['satuan'];
            // Masukkan ke array group berdasarkan Nama Kategori
            $data[$row['namaKategori']][] = $row;
        }
        return $data;
    }

    /**
     * ====================================================
     * 4. UPDATE STATUS (UNTUK TOMBOL AKSI)
     * ====================================================
     */
    
    // Update Status & Kode MAK (Approve)
    public function approveUsulan($kegiatanId, $kodeMak) {
        // Status 3 = Disetujui
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = 3, buktiMAK = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "si", $kodeMak, $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }

    // Update Status (Reject/Revisi)
    // Status 2 = Revisi, 4 = Ditolak
    public function updateStatus($kegiatanId, $statusId) {
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $statusId, $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }
}


?>