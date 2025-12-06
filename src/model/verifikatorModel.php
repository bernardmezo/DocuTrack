<?php
declare(strict_types=1);

use Core\Database;
use mysqli;
use RuntimeException;
use Throwable;

/**
 * verifikatorModel - Verifikator Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class verifikatorModel
{
    private mysqli $db;

    public function __construct(?mysqli $db = null)
    {
        if ($db instanceof mysqli) {
            $this->db = $db;
            return;
        }

        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE 
                        WHEN posisiId IN (1 ,3, 4, 5) AND statusUtamaId != 4 AND (statusUtamaId = 3 OR statusUtamaId = 5) THEN 1 
                        ELSE 0 
                    END) as disetujui,
                    SUM(CASE 
                        WHEN statusUtamaId = 4 THEN 1 
                        ELSE 0 
                    END) as ditolak,
                    SUM(CASE 
                        WHEN posisiId = 2 THEN 1 
                        ELSE 0 
                    END) as pending
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            return [
                'total' => $data['total'],
                'disetujui' => $data['disetujui'],
                'ditolak' => $data['ditolak'],
                'pending' => $data['pending']
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'pending' => 0];
    }

    /**
     * Mengambil daftar KAK untuk tabel.
     */
    public function getDashboardKAK() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.pemilikKegiatan as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    s.namaStatusUsulan as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 2
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
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Mengambil indikator KAK.
     */
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
        while ($row = mysqli_fetch_assoc($result)) { $data[] = $row['namaTahapan']; }
        return $data;
    }

    /**
     * Mengambil RAB (dikelompokkan berdasarkan kategori).
     */
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
     * Memperbarui status kegiatan.
     */
    public function updateStatus($kegiatanId, $statusId) {
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $statusId, $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Mengambil daftar jurusan.
     */
    public function getListJurusan() {
        $query = "SELECT * FROM tbl_jurusan j";
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->db));
            return [];
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $jurusan = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $jurusan[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        return $jurusan;
    }

    /**
     * Mengambil data riwayat verifikasi.
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
                    CASE 
                        WHEN k.posisiId IN (1, 3, 4, 5) AND k.statusUtamaId != 4 AND (k.statusUtamaId = 3 OR k.statusUtamaId = 5) THEN 'Disetujui'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 
                    k.posisiId IN (1 ,3, 4, 5) AND (k.statusUtamaId = 3 OR k.statusUtamaId = 5)
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
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }
    /**
     * Menyetujui usulan dan mengembalikan ke Admin untuk melengkapi rincian.
     * 
     * Logic:
     * 1. Update statusUtamaId = 3 (Disetujui/Menunggu Rincian)
     * 2. Update posisiId = 1 (Kembali ke Admin)
     * 3. Input buktiMAK
     */
    public function approveUsulan(int $kegiatanId, string $kodeMak): bool
    {
        $trimmedMak = trim($kodeMak);

        if ($trimmedMak === '') {
            error_log('approveUsulan Error: Kode MAK tidak boleh kosong.');
            return false;
        }

        $connection = $this->db;
        $currentPosisi = 2; // Verifikator
        $nextPosisi = 1;    // Admin (PENTING: Kembali ke Admin dulu)
        $nextStatus = 3;    // Disetujui Verifikator

        $userId = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
            ? (int) $_SESSION['user_id']
            : null;

        $transactionStarted = false;

        try {
            $connection->begin_transaction();
            $transactionStarted = true;

            // Lock row untuk mencegah race condition
            $lockStmt = $connection->prepare('SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE');
            if ($lockStmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement lock.');
            }
            $lockStmt->bind_param('i', $kegiatanId);
            $lockStmt->execute();
            $result = $lockStmt->get_result();
            if ($result->num_rows === 0) {
                throw new RuntimeException('Kegiatan tidak ditemukan.');
            }
            $lockStmt->close();

            // ⚠️ PERBAIKAN SYNTAX ERROR DI SINI ⚠️
            // Query yang SALAH (ada koma setelah statusUtamaId tanpa nilai):
            // UPDATE tbl_kegiatan SET statusUtamaId, posisiId = ?, buktiMAK = ? ...
            
            // Query yang BENAR:
            $sql = 'UPDATE tbl_kegiatan 
                    SET statusUtamaId = ?, 
                        posisiId = ?, 
                        buktiMAK = ?
                    WHERE kegiatanId = ?';
            
            $updateStmt = $connection->prepare($sql);
            if ($updateStmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement update: ' . $connection->error);
            }

            // Bind parameters: 4 integers (statusUtamaId, posisiId, kegiatanId) + 1 string (buktiMAK)
            $updateStmt->bind_param('iisi', $nextStatus, $nextPosisi, $trimmedMak, $kegiatanId);
            
            if (!$updateStmt->execute()) {
                throw new RuntimeException('Gagal update kegiatan: ' . $updateStmt->error);
            }
            
            // Debug: cek apakah ada row yang ter-update
            if ($updateStmt->affected_rows === 0) {
                error_log('approveUsulan Warning: Tidak ada baris yang ter-update untuk kegiatanId: ' . $kegiatanId);
            }
            
            $updateStmt->close();

            // Catat History
            $historyId = $this->insertProgressHistory(
                $kegiatanId,
                $nextStatus,
                $currentPosisi,
                $nextPosisi,
                $userId,
                'verifikator_approve'
            );

            if ($historyId <= 0) {
                throw new RuntimeException('Gagal mencatat riwayat.');
            }

            $connection->commit();
            return true;

        } catch (Throwable $e) {
            if ($transactionStarted) {
                $connection->rollback();
            }
            error_log('approveUsulan Exception: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Buat Menolak usulan.
     * 
     * Logic:
     * 1. Update statusUtamaId = 4 (Ditolak)
     * 2. Update posisiId = 1 (Kembali ke Admin untuk info)
     * 3. Simpan alasan penolakan di tbl_revisi_comment
     */
    public function rejectUsulan($kegiatanId, $alasanPenolakan = '') {
        $statusDitolak = 4;      // Status: Ditolak
        $currentPosisi = 2;       // Verifikator
        $backToPosisi = 1;        // Kembali ke Admin
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = 2;              // Role Verifikator
        
        // Validasi alasan penolakan
        $trimmedAlasan = trim($alasanPenolakan);
        if ($trimmedAlasan === '') {
            error_log('rejectUsulan Error: Alasan penolakan tidak boleh kosong.');
            return false;
        }
        
        mysqli_begin_transaction($this->db);
        
        try {
            // Lock row untuk mencegah race condition
            $lockQuery = "SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE";
            $lockStmt = mysqli_prepare($this->db, $lockQuery);
            if (!$lockStmt) {
                throw new Exception("Gagal menyiapkan statement lock: " . mysqli_error($this->db));
            }
            mysqli_stmt_bind_param($lockStmt, "i", $kegiatanId);
            mysqli_stmt_execute($lockStmt);
            $result = mysqli_stmt_get_result($lockStmt);
            
            if (mysqli_num_rows($result) === 0) {
                throw new Exception("Kegiatan dengan ID {$kegiatanId} tidak ditemukan");
            }
            mysqli_stmt_close($lockStmt);
            
            // Update status dan posisi kegiatan
            $updateQuery = "UPDATE tbl_kegiatan 
                            SET statusUtamaId = ?, 
                                posisiId = ? 
                            WHERE kegiatanId = ?";
            
            $updateStmt = mysqli_prepare($this->db, $updateQuery);
            if (!$updateStmt) {
                throw new Exception("Gagal menyiapkan statement update: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($updateStmt, "iii", $statusDitolak, $backToPosisi, $kegiatanId);
            
            if (!mysqli_stmt_execute($updateStmt)) {
                throw new Exception("Gagal update status: " . mysqli_stmt_error($updateStmt));
            }
            
            $affectedRows = mysqli_stmt_affected_rows($updateStmt);
            mysqli_stmt_close($updateStmt);
            
            if ($affectedRows === 0) {
                error_log("rejectUsulan Warning: Tidak ada baris yang ter-update untuk kegiatanId: {$kegiatanId}");
            }
            
            // Catat ke progress history
            $historyId = $this->insertProgressHistory(
                $kegiatanId, 
                $statusDitolak, 
                $currentPosisi, 
                $backToPosisi, 
                $userId, 
                'reject'
            );
            
            if (!$historyId || $historyId <= 0) {
                throw new Exception("Gagal mencatat riwayat progress");
            }
            
            // Simpan alasan penolakan sebagai komentar
            if (!empty($trimmedAlasan)) {
                $commentResult = $this->insertRevisiComment(
                    $historyId, 
                    $userId, 
                    $roleId, 
                    $trimmedAlasan, 
                    'tbl_kegiatan',  // Target tabel
                    'alasan_penolakan' // Target kolom
                );
                
                if (!$commentResult) {
                    error_log("rejectUsulan Warning: Gagal menyimpan alasan penolakan");
                    // Tidak throw error karena ini optional
                }
            }
            
            mysqli_commit($this->db);
            error_log("rejectUsulan Success: Kegiatan {$kegiatanId} berhasil ditolak");
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("rejectUsulan Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper method untuk insert progress history
     * (Pastikan method ini sudah ada di class Anda)
     */
    /**
     * Helper method untuk insert progress history
     */
    private function insertProgressHistory(
        int $kegiatanId,
        int $statusId,
        int $fromPosisi,
        int $toPosisi,
        ?int $userId,
        string $actionType
    ): int {
        $sql = 'INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId) VALUES (?, ?, ';
        $placeholders = $userId === null ? 'NULL)' : '?)';
        $stmt = $this->db->prepare($sql . $placeholders);

        if ($stmt === false) {
            throw new RuntimeException('Gagal menyiapkan statement progress history: ' . $this->db->error);
        }

        if ($userId === null) {
            $stmt->bind_param('ii', $kegiatanId, $statusId);
        } else {
            $stmt->bind_param('iii', $kegiatanId, $statusId, $userId);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('Gagal execute progress history: ' . $stmt->error);
        }

        $insertId = (int) $this->db->insert_id;
        $stmt->close();

        return $insertId;
    }


    /**
     * Helper method untuk insert komentar revisi
     * (Pastikan method ini sudah ada di class Anda)
     */
    /**
     * Helper method untuk insert komentar revisi
     */
    private function insertRevisiComment($historyId, $userId, $roleId, $komentar, $targetTabel = null, $targetKolom = null) {
        error_log("insertRevisiComment called with historyId={$historyId}, komentar={$komentar}");
        
        $query = "INSERT INTO tbl_revisi_comment 
                (progressHistoryId, komentarRevisi, targetTabel, targetKolom) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log("ERROR prepare: " . mysqli_error($this->db));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "isss", $historyId, $komentar, $targetTabel, $targetKolom);
        
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            error_log("ERROR execute: " . mysqli_stmt_error($stmt));
        } else {
            error_log("Insert OK, affected rows: " . mysqli_stmt_affected_rows($stmt));
        }
        
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    /**
     * Mengirim usulan untuk direvisi.
     * 
     * Logic:
     * 1. Update statusUtamaId = 2 (Revisi)
     * 2. Update posisiId = 1 (Kembali ke Admin)
     * 3. Simpan semua komentar revisi ke tbl_revisi_comment
     */
    public function reviseUsulan($kegiatanId, $komentarRevisi = []) {
        error_log("=== reviseUsulan START ===");
        error_log("kegiatanId (raw): " . $kegiatanId . " (type: " . gettype($kegiatanId) . ")");
        
        // PENTING: Cast ke integer
        $kegiatanId = (int) $kegiatanId;
        error_log("kegiatanId (casted): " . $kegiatanId);
        error_log("komentarRevisi: " . print_r($komentarRevisi, true));
        
        // Validasi input
        if (empty($kegiatanId) || $kegiatanId <= 0) {
            error_log("ERROR: kegiatanId tidak valid");
            return false;
        }
        
        if (empty($komentarRevisi) || !is_array($komentarRevisi)) {
            error_log("ERROR: komentarRevisi kosong atau bukan array");
            return false;
        }
        
        $statusRevisi = 2;        // Status: Revisi
        $currentPosisi = 2;       // Verifikator
        $backToPosisi = 1;        // Kembali ke Admin
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $roleId = 2;              // Role Verifikator
        
        error_log("userId: " . ($userId ?? 'NULL'));
        error_log("Starting transaction...");
        
        // Mulai transaction
        mysqli_begin_transaction($this->db);
        
        try {
            // Lock row untuk mencegah race condition
            $lockQuery = "SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE";
            $lockStmt = mysqli_prepare($this->db, $lockQuery);
            
            if (!$lockStmt) {
                throw new Exception("Gagal menyiapkan statement lock: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($lockStmt, "i", $kegiatanId);
            mysqli_stmt_execute($lockStmt);
            $result = mysqli_stmt_get_result($lockStmt);
            
            if (mysqli_num_rows($result) === 0) {
                throw new Exception("Kegiatan dengan ID {$kegiatanId} tidak ditemukan");
            }
            mysqli_stmt_close($lockStmt);
            error_log("Lock OK");
            
            // Update status dan posisi kegiatan
            $updateQuery = "UPDATE tbl_kegiatan 
                            SET statusUtamaId = ?, 
                                posisiId = ? 
                            WHERE kegiatanId = ?";
            
            $updateStmt = mysqli_prepare($this->db, $updateQuery);
            
            if (!$updateStmt) {
                throw new Exception("Gagal menyiapkan statement update: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($updateStmt, "iii", $statusRevisi, $backToPosisi, $kegiatanId);
            
            if (!mysqli_stmt_execute($updateStmt)) {
                throw new Exception("Gagal update status: " . mysqli_stmt_error($updateStmt));
            }
            
            $affectedRows = mysqli_stmt_affected_rows($updateStmt);
            mysqli_stmt_close($updateStmt);
            error_log("Update OK, affected rows: " . $affectedRows);
            
            if ($affectedRows === 0) {
                error_log("WARNING: Tidak ada baris yang ter-update");
            }
            
            // Catat ke progress history
            error_log("Inserting progress history...");
            $historyId = $this->insertProgressHistory(
                $kegiatanId, 
                $statusRevisi, 
                $currentPosisi, 
                $backToPosisi, 
                $userId, 
                'revise'
            );
            
            if (!$historyId || $historyId <= 0) {
                throw new Exception("Gagal mencatat riwayat progress");
            }
            error_log("Progress history OK, ID: " . $historyId);
            
            // Simpan semua komentar revisi
            $successCount = 0;
            error_log("Inserting " . count($komentarRevisi) . " comments...");
            
            foreach ($komentarRevisi as $index => $komentar) {
                error_log("Processing comment #{$index}: " . print_r($komentar, true));
                
                $targetKolom = $komentar['targetKolom'] ?? null;
                $targetTabel = $komentar['targetTabel'] ?? 'tbl_kegiatan';
                $komentarText = $komentar['komentar'] ?? '';
                
                if (empty($komentarText)) {
                    error_log("WARNING: Komentar #{$index} kosong, skip");
                    continue;
                }
                
                $commentResult = $this->insertRevisiComment(
                    $historyId, 
                    $userId, 
                    $roleId, 
                    $komentarText, 
                    $targetTabel,
                    $targetKolom
                );
                
                if ($commentResult) {
                    $successCount++;
                    error_log("Comment #{$index} inserted OK");
                } else {
                    error_log("WARNING: Gagal insert comment #{$index}");
                }
            }
            
            error_log("Successfully inserted {$successCount} comments");
            
            if ($successCount === 0) {
                throw new Exception("Tidak ada komentar yang berhasil disimpan");
            }
            
            // Commit transaction
            mysqli_commit($this->db);
            error_log("Transaction committed successfully");
            error_log("=== reviseUsulan END (SUCCESS) ===");
            return true;
            
        } catch (Exception $e) {
            // Rollback jika error
            mysqli_rollback($this->db);
            error_log("Transaction rolled back");
            error_log("ERROR: " . $e->getMessage());
            error_log("=== reviseUsulan END (FAILED) ===");
            return false;
        }
    }
    

    /**
     * Mengambil data proposal untuk monitoring.
     */
    public function getProposalMonitoring() {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.jurusanPenyelenggara as jurusan,
                    s.namaStatusUsulan as status,
                    r.namaRole as tahap_sekarang
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                LEFT JOIN tbl_role r ON k.posisiId = r.roleId
                ORDER BY k.createdAt DESC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst(strtolower($row['status']));
                } else {
                    $row['status'] = 'Menunggu';
                }
                if (!isset($row['tahap_sekarang']) || empty($row['tahap_sekarang'])) {
                    $row['tahap_sekarang'] = 'Admin';
                }
                $data[] = $row;
            }
        }
        return $data;
    }
}