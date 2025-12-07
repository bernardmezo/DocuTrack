<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use mysqli;
use RuntimeException;
use Throwable;

/**
 * VerifikatorModel - Verifikator Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class VerifikatorModel
{
    private mysqli $db;

    public function __construct(?mysqli $db = null)
    {
        if ($db instanceof mysqli) {
            $this->db = $db;
            return;
        }

        // Fallback to global db() helper function from bootstrap.php
        if (function_exists('db')) {
            $this->db = db();
        } else {
            // Last resort: get from Database singleton
            try {
                $this->db = \App\Core\Database::getInstance()->getConnection();
            } catch (\Exception $e) {
                throw new \Exception("Database connection not provided to VerifikatorModel. " . $e->getMessage());
            }
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE 
                        WHEN posisiId IN (4, 3, 5) AND statusUtamaId != 4 THEN 1 
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
                        WHEN k.posisiId IN (3, 4, 5) AND k.statusUtamaId != 4 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        ELSE 'Diproses'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 
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
     * 3. Input buktiMAK & umpanBalikVerifikator
     */
    public function approveUsulan(int $kegiatanId, string $kodeMak, ?string $catatan = null): bool
    {
        $trimmedMak = trim($kodeMak);

        if ($trimmedMak === '') {
            error_log('approveUsulan Error: Kode MAK tidak boleh kosong.');
            return false;
        }

        $note = null;
        if ($catatan !== null) {
            $catatanTrimmed = trim($catatan);
            if ($catatanTrimmed !== '') {
                $note = $catatanTrimmed;
            }
        }

        $connection = $this->db;
        $currentPosisi = 2; // Verifikator
        $nextPosisi = 1;    // Admin (PENTING: Kembali ke Admin dulu)
        $nextStatus = 3;    // Disetujui Verifikator - Menunggu Rincian

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

            // Prepare Update Query
            $sql = 'UPDATE tbl_kegiatan 
                    SET statusUtamaId = ?, 
                        posisiId = ?, 
                        buktiMAK = ?, 
                        umpanBalikVerifikator = ?
                    WHERE kegiatanId = ?';
            
            $updateStmt = $connection->prepare($sql);
            if ($updateStmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement update.');
            }

            $updateStmt->bind_param('iisss', $nextStatus, $nextPosisi, $trimmedMak, $note, $kegiatanId);
            
            if (!$updateStmt->execute()) {
                throw new RuntimeException('Gagal update kegiatan: ' . $updateStmt->error);
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
     * Menolak usulan.
     */
    public function rejectUsulan($kegiatanId, $alasanPenolakan = '') {
        $statusDitolak = 4;
        $currentPosisi = 2;
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = 2;
        
        mysqli_begin_transaction($this->db);
        
        try {
            $query = "UPDATE tbl_kegiatan 
                      SET statusUtamaId = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $statusDitolak, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update status");
            }
            mysqli_stmt_close($stmt);
            
            $historyId = $this->insertProgressHistory($kegiatanId, $statusDitolak, $currentPosisi, $currentPosisi, $userId, 'reject');
            
            if (!empty($alasanPenolakan) && $historyId) {
                $this->insertRevisiComment(
                    $historyId, 
                    $userId, 
                    $roleId, 
                    $alasanPenolakan, 
                    null, 
                    null
                );
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
     * Mengirim usulan untuk direvisi.
     */
    public function reviseUsulan($kegiatanId, $komentarRevisi = []) {
        $statusRevisi = 2;
        $currentPosisi = 2;
        $backToPosisi = 1;
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = 2;
        
        mysqli_begin_transaction($this->db);
        
        try {
            $query = "UPDATE tbl_kegiatan 
                      SET statusUtamaId = ?, posisiId = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $statusRevisi, $backToPosisi, $kegiatanId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update status");
            }
            mysqli_stmt_close($stmt);
            
            $historyId = $this->insertProgressHistory($kegiatanId, $statusRevisi, $currentPosisi, $backToPosisi, $userId, 'revise');
            
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

    /**
     * Memasukkan record ke tabel tbl_progress_history.
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
            throw new RuntimeException('Gagal menyiapkan statement progress history.');
        }

        if ($userId === null) {
            $stmt->bind_param('ii', $kegiatanId, $statusId);
        } else {
            $stmt->bind_param('iii', $kegiatanId, $statusId, $userId);
        }

        $stmt->execute();
        $insertId = (int) $this->db->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * Memasukkan komentar revisi ke tabel tbl_revisi_comment.
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