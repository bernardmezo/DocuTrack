<?php
/**
 * bendaharaModel - Bendahara Management Model
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class bendaharaModel {
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
                die("Error: Koneksi database gagal di bendaharaModel.");
            }
        }
    }

    // =========================================================
    // 1. STATISTIK DASHBOARD
    // =========================================================
    
    /**
     * Hitung statistik untuk dashboard Bendahara
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    -- Total: Semua yang sudah sampai ke Bendahara (posisi 5) atau sudah dicairkan
                    SUM(CASE WHEN posisiId = 5 OR tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as total,
                    
                    -- Menunggu Pencairan: Posisi di Bendahara tapi belum dicairkan
                    SUM(CASE WHEN posisiId = 5 AND tanggalPencairan IS NULL THEN 1 ELSE 0 END) as menunggu,
                    
                    -- Sudah Dicairkan: Ada tanggal pencairan
                    SUM(CASE WHEN statusUtamaId = 5 AND tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as dicairkan
                    
                  FROM tbl_kegiatan";
        
        $result = mysqli_query($this->db, $query);
        
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        
        return ['total' => 0, 'menunggu' => 0, 'dicairkan' => 0];
    }

    // =========================================================
    // 2. LIST PENCAIRAN DANA (Antrian)
    // =========================================================
    
    /**
     * Ambil semua kegiatan yang menunggu pencairan (posisiId = 5, belum dicairkan)
     */
    public function getAntrianPencairan() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal_pengajuan,
                    k.buktiMAK as kode_mak,
                    
                    -- Hitung total RAB sebagai anggaran
                    (SELECT COALESCE(SUM(r.totalHarga), 0) 
                     FROM tbl_rab r 
                     JOIN tbl_kak kak ON r.kakId = kak.kakId 
                     WHERE kak.kegiatanId = k.kegiatanId) as anggaran_disetujui,
                    
                    'Menunggu' as status
                    
                  FROM tbl_kegiatan k
                  WHERE k.posisiId = 5 
                    AND k.tanggalPencairan IS NULL
                    AND k.statusUtamaId != 4
                  ORDER BY k.createdAt ASC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $data[] = $row;
            }
        }
        
        return $data;
    }

    /**
     * Ambil riwayat pencairan (sudah dicairkan)
     */
    public function getRiwayatPencairan() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.tanggalPencairan,
                    k.jumlahDicairkan,
                    k.metodePencairan,
                    'Dana Diberikan' as status
                    
                  FROM tbl_kegiatan k
                  WHERE k.tanggalPencairan IS NOT NULL
                  ORDER BY k.tanggalPencairan DESC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 3. DETAIL KEGIATAN (untuk halaman detail pencairan)
    // =========================================================
    
    /**
     * Ambil detail lengkap kegiatan untuk proses pencairan
     */
    public function getDetailPencairan($kegiatanId) {
        $query = "SELECT 
                    k.*,
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    u.nama as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    s.namaStatusUsulan as status_text,
                    
                    -- Hitung total RAB
                    (SELECT COALESCE(SUM(r.totalHarga), 0) 
                     FROM tbl_rab r WHERE r.kakId = kak.kakId) as total_rab
                     
                  FROM tbl_kegiatan k
                  JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  LEFT JOIN tbl_user u ON u.userId = k.userId
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Ambil data RAB (untuk ditampilkan di detail)
     */
    public function getRABByKegiatan($kegiatanId) {
        $query = "SELECT r.*, cat.namaKategori 
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  JOIN tbl_kak kak ON r.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Mapping field agar sesuai dengan format yang diharapkan View
            $kategori = $row['namaKategori'];
            $data[$kategori][] = [
                'uraian' => $row['uraian'] ?? '',
                'rincian' => $row['rincian'] ?? '',
                'vol1' => $row['vol1'] ?? 0,
                'sat1' => $row['sat1'] ?? '',
                'vol2' => $row['vol2'] ?? 1,
                'sat2' => $row['sat2'] ?? '',
                'harga' => $row['harga'] ?? 0
            ];
        }
        return $data;
    }

    /**
     * Ambil data IKU berdasarkan kegiatanId
     */
    public function getIKUByKegiatan($kegiatanId) {
        // Ambil IKU dari tbl_kak (asumsi disimpan sebagai CSV atau join tabel)
        $query = "SELECT iku FROM tbl_kak WHERE kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row && !empty($row['iku'])) {
            return explode(',', $row['iku']);
        }
        return [];
    }

    /**
     * Ambil data Indikator KAK berdasarkan kegiatanId
     */
    public function getIndikatorByKegiatan($kegiatanId) {
        $query = "SELECT i.bulan, i.indikatorKeberhasilan as nama, i.targetPersen as target 
                  FROM tbl_indikator_kak i
                  JOIN tbl_kak kak ON i.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY i.indikatorId ASC";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Ambil tahapan kegiatan
     */
    public function getTahapanByKegiatan($kegiatanId) {
        $query = "SELECT t.namaTahapan 
                  FROM tbl_tahapan_pelaksanaan t
                  JOIN tbl_kak kak ON t.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY t.tahapanId ASC";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        return $data;
    }

    /**
     * Helper: Hitung Tenggat LPJ (14 Hari Kerja setelah pencairan).
     * Melewati hari Sabtu dan Minggu.
     */
    private function calculateLpjDeadline($startDate) {
        $date = new DateTime($startDate);
        $workingDaysToAdd = 14;

        while ($workingDaysToAdd > 0) {
            $date->modify('+1 day');
            // 6 = Saturday, 7 = Sunday
            $dayOfWeek = (int)$date->format('N');
            if ($dayOfWeek < 6) {
                $workingDaysToAdd--;
            }
        }
        
        return $date->format('Y-m-d');
    }

    /**
     * Proses Pencairan Dana (Unified).
     * Menangani pencairan penuh maupun bertahap.
     * 
     * @param int $kegiatanId
     * @param array $dataPencairan [
     *   'jumlah' => float,
     *   'tanggal' => string (Y-m-d),
     *   'metode' => string ('full' | 'bertahap'),
     *   'tahapan' => array (optional, required if metode='bertahap')
     *   'catatan' => string (optional)
     * ]
     */
    public function cairkanDana($kegiatanId, $dataPencairan) {
        $jumlah = $dataPencairan['jumlah'] ?? 0;
        $tanggalCair = $dataPencairan['tanggal'] ?? date('Y-m-d');
        $metode = $dataPencairan['metode'] ?? 'full';
        $tahapan = $dataPencairan['tahapan'] ?? [];
        $catatan = $dataPencairan['catatan'] ?? '';
        
        // Logic untuk menentukan Base Date perhitungan LPJ
        // Jika bertahap, biasanya dihitung dari tahap terakhir. 
        // Namun jika aturan bisnis = 14 hari dari uang diterima (tahap 1), gunakan $tanggalCair.
        // Asumsi disini: Tenggat dihitung dari pencairan TERAKHIR yang direncanakan.
        
        $baseDateForLpj = $tanggalCair;
        $jsonTahapan = null;

        // TO DO : Handle Buat metode bertahap
        // Tambahin tbl_tahapan_pencairan untuk simpan tahapannya. (metodenya sama kaya tbl_tahapan_pelaksanaan di KAK)

        if ($metode === 'bertahap' && !empty($tahapan)) {
            // Validasi total persentase
            $totalPersen = array_sum(array_column($tahapan, 'persentase'));
            if ($totalPersen != 100) {
                throw new Exception("Total persentase tahapan harus 100%.");
            }

            // Encode JSON
            $jsonTahapan = json_encode($tahapan);
            
            // Ambil tanggal tahap terakhir untuk deadline LPJ
            $lastStage = end($tahapan);
            $baseDateForLpj = $lastStage['tanggal'];
        }

        $tenggatLpj = $this->calculateLpjDeadline($baseDateForLpj);
        
        mysqli_begin_transaction($this->db);
        try {
            // 1. Update Kegiatan
            // REVISI: Kembalikan posisi ke Admin (1) dengan status (1) agar Admin bisa submit LPJ
            $query = "UPDATE tbl_kegiatan 
                      SET tanggalPencairan = ?, 
                          jumlahDicairkan = ?, 
                          metodePencairan = ?, 
                          catatanBendahara = ?,
                          statusUtamaId = 5,
                          posisiId = 1 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "sdssi", 
                $tanggalCair, 
                $jumlah, 
                $metode, 
                $catatan, 
                $kegiatanId
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update data pencairan.");
            }
            mysqli_stmt_close($stmt);

            // 2. Update/Create LPJ Deadline
            if (!$this->createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)) {
                 throw new Exception("Gagal set tenggat LPJ.");
            }

            // 3. Log History
            $statusDisetujui = 3; 
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, timestamp) VALUES (?, ?, NOW())";
            $stmtHist = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHist, "ii", $kegiatanId, $statusDisetujui);
            mysqli_stmt_execute($stmtHist);
            mysqli_stmt_close($stmtHist);

            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("cairkanDana Error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // 4. PROSES PENCAIRAN DANA (Legacy Wrapper)
    // =========================================================
    
    /**
     * Proses pencairan dana
     * 
     * @param int $kegiatanId
     * @param float $jumlahDicairkan
     * @param string $metodePencairan (uang_muka, dana_penuh, bertahap)
     * @param string $catatan
     * @param string $tenggatLpj Format: Y-m-d
     * @return bool
     */
    public function prosesPencairan($kegiatanId, $jumlahDicairkan, $metodePencairan, $catatan = '', $tenggatLpj = null) {
        return $this->cairkanDana($kegiatanId, [
            'jumlah' => $jumlahDicairkan,
            'metode' => $metodePencairan,
            'catatan' => $catatan,
            'tanggal' => date('Y-m-d')
        ]);
    }
    
    /**
     * Membuat atau update row LPJ saat dana dicairkan
     * @param int $kegiatanId
     * @param string $tenggatLpj Format: Y-m-d
     * @return bool
     */
    private function createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj) {
        // Cek apakah row LPJ sudah ada
        $checkQuery = "SELECT lpjId FROM tbl_lpj WHERE kegiatanId = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $checkQuery);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($existing) {
            // Row sudah ada, update saja tenggatLpj
            $updateQuery = "UPDATE tbl_lpj SET tenggatLpj = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $tenggatLpj, $kegiatanId);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $success;
        }
        
        // Row belum ada, INSERT baru
        $insertQuery = "INSERT INTO tbl_lpj (kegiatanId, tenggatLpj) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $insertQuery);
        mysqli_stmt_bind_param($stmt, "is", $kegiatanId, $tenggatLpj);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    // =========================================================
    // 5. LPJ METHODS
    // =========================================================
    
    /**
     * Ambil daftar LPJ yang perlu divalidasi Bendahara
     */
    public function getAntrianLPJ() {
        $query = "SELECT 
                    l.lpjId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    l.grandTotalRealisasi as total_realisasi,
                    l.submittedAt as tanggal_pengajuan,
                    l.tenggatLpj as tenggat_lpj,
                    
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'Disetujui'
                        WHEN l.submittedAt IS NOT NULL THEN 'Menunggu'
                        ELSE 'Draft'
                    END as status
                    
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  WHERE l.submittedAt IS NOT NULL 
                    AND l.approvedAt IS NULL
                  ORDER BY l.submittedAt ASC";
        
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
     * Ambil detail LPJ dengan informasi lengkap
     */
    public function getDetailLPJ($lpjId) {
        error_log("=== bendaharaModel::getDetailLPJ START ===");
        error_log("lpjId: " . $lpjId);
        
        $query = "SELECT 
                    l.lpjId,
                    l.kegiatanId,
                    l.grandTotal,
                    l.submittedAt,
                    l.approvedAt,
                    l.tenggatLpj,
                    k.namaKegiatan,
                    k.pemilikKegiatan,
                    k.nimPelaksana,
                    k.prodiPenyelenggara,
                    k.jurusanPenyelenggara,
                    k.jumlahDicairkan,
                    k.tanggalPencairan,
                    k.createdAt as tanggal_pengajuan
                FROM tbl_lpj l
                JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                WHERE l.lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log("ERROR prepare: " . mysqli_error($this->db));
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$data) {
            error_log("ERROR: No data found for lpjId: " . $lpjId);
            return null;
        }
        
        // ✅ Hitung grand total realisasi dari items
        $totalQuery = "SELECT COALESCE(SUM(subTotal), 0) as grandTotalRealisasi 
                    FROM tbl_lpj_item 
                    WHERE lpjId = ?";
        
        $stmtTotal = mysqli_prepare($this->db, $totalQuery);
        mysqli_stmt_bind_param($stmtTotal, "i", $lpjId);
        mysqli_stmt_execute($stmtTotal);
        $resultTotal = mysqli_stmt_get_result($stmtTotal);
        $totalData = mysqli_fetch_assoc($resultTotal);
        mysqli_stmt_close($stmtTotal);
        
        $data['grandTotalRealisasi'] = $totalData['grandTotalRealisasi'] ?? 0;
        
        error_log("Detail LPJ fetched - grandTotalRealisasi: " . $data['grandTotalRealisasi']);
        error_log("=== bendaharaModel::getDetailLPJ END ===");
        
        return $data;
    }

        /**
         * Ambil item-item LPJ
         */
        public function getLPJItems($lpjId) {
        error_log("=== bendaharaModel::getLPJItems START ===");
        error_log("lpjId: " . $lpjId);
        
        $query = "SELECT 
                    lpjItemId,
                    jenisBelanja,
                    uraian,
                    rincian,
                    vol1,
                    sat1,
                    vol2,
                    sat2,
                    totalHarga,
                    subTotal,
                    fileBukti,
                    komentar
                FROM tbl_lpj_item 
                WHERE lpjId = ? 
                ORDER BY lpjItemId ASC";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log("ERROR prepare: " . mysqli_error($this->db));
            return [];
        }
        
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        $itemCount = 0;
        
        while ($row = mysqli_fetch_assoc($result)) {
            $itemCount++;
            $data[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        
        error_log("Total items fetched: " . $itemCount);
        error_log("=== bendaharaModel::getLPJItems END ===");
        
        return $data;
    }

    /**
     * Approve LPJ
     */
    public function approveLPJ($lpjId) {
        $query = "UPDATE tbl_lpj SET approvedAt = NOW() WHERE lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        
        return mysqli_stmt_execute($stmt);
    }

    // =========================================================
    // 6. HELPER - LIST JURUSAN UNTUK FILTER
    // =========================================================
    
    public function getListJurusan() {
        $query = "SELECT DISTINCT jurusanPenyelenggara as jurusan 
                  FROM tbl_kegiatan 
                  WHERE jurusanPenyelenggara IS NOT NULL 
                  ORDER BY jurusanPenyelenggara ASC";
        
        $result = mysqli_query($this->db, $query);
        $list = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['jurusan'];
            }
        }
        
        return $list;
    }

    // =========================================================
    // 7. DASHBOARD - Statistik dan List Kegiatan
    // =========================================================

    /**
     * Hitung statistik untuk Dashboard Bendahara
     * - Total: Semua kegiatan yang sudah melalui approval Wadir (posisiId >= 5)
     * - Dana Diberikan: Sudah dicairkan (tanggalPencairan IS NOT NULL)
     * - Ditolak: statusUtamaId = 4
     * - Menunggu: posisiId = 5 dan belum dicairkan
     */
    public function getDashboardStatistik() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as danaDiberikan,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN posisiId = 5 AND tanggalPencairan IS NULL AND statusUtamaId != 4 THEN 1 ELSE 0 END) as menunggu
                  FROM tbl_kegiatan
                  WHERE posisiId >= 5 OR tanggalPencairan IS NOT NULL";
        
        $result = mysqli_query($this->db, $query);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return [
                'total' => (int)($row['total'] ?? 0),
                'danaDiberikan' => (int)($row['danaDiberikan'] ?? 0),
                'ditolak' => (int)($row['ditolak'] ?? 0),
                'menunggu' => (int)($row['menunggu'] ?? 0)
            ];
        }
        
        return ['total' => 0, 'danaDiberikan' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * Ambil list kegiatan untuk Dashboard Bendahara
     * Menampilkan kegiatan yang sudah sampai ke Bendahara atau sudah diproses
     */
    public function getListKegiatanDashboard($limit = 10) {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    k.pemilikKegiatan as pengusul,
                    
                    -- Status berdasarkan kondisi
                    CASE 
                        WHEN k.tanggalPencairan IS NOT NULL THEN 'Dana Diberikan'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 'Menunggu'
                        ELSE 'Proses'
                    END as status
                    
                  FROM tbl_kegiatan k
                  WHERE k.posisiId >= 5 OR k.tanggalPencairan IS NOT NULL
                  ORDER BY 
                    CASE 
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 0
                        ELSE 1
                    END,
                    k.createdAt DESC
                  LIMIT ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 8. RIWAYAT VERIFIKASI - Kegiatan yang sudah diproses
    // =========================================================

    /**
     * Ambil riwayat verifikasi/proses Bendahara
     * Menampilkan kegiatan yang sudah dicairkan atau ditolak
     */
    public function getRiwayatVerifikasi() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    k.tanggalPencairan as tgl_verifikasi,
                    
                    -- Status berdasarkan kondisi
                    CASE 
                        WHEN k.tanggalPencairan IS NOT NULL THEN 'Dana Diberikan'
                        WHEN k.statusUtamaId = 4 THEN 'Revisi'
                        ELSE 'Proses'
                    END as status
                    
                  FROM tbl_kegiatan k
                  WHERE k.tanggalPencairan IS NOT NULL 
                     OR (k.statusUtamaId = 4 AND k.posisiId >= 5)
                  ORDER BY COALESCE(k.tanggalPencairan, k.createdAt) DESC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                $row['tgl_verifikasi'] = $row['tgl_verifikasi'] ?? date('Y-m-d');
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 9. AKUN - Update profil user
    // =========================================================

    /**
     * Ambil data user berdasarkan userId
     */
    public function getUserById($userId) {
        $query = "SELECT u.*, r.namaRole 
                  FROM tbl_user u
                  LEFT JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE u.userId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Update profil user (nama, email, password)
     * 
     * @param int $userId
     * @param array $data ['nama', 'email', 'password' (optional), 'profile_image' (optional), 'header_bg' (optional)]
     * @return bool
     */
    public function updateUserProfile($userId, $data) {
        $fields = [];
        $values = [];
        $types = "";
        
        // Build dynamic query berdasarkan field yang ada
        if (!empty($data['nama'])) {
            $fields[] = "nama = ?";
            $values[] = $data['nama'];
            $types .= "s";
        }
        
        if (!empty($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
            $types .= "s";
        }
        
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = $data['password']; // TODO: hash password in production
            $types .= "s";
        }
        
        if (empty($fields)) {
            return false; // Tidak ada yang diupdate
        }
        
        // Tambahkan userId untuk WHERE clause
        $values[] = $userId;
        $types .= "i";
        
        $query = "UPDATE tbl_user SET " . implode(", ", $fields) . " WHERE userId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        return mysqli_stmt_execute($stmt);
    }

    // =========================================================
    // 7. PENCAIRAN BERTAHAP (NEW FEATURE)
    // =========================================================
    
    /**
     * Proses pencairan dana secara bertahap.
     *
     * Method ini memproses pencairan dana dengan metode bertahap dimana:
     * - Dana dicairkan dalam beberapa tahap sesuai persentase yang ditentukan
     * - Setiap tahap memiliki tanggal pencairan dan persentase sendiri
     * - Data tahapan disimpan sebagai JSON di kolom pencairan_tahap_json
     * - Status kegiatan diupdate menjadi 'dana_dicairkan_bertahap'
     * - Batas waktu LPJ dihitung dari tanggal pencairan tahap TERAKHIR + 14 hari
     *
     * @param int $kegiatanId ID kegiatan yang akan dicairkan
     * @param float $totalAnggaran Total anggaran yang disetujui
     * @param array $tahapData Array of arrays, setiap item berisi:
     *                         ['tanggal' => 'Y-m-d', 'persentase' => float]
     * @return bool True jika berhasil, false jika gagal
     * @throws Exception Jika validasi gagal atau database error
     *
     * @example
     * ```php
     * $tahapData = [
     *     ['tanggal' => '2025-01-15', 'persentase' => 50],
     *     ['tanggal' => '2025-02-15', 'persentase' => 50]
     * ];
     * $model->prosesPencairanBertahap(123, 10000000, $tahapData);
     * ```
     */
    public function prosesPencairanBertahap($kegiatanId, $totalAnggaran, $tahapData)
    {
        // Validasi: Total persentase harus 100%
        $totalPersentase = array_sum(array_column($tahapData, 'persentase'));
        if ($totalPersentase != 100) {
            throw new Exception("Total persentase harus 100%, saat ini: {$totalPersentase}%");
        }
        
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Build JSON array untuk tahapan pencairan
            $tahapanJson = [];
            
            foreach ($tahapData as $index => $tahap) {
                $tahapKe = $index + 1;
                $tanggal = $tahap['tanggal'];
                $persentase = $tahap['persentase'];
                $jumlah = ($persentase / 100) * $totalAnggaran;
                
                // Build JSON object untuk setiap tahap
                $tahapanJson[] = [
                    'tahap' => $tahapKe,
                    'tanggal' => $tanggal,
                    'persentase' => $persentase,
                    'jumlah' => $jumlah,
                    'status' => 'scheduled' // scheduled, disbursed, cancelled
                ];
            }
            
            // Convert array to JSON string
            $jsonString = json_encode($tahapanJson, JSON_UNESCAPED_UNICODE);
            
            if ($jsonString === false) {
                throw new Exception('Gagal encode data tahapan ke JSON');
            }
            
            // 2. Update status kegiatan dengan data tahapan JSON
            $updateQuery = "UPDATE tbl_kegiatan 
                           SET tanggalPencairan = ?,
                               jumlahDicairkan = ?,
                               metodePencairan = 'bertahap',
                               pencairan_tahap_json = ?,
                               statusUtamaId = 3
                           WHERE kegiatanId = ?";
            
            // Tanggal pencairan = tanggal tahap pertama
            $tanggalPertama = $tahapData[0]['tanggal'];
            $stmtUpdate = mysqli_prepare($this->db, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "sdsi", $tanggalPertama, $totalAnggaran, $jsonString, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmtUpdate)) {
                throw new Exception("Gagal update status kegiatan");
            }
            mysqli_stmt_close($stmtUpdate);
            
            // 3. Hitung batas waktu LPJ: tanggal pencairan TERAKHIR + 14 hari
            $tanggalTerakhir = end($tahapData)['tanggal'];
            $batasLpj = date('Y-m-d', strtotime($tanggalTerakhir . ' +14 days'));
            
            $this->createOrUpdateLpjPlaceholder($kegiatanId, $batasLpj);
            
            // 4. Catat history
            $statusDisetujui = 3;
            $historyQuery = "INSERT INTO tbl_progress_history 
                            (kegiatanId, statusId, timestamp) 
                            VALUES (?, ?, NOW())";
            
            $stmtHistory = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHistory, "ii", $kegiatanId, $statusDisetujui);
            mysqli_stmt_execute($stmtHistory);
            mysqli_stmt_close($stmtHistory);
            
            mysqli_commit($this->db);
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("prosesPencairanBertahap Error: " . $e->getMessage());
            throw $e; // Re-throw untuk ditangani di controller
        }
    }
    
    /**
     * Ambil data tahapan pencairan untuk kegiatan tertentu.
     *
     * Method ini akan parse JSON dari kolom pencairan_tahap_json dan
     * mengembalikan array tahapan pencairan.
     *
     * @param int $kegiatanId ID kegiatan
     * @return array Array berisi data tahapan pencairan, atau empty array jika tidak ada
     */
    public function getTahapanPencairan($kegiatanId)
    {
        $query = "SELECT pencairan_tahap_json 
                 FROM tbl_kegiatan 
                 WHERE kegiatanId = ? 
                 LIMIT 1";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Return empty array jika tidak ada data atau JSON null
        if (!$row || empty($row['pencairan_tahap_json'])) {
            return [];
        }
        
        // Parse JSON string to array
        $tahapanData = json_decode($row['pencairan_tahap_json'], true);
        
        // Validasi hasil decode
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('getTahapanPencairan: JSON decode error - ' . json_last_error_msg());
            return [];
        }
        
        return is_array($tahapanData) ? $tahapanData : [];
    }
}