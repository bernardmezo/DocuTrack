<?php
/**
 * wadirModel - Wadir Management Model
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class wadirModel {
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli|null $db Database connection (optional for backward compatibility)
     */
    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Backward compatibility
            require_once __DIR__ . '/conn.php';
            if (isset($conn)) {
                $this->db = $conn;
            } else {
                die("Error: Koneksi database gagal di wadirModel.");
            }
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN posisiId IN (1, 5) AND statusUtamaId != 4 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN posisiId = 3 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
            return [
                'total' => $row['total'] ?? 0,
                'disetujui' => $row['disetujui'] ?? 0,
                'menunggu' => $row['menunggu'] ?? 0
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'menunggu' => 0];
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
        $query = "SELECT 
                    k.*, 
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    k.pemilikKegiatan as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    s.namaStatusUsulan as status_text
                FROM tbl_kegiatan k
                JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_user u ON u.userId = k.userId
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
     * Menyetujui usulan dan meneruskan ke Bendahara.
     */
    public function approveUsulan($kegiatanId, $rekomendasi = '') {
        $nextPosisi = 5;  // BENDAHARA
        $currentPosisi = 3; // Wadir
        $statusProses = 1; // Masih dalam proses
        $userId = $_SESSION['user_id'] ?? null;
        
        mysqli_begin_transaction($this->db);
        
        try {
            // Update status kegiatan
            $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $nextPosisi, $statusProses, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);
            
            // Insert History
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId, timestamp) VALUES (?, ?, ?, NOW())";
            $stmtHistory = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHistory, "iii", $kegiatanId, $statusProses, $userId);
            
            if (!mysqli_stmt_execute($stmtHistory)) {
                 throw new Exception("Gagal catat history");
            }

            $historyId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmtHistory);

            // Jika ada rekomendasi, simpan ke tabel komentar
            if (!empty($rekomendasi)) {
                $commentQuery = "INSERT INTO tbl_revisi_comment (progressHistoryId, komentarRevisi, targetTabel) VALUES (?, ?, 'wadir_rekomendasi')";
                $stmtComment = mysqli_prepare($this->db, $commentQuery);
                mysqli_stmt_bind_param($stmtComment, "is", $historyId, $rekomendasi);
                mysqli_stmt_execute($stmtComment);
                mysqli_stmt_close($stmtComment);
            }
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("Wadir approveUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil riwayat verifikasi Wadir dengan tanggal approval.
     *
     * Method ini mengambil semua kegiatan yang sudah diproses oleh Wadir:
     * - Disetujui: posisiId >= 5 (sudah lanjut ke tahap berikutnya)
     * - Ditolak: statusUtamaId = 4
     *
     * Tanggal approval diambil dari:
     * 1. tbl_progress_history (jika ada record approval Wadir)
     * 2. k.updatedAt (fallback jika history tidak tersedia)
     *
     * @return array Array berisi daftar kegiatan yang sudah diproses Wadir
     */
    public function getRiwayat()
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal_pengajuan,
                    k.updatedAt as tanggal_update,
                    
                    -- Ambil tanggal approval dari history (jika ada)
                    COALESCE(
                        (SELECT ph.timestamp 
                         FROM tbl_progress_history ph 
                         WHERE ph.kegiatanId = k.kegiatanId 
                         AND ph.statusId IN (3, 5) 
                         ORDER BY ph.timestamp DESC 
                         LIMIT 1),
                        k.updatedAt
                    ) as tanggal_disetujui,
                    
                    CASE 
                        WHEN k.posisiId != 5 AND k.statusUtamaId != 4 AND k.statusUtamaId = 5 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status
                    
                  FROM tbl_kegiatan k
                  WHERE k.posisiId = 1 AND k.statusUtamaId = 5
                  ORDER BY tanggal_disetujui DESC";

        $result = mysqli_query($this->db, $query);
                
        // Debug log
        if ($result === false) {
            error_log("wadirModel::getRiwayat() ERROR: " . mysqli_error($this->db));
            return [];
        }
        
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Pastikan field tidak null
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $data[] = $row;
            }
        }
        
        error_log("wadirModel::getRiwayat() - Total rows: " . count($data));
        
        return $data;
    }

    /**
     * Mengambil data monitoring untuk Wadir dengan filtering dan pagination.
     *
     * Method ini mengambil data kegiatan untuk monitoring dengan berbagai filter:
     * - 'menunggu': Hanya usulan yang posisiId = 3 DAN statusUtamaId = 1 (menunggu approval Wadir)
     * - 'approved': Usulan yang sudah disetujui (posisiId >= 4)
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
                        WHEN k.posisiId >= 5 AND k.statusUtamaId != 4 THEN 'Approved'
                        WHEN k.posisiId = 3 AND k.statusUtamaId = 1 THEN 'Menunggu'
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
                // FIXED: Approved = sudah melewati Wadir (posisiId >= 5)
                $query .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                // Menunggu = posisi di Wadir (3) DAN statusUtama = 1 (menunggu approval)
                $query .= " AND k.posisiId = 3 AND k.statusUtamaId = 1";
            } elseif ($statusFilter === 'in process') {
                // In Process = belum sampai approved, belum ditolak, bukan menunggu di Wadir
                $query .= " AND k.statusUtamaId != 4 AND k.posisiId < 5 AND NOT (k.posisiId = 3 AND k.statusUtamaId = 1)";
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
                // FIXED: Konsisten dengan main query
                $countQuery .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                $countQuery .= " AND k.posisiId = 3 AND k.statusUtamaId = 1";
            } elseif ($statusFilter === 'in process') {
                $countQuery .= " AND k.statusUtamaId != 4 AND k.posisiId < 5 AND NOT (k.posisiId = 3 AND k.statusUtamaId = 1)";
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
?>