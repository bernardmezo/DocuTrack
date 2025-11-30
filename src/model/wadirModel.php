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
                    COUNT(*) as total,
                    -- Disetujui Wadir: Jika posisi sudah di Bendahara (5)
                    SUM(CASE 
                        WHEN posisiId = 5 AND statusUtamaId != 4 THEN 1 
                        ELSE 0 
                    END) as disetujui,
                    
                    -- Ditolak
                    SUM(CASE 
                        WHEN statusUtamaId = 4 THEN 1 
                        ELSE 0 
                    END) as ditolak,

                    -- Menunggu: Sedang di meja Wadir (Posisi = 3)
                    SUM(CASE 
                        WHEN posisiId = 3 THEN 1 
                        ELSE 0 
                    END) as menunggu

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
        
        $nextPosisi = 5; // BENDAHARA
        $resetStatus = 1; // Menunggu
        
        $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "iii", $nextPosisi, $resetStatus, $kegiatanId);
        
        // Catat History (Opsional)
        // $this->catatHistory($kegiatanId, 3, 'Disetujui Wadir, lanjut Bendahara');
        
        return mysqli_stmt_execute($stmt);
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
}
?>