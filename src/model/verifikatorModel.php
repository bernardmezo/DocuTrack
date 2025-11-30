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
                WHERE k.posisiId = 1  -- posisi verifikator
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
    
    // Update Status & Kode MAK (Approve)
    public function approveUsulan($kegiatanId, $kodeMak) {
        // Status 3 = Disetujui
        $nextPosisi = 2; // ID Role verifikator sesuai tbl_role
        $statusTetap = 1; // ID Status Menunggu

        $query = "UPDATE tbl_kegiatan 
                SET statusUtamaId = ?, posisiId = ?, buktiMAK = ? 
                WHERE kegiatanId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "iisi", $statusTetap, $nextPosisi, $kodeMak, $kegiatanId);
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
}   


?>