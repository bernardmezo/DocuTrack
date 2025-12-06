<?php
/**
 * adminModel - Admin Management Model
 * 
 * Model untuk mengelola operasi admin dengan DI pattern.
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class adminModel {
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
            // New DI pattern: accept database from parameter
            $this->db = $db;
        } else {
            // Backward compatibility: load conn.php if no DI provided
            require_once __DIR__ . '/conn.php';
            if (isset($conn)) {
                $this->db = $conn;
            } else {
                die("Error: Koneksi database gagal di adminModel.");
            }
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 5 AND tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId != 4 AND statusUtamaId != 3 AND statusUtamaId != 5 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * Mengambil daftar KAK (Kerangka Acuan Kegiatan) untuk tabel dashboard.
     */
    /**
 * Mengambil daftar KAK (Kerangka Acuan Kegiatan) untuk tabel dashboard.
 * PENTING: HARUS mengembalikan posisiId dan statusUtamaId untuk filter!
 */
    public function getDashboardKAK() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, '), ', k.prodiPenyelenggara) as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,           -- ✅ PENTING: Tambahkan ini
                    k.posisiId,                     -- ✅ PENTING: Tambahkan ini juga
                    k.statusUtamaId,                -- ✅ PENTING: Tambahkan ini
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 3 THEN 'Disetujui'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 1 THEN 'Draft'
                        WHEN k.posisiId = 2 THEN 'Di Verifikator'
                        WHEN k.posisiId = 4 THEN 'Di PPK'
                        WHEN k.posisiId = 3 THEN 'Di Wadir'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 'Di Bendahara'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NOT NULL THEN 'Dana Cair'
                        ELSE s.namaStatusUsulan
                    END as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                LEFT JOIN tbl_role r ON k.posisiId = r.roleId
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
                
                // Debug log untuk cek data
                error_log("adminModel::getDashboardKAK() - Row Data:");
                error_log("  ID: {$row['id']}, posisiId: {$row['posisiId']}, statusUtamaId: {$row['statusUtamaId']}");
                
                $data[] = $row;
            }
        }
        
        error_log("adminModel::getDashboardKAK() - Total rows: " . count($data));
        return $data;
    }

    /**
     * Mengambil daftar KAK berdasarkan jurusan.
     */
    public function getDashboardKAKByJurusan($namaJurusan) {
        if (empty($namaJurusan)) {
            return [];
        }

        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, '), ', k.prodiPenyelenggara) as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    k.statusUtamaId,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 3 THEN 'Usulan Disetujui'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 1 THEN 'Draft'
                        WHEN k.posisiId = 2 THEN 'Di Verifikator'
                        WHEN k.posisiId = 4 THEN 'Di PPK'
                        WHEN k.posisiId = 3 THEN 'Di Wadir'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 'Di Bendahara'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NOT NULL THEN 'Dana Cair'
                        ELSE s.namaStatusUsulan
                    END as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                JOIN tbl_role r ON k.posisiId = r.roleId
                WHERE k.jurusanPenyelenggara = ?
                ORDER BY k.createdAt DESC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $namaJurusan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']); 
                } else {
                    $row['status'] = 'Menunggu';
                }
                $data[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Mengambil daftar LPJ untuk tabel LPJ.
     */
    public function getDashboardLPJ() {
        $query = "SELECT 
                    l.lpjId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    l.submittedAt as tanggal_pengajuan,
                    l.approvedAt,
                    l.tenggatLpj,
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'setuju'
                        WHEN l.submittedAt IS NULL THEN 'menunggu'
                        WHEN EXISTS (
                            SELECT 1 FROM tbl_lpj_item li 
                            WHERE li.lpjId = l.lpjId 
                            AND (li.fileBukti IS NULL OR li.fileBukti = '')
                        ) THEN 'Menunggu_Upload'
                        ELSE 'Siap_Submit'
                    END as status
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  ORDER BY 
                    CASE 
                        WHEN l.approvedAt IS NULL AND l.submittedAt IS NOT NULL THEN 1
                        WHEN l.approvedAt IS NULL AND l.submittedAt IS NULL THEN 2
                        ELSE 3
                    END,
                    l.submittedAt DESC";

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
     * Mengambil detail LPJ dengan status yang akurat.
     * 
     * STATUS FLOW:
     * 1. 'setuju' - LPJ disetujui bendahara (approvedAt NOT NULL)
     * 2. 'menunggu' - LPJ sudah disubmit, menunggu verifikasi (submittedAt NOT NULL)
     * 3. 'draft' - LPJ baru dibuat, belum ada item atau sedang upload
     */
    public function getDetailLPJ($lpjId) {
        error_log("=== adminModel::getDetailLPJ START ===");
        error_log("lpjId: " . $lpjId);
        
        // Cast ke integer untuk keamanan
        $lpjId = (int) $lpjId;
        
        $query = "SELECT 
                    l.*,
                    k.namaKegiatan as nama_kegiatan,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.kegiatanId,
                    kak.kakId,
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'setuju'
                        WHEN l.submittedAt IS NULL THEN 'menunggu'
                        ELSE 'draft'
                    END as status
                FROM tbl_lpj l
                JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                WHERE l.lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log("ERROR prepare statement: " . mysqli_error($this->db));
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("ERROR execute statement: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);
        
        if (!$data) {
            error_log("ERROR: No data found for lpjId: " . $lpjId);
            return null;
        }
        
        error_log("Raw DB Data:");
        error_log("  - lpjId: " . $data['lpjId']);
        error_log("  - kegiatanId: " . $data['kegiatanId']);
        error_log("  - kakId: " . ($data['kakId'] ?? 'NULL'));
        error_log("  - submittedAt: " . ($data['submittedAt'] ?? 'NULL'));
        error_log("  - approvedAt: " . ($data['approvedAt'] ?? 'NULL'));
        error_log("  - status (from query): " . $data['status']);
        
        // Cek jumlah item LPJ yang ada
        $countItemQuery = "SELECT COUNT(*) as total FROM tbl_lpj_item WHERE lpjId = ?";
        $stmtCount = mysqli_prepare($this->db, $countItemQuery);
        mysqli_stmt_bind_param($stmtCount, "i", $lpjId);
        mysqli_stmt_execute($stmtCount);
        $resultCount = mysqli_stmt_get_result($stmtCount);
        $countData = mysqli_fetch_assoc($resultCount);
        mysqli_stmt_close($stmtCount);
        
        $totalItems = $countData['total'] ?? 0;
        error_log("  - Total items in tbl_lpj_item: " . $totalItems);
        
        // Jika status draft, cek detail upload
        if ($data['status'] === 'draft') {
            if ($totalItems === 0) {
                // Belum ada item sama sekali
                $data['status'] = 'belum_ada_item';
                error_log("Status updated to: belum_ada_item (no items in tbl_lpj_item)");
            } else {
                // Ada item, cek upload status
                $checkBuktiQuery = "SELECT 
                                        COUNT(*) as total,
                                        SUM(CASE WHEN fileBukti IS NOT NULL AND fileBukti != '' THEN 1 ELSE 0 END) as uploaded
                                    FROM tbl_lpj_item 
                                    WHERE lpjId = ?";
                
                $stmtCheck = mysqli_prepare($this->db, $checkBuktiQuery);
                mysqli_stmt_bind_param($stmtCheck, "i", $lpjId);
                mysqli_stmt_execute($stmtCheck);
                $resultCheck = mysqli_stmt_get_result($stmtCheck);
                $buktiStatus = mysqli_fetch_assoc($resultCheck);
                mysqli_stmt_close($stmtCheck);
                
                error_log("  - Bukti status - Total: {$buktiStatus['total']}, Uploaded: {$buktiStatus['uploaded']}");
                
                if ($buktiStatus['uploaded'] == $buktiStatus['total']) {
                    $data['status'] = 'siap_submit';
                    error_log("Status updated to: siap_submit (all bukti uploaded)");
                } else {
                    $data['status'] = 'menunggu_upload';
                    error_log("Status updated to: menunggu_upload (partial upload)");
                }
            }
        }
        
        error_log("Final status: " . $data['status']);
        error_log("=== adminModel::getDetailLPJ END ===");
        
        return $data;
    }

    /**
     * Auto-create LPJ items dari RAB jika belum ada.
     * Dipanggil saat pertama kali buka halaman detail LPJ.
     * 
     * @param int $lpjId - ID LPJ
     * @param int $kakId - ID KAK (untuk ambil data RAB)
     * @return bool - Success status
     */
    public function autoPopulateLPJItems($lpjId, $kakId) {
        error_log("=== autoPopulateLPJItems START ===");
        error_log("lpjId: {$lpjId}, kakId: {$kakId}");
        
        $lpjId = (int) $lpjId;
        $kakId = (int) $kakId;
        
        // 1. Cek apakah sudah ada items
        $checkQuery = "SELECT COUNT(*) as total FROM tbl_lpj_item WHERE lpjId = ?";
        $stmtCheck = mysqli_prepare($this->db, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "i", $lpjId);
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);
        $checkData = mysqli_fetch_assoc($resultCheck);
        mysqli_stmt_close($stmtCheck);
        
        $existingItems = $checkData['total'];
        error_log("Existing items in tbl_lpj_item: " . $existingItems);
        
        if ($existingItems > 0) {
            error_log("Items already exist, skipping auto-populate");
            return true; // Sudah ada, skip
        }
        
        // 2. Ambil semua RAB items dari tbl_rab
        $rabQuery = "SELECT 
                        r.rabItemId,
                        r.uraian,
                        r.rincian,
                        r.sat1,
                        r.vol1,
                        r.vol2,
                        r.sat2,
                        r.harga,
                        r.totalHarga,
                        cat.namaKategori as jenisBelanja
                    FROM tbl_rab r
                    JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                    WHERE r.kakId = ?
                    ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";
        
        $stmtRAB = mysqli_prepare($this->db, $rabQuery);
        mysqli_stmt_bind_param($stmtRAB, "i", $kakId);
        mysqli_stmt_execute($stmtRAB);
        $resultRAB = mysqli_stmt_get_result($stmtRAB);
        
        $rabItems = [];
        while ($row = mysqli_fetch_assoc($resultRAB)) {
            $rabItems[] = $row;
        }
        mysqli_stmt_close($stmtRAB);
        
        error_log("Total RAB items fetched: " . count($rabItems));
        
        if (empty($rabItems)) {
            error_log("WARNING: No RAB items found for kakId: {$kakId}");
            return false;
        }
        
        // 3. Insert ke tbl_lpj_item
        mysqli_begin_transaction($this->db);
        
        try {
            // ✅ PERBAIKAN: Query INSERT yang benar
            $insertQuery = "INSERT INTO tbl_lpj_item 
                            (lpjId, jenisBelanja, uraian, rincian, vol1, sat1, vol2, sat2, totalHarga, subTotal, fileBukti) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
            
            $stmtInsert = mysqli_prepare($this->db, $insertQuery);
            
            if (!$stmtInsert) {
                throw new Exception("Failed to prepare insert statement: " . mysqli_error($this->db));
            }
            
            $insertCount = 0;
            
            foreach ($rabItems as $item) {
                $jenisBelanja = $item['jenisBelanja'];
                $uraian = $item['uraian'];
                $rincian = $item['rincian'];
                $vol1 = $item['vol1'] ?? '';
                $sat1 = $item['sat1'] ?? '';
                $vol2 = $item['vol2'] ?? '';
                $sat2 = $item['sat2'] ?? '';
                $totalHarga = $item['totalHarga']; // Rencana
                $subTotal = $item['totalHarga'];   // Default realisasi = rencana
                
                // ✅ PERBAIKAN: Bind param yang benar (10 parameter)
                mysqli_stmt_bind_param(
                    $stmtInsert, 
                    "isssssssdd", 
                    $lpjId, 
                    $jenisBelanja, 
                    $uraian, 
                    $rincian, 
                    $vol1,
                    $sat1,
                    $vol2,
                    $sat2, 
                    $totalHarga, 
                    $subTotal
                );
                
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Failed to insert item: " . mysqli_stmt_error($stmtInsert));
                }
                
                $insertCount++;
            }
            
            mysqli_stmt_close($stmtInsert);
            mysqli_commit($this->db);
            
            error_log("Successfully inserted {$insertCount} items into tbl_lpj_item");
            error_log("=== autoPopulateLPJItems END (SUCCESS) ===");
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("ERROR: " . $e->getMessage());
            error_log("=== autoPopulateLPJItems END (FAILED) ===");
            return false;
        }
    }

    /**
     * Mengambil item RAB untuk LPJ dengan data dari tbl_lpj_item (jika ada).
     * UPDATED: Ambil dari tbl_lpj_item, bukan langsung dari tbl_rab.
     */
    // Di adminModel.php, ganti method getRABForLPJ dengan yang sudah diperbaiki:

    public function getRABForLPJ($lpjId, $kakId) {
        error_log("=== adminModel::getRABForLPJ START ===");
        error_log("lpjId: {$lpjId}, kakId: {$kakId}");
        
        $lpjId = (int) $lpjId;
        $kakId = (int) $kakId;
        
        // PENTING: Auto-populate dulu jika belum ada items
        $this->autoPopulateLPJItems($lpjId, $kakId);
        
        // ✅ PERBAIKAN: Query yang disesuaikan dengan struktur tbl_lpj_item
        $query = "SELECT 
                    li.lpjItemId as id,
                    li.uraian,
                    li.rincian,
                    li.vol1,
                    li.sat1,
                    li.vol2,
                    li.sat2,
                    li.totalHarga as harga_satuan,
                    li.totalHarga as harga_plan,
                    li.subTotal as realisasi,
                    li.fileBukti as bukti_file,
                    li.komentar,
                    li.jenisBelanja as namaKategori
                FROM tbl_lpj_item li
                WHERE li.lpjId = ?
                ORDER BY li.lpjItemId ASC";

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
            
            // Group by kategori
            $kategori = $row['namaKategori'];
            unset($row['namaKategori']); // Remove from row data
            
            $data[$kategori][] = $row;
        }
        
        mysqli_stmt_close($stmt);
        
        error_log("Total LPJ items fetched: " . $itemCount);
        error_log("Categories: " . implode(', ', array_keys($data)));
        error_log("=== adminModel::getRABForLPJ END ===");
        
        return $data;
    }

    /**
     * Mengambil detail lengkap kegiatan beserta data KAK, Penanggung Jawab, dan file pendukung.
     *
     * Method ini melakukan JOIN dengan beberapa tabel untuk mengumpulkan informasi lengkap:
     * - Data kegiatan utama dari tbl_kegiatan
     * - Data KAK dari tbl_kak
     * - Data Penanggung Jawab dari tbl_user (via rancangan_kegiatan)
     * - Tanggal mulai dan selesai dari tbl_rancangan_kegiatan
     * - File surat pengantar dari tbl_rancangan_kegiatan
     * - Status usulan dari tbl_status_utama
     *
     * @param int $kegiatanId ID dari kegiatan yang akan diambil detailnya
     * @return array|null Array asosiatif berisi detail kegiatan, atau null jika tidak ditemukan
     * @throws mysqli_sql_exception Jika terjadi kesalahan database
     */
    public function getDetailKegiatan($kegiatanId)
    {
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
                // --   LIMIT 1";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log('Failed to prepare statement in getDetailKegiatan: ' . mysqli_error($this->db));
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $data;
    }

    /**
     * Mengambil indikator KAK.
     */
    public function getIndikatorByKAK($kakId) {
        $query = "SELECT 
                    bulan, 
                    indikatorKeberhasilan as nama, 
                    targetPersen as target 
                FROM tbl_indikator_kak 
                WHERE kakId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
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
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        return $data;
    }

    /**
     * Mengambil RAB (dikelompokkan berdasarkan kategori).
     */
    public function getRABByKAK($kakId) {
        $query = "SELECT 
                    r.*, 
                    cat.namaKategori 
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
     * Mengambil komentar revisi terbaru untuk suatu kegiatan.
     */
    public function getKomentarTerbaru($kegiatanId) {
        $queryHistory = "SELECT ph.progressHistoryId 
                         FROM tbl_progress_history ph 
                         WHERE ph.kegiatanId = ? 
                         AND ph.statusId = 2
                         ORDER BY ph.progressHistoryId DESC 
                         LIMIT 1";
        
        $stmtHistory = mysqli_prepare($this->db, $queryHistory);
        mysqli_stmt_bind_param($stmtHistory, "i", $kegiatanId);
        mysqli_stmt_execute($stmtHistory);
        $resultHistory = mysqli_stmt_get_result($stmtHistory);
        $history = mysqli_fetch_assoc($resultHistory);
        mysqli_stmt_close($stmtHistory);
        
        if (!$history) {
            return [];
        }
        
        $historyId = $history['progressHistoryId'];
        
        $queryKomentar = "SELECT targetKolom, komentarRevisi 
                          FROM tbl_revisi_comment 
                          WHERE progressHistoryId = ?";
        
        $stmtKomentar = mysqli_prepare($this->db, $queryKomentar);
        mysqli_stmt_bind_param($stmtKomentar, "i", $historyId);
        mysqli_stmt_execute($stmtKomentar);
        $resultKomentar = mysqli_stmt_get_result($stmtKomentar);
        
        $komentar = [];
        while ($row = mysqli_fetch_assoc($resultKomentar)) {
            if (!empty($row['targetKolom'])) {
                $komentar[$row['targetKolom']] = $row['komentarRevisi'];
            }
        }
        mysqli_stmt_close($stmtKomentar);
        
        return $komentar;
    }

    /**
     * Mengambil komentar penolakan terbaru untuk suatu kegiatan.
     */
    public function getKomentarPenolakan($kegiatanId) {
        $query = "SELECT rc.komentarRevisi 
                  FROM tbl_revisi_comment rc
                  JOIN tbl_progress_history ph ON rc.progressHistoryId = ph.progressHistoryId
                  WHERE ph.kegiatanId = ? 
                  AND ph.statusId = 4
                  AND rc.targetKolom IS NULL
                  ORDER BY ph.progressHistoryId DESC 
                  LIMIT 1";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $row['komentarRevisi'] ?? '';
    }

    /**
     * Menyimpan pengajuan KAK lengkap.
     */
    public function simpanPengajuan($data) {
        mysqli_begin_transaction($this->db);

        try {
            $nama_pengusul = $data['nama_pengusul'] ?? '';
            $nim           = $data['nim_nip'] ?? '';
            $jurusan       = $data['jurusan'] ?? '';
            $prodi         = $data['prodi'] ?? '';
            $nama_kegiatan = $data['nama_kegiatan_step1'] ?? '';
            $user_id       = $_SESSION['user_id'] ?? 0;
            $tgl_sekarang  = date('Y-m-d H:i:s');
            $status_awal   = 1;
            $wadir_tujuan  = $data['wadir_tujuan'] ?? null; 
            
            $posisi_tujuan = 2;

            $queryKegiatan = "INSERT INTO tbl_kegiatan 
            (namaKegiatan, prodiPenyelenggara, pemilikKegiatan, nimPelaksana, userId, jurusanPenyelenggara, statusUtamaId, createdAt, wadirTujuan, posisiId)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = mysqli_prepare($this->db, $queryKegiatan);
            mysqli_stmt_bind_param($stmt, "ssssisisii", $nama_kegiatan, $prodi, $nama_pengusul, $nim, $user_id, $jurusan, $status_awal, $tgl_sekarang, $wadir_tujuan, $posisi_tujuan);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert kegiatan: " . mysqli_error($this->db));
            }
            
            $kegiatanId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);

            $iku           = $data['indikator_kinerja'] ?? 'Belum pilih';
            $gambaran_umum = $data['gambaran_umum'] ?? '';
            $penerima      = $data['penerima_manfaat'] ?? '';
            $metode        = $data['metode_pelaksanaan'] ?? '';
            $tgl_only      = date('Y-m-d');
            
            $queryKAK = "INSERT INTO tbl_kak 
                (kegiatanId, iku, gambaranUmum, penerimaMaanfaat, metodePelaksanaan, tglPembuatan)
                VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->db, $queryKAK);
            mysqli_stmt_bind_param(
                $stmt, "isssss", $kegiatanId, $iku, $gambaran_umum, $penerima, $metode, $tgl_only
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert KAK: " . mysqli_error($this->db));
            }

            $kakId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);

            if (!empty($data['tahapan']) && is_array($data['tahapan'])) {
                $queryTahapan = "INSERT INTO tbl_tahapan_pelaksanaan (kakId, namaTahapan) VALUES (?, ?)";
                $stmt = mysqli_prepare($this->db, $queryTahapan);
                
                foreach ($data['tahapan'] as $tahap) {
                    if (!empty($tahap)) {
                        mysqli_stmt_bind_param($stmt, "is", $kakId, $tahap);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Gagal insert tahapan: " . mysqli_error($this->db));
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }

            if (!empty($data['indikator_nama']) && is_array($data['indikator_nama'])) {
                $queryIndikator = "INSERT INTO tbl_indikator_kak (kakId, bulan, indikatorKeberhasilan, targetPersen) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->db, $queryIndikator);
                
                $count = count($data['indikator_nama']);
                
                for ($i = 0; $i < $count; $i++) {
                    $bulan  = $data['indikator_bulan'][$i] ?? '';
                    $nama   = $data['indikator_nama'][$i] ?? '';
                    $target = $data['indikator_target'][$i] ?? '';
                    
                    if (!empty($nama)) {
                        mysqli_stmt_bind_param($stmt, "isss", $kakId, $bulan, $nama, $target);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Gagal insert indikator: " . mysqli_error($this->db));
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }

            $rab_json = $data['rab_data'] ?? '[]'; 
            $budgetData = json_decode($rab_json, true);

            if (!empty($budgetData) && is_array($budgetData)) {
                
                $queryKategori = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
                $queryItemRAB  = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                foreach ($budgetData as $namaKategori => $items) {
                    if (empty($items)) continue; 

                    $kategoriId = 0;
                    
                    $checkKat = mysqli_prepare($this->db, "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1");
                    mysqli_stmt_bind_param($checkKat, "s", $namaKategori);
                    mysqli_stmt_execute($checkKat);
                    $resKat = mysqli_stmt_get_result($checkKat);
                    
                    if ($rowKat = mysqli_fetch_assoc($resKat)) {
                        $kategoriId = $rowKat['kategoriRabId'];
                    } 
                    
                    mysqli_stmt_close($checkKat); 

                    if ($kategoriId == 0) {
                        $stmtKat = mysqli_prepare($this->db, $queryKategori);
                        mysqli_stmt_bind_param($stmtKat, "s", $namaKategori);
                        if (!mysqli_stmt_execute($stmtKat)) {
                            throw new Exception("Gagal insert kategori RAB: " . mysqli_error($this->db));
                        }
                        $kategoriId = mysqli_insert_id($this->db);
                        mysqli_stmt_close($stmtKat);
                    }

                    $stmtItem = mysqli_prepare($this->db, $queryItemRAB);
                    foreach ($items as $item) {
                        $uraian  = $item['uraian'] ?? '';
                        $rincian = $item['rincian'] ?? '';
                        
                        $vol1 = floatval($item['vol1'] ?? 0);
                        $vol2 = floatval($item['vol2'] ?? 1);
                        $sat1 = $item['sat1'] ?? '';
                        $sat2 = $item['sat2'] ?? '';

                        $volume = $vol1 * $vol2; 
                        $harga   = floatval($item['harga'] ?? 0);
                        $total   = $volume * $harga;

                        mysqli_stmt_bind_param($stmtItem, "iissssdddd", $kakId, $kategoriId, $uraian, $rincian, $sat1, $sat2, $vol1, $vol2, $harga, $total);
                        if (!mysqli_stmt_execute($stmtItem)) {
                            throw new Exception("Gagal insert item RAB: " . mysqli_error($this->db));
                        }
                    }
                    mysqli_stmt_close($stmtItem);
                }
            }

            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("Gagal Simpan Pengajuan: " . $e->getMessage());
            
            return false;
        }
    }

    /**
     * Memperbarui surat pengantar.
     */
    public function updateSuratPengantar($kegiatanId, $fileName) {
        $query = "UPDATE tbl_kegiatan SET suratPengantar = ? WHERE kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $fileName, $kegiatanId);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
        return false;
    }

    /**
     * Memperbarui rincian kegiatan (PJ, Tanggal, Surat).
     */
    public function updateRincianKegiatan($id, $data, $fileSurat = null) {
        $posisiIdPPK = 4;
        $statusMenunggu = 3;
        
        if ($fileSurat) {
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ?, 
                        suratPengantar = ?,
                        posisiId = ?,
                        statusUtamaId = ?
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "sssssiii", 
                $data['namaPj'], 
                $data['nip'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $fileSurat, 
                $posisiIdPPK,
                $statusMenunggu,
                $id
            );
        } else {
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ?,
                        posisiId = ?,
                        statusUtamaId = ?
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "ssssiii", 
                $data['namaPj'], 
                $data['nip'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $posisiIdPPK,
                $statusMenunggu,
                $id
            );
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}