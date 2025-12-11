<?php

namespace App\Services;

use App\Models\BendaharaModel;
use App\Services\LogStatusService;
use Exception;
use DateTime;
use Throwable;
use mysqli;

class PencairanService
{
    /**
     * @var mysqli Database connection
     */
    private $db;

    /**
     * @var BendaharaModel
     */
    private $bendaharaModel;

    /**
     * @var LogStatusService
     */
    private $logStatusService;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->bendaharaModel = new BendaharaModel($db);
        $this->logStatusService = new LogStatusService($db);
    }

    /**
     * Get Dashboard Statistics for Bendahara
     */
    public function getDashboardStats()
    {
        return $this->bendaharaModel->getDashboardStats();
    }

    /**
     * Get List of Pencairan Queue
     */
    public function getAntrianPencairan()
    {
        return $this->bendaharaModel->getAntrianPencairan();
    }

    /**
     * Get List of Jurusan
     */
    public function getListJurusan()
    {
        return $this->bendaharaModel->getListJurusan();
    }

    /**
     * Get Detail Pencairan by ID
     */
    public function getDetailPencairan($id)
    {
        return $this->bendaharaModel->getDetailPencairan($id);
    }

    /**
     * Get RAB by Kegiatan ID
     */
    public function getRABByKegiatan($id)
    {
        return $this->bendaharaModel->getRABByKegiatan($id);
    }

    /**
     * Get IKU by Kegiatan ID
     */
    public function getIKUByKegiatan($id)
    {
        return $this->bendaharaModel->getIKUByKegiatan($id);
    }

    /**
     * Get Indikator by Kegiatan ID
     */
    public function getIndikatorByKegiatan($id)
    {
        return $this->bendaharaModel->getIndikatorByKegiatan($id);
    }

    /**
     * Get Tahapan by Kegiatan ID
     */
    public function getTahapanByKegiatan($id)
    {
        return $this->bendaharaModel->getTahapanByKegiatan($id);
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
     * @return bool
     * @throws Exception
     */
    public function cairkanDana($kegiatanId, $dataPencairan)
    {
        // ===== VALIDASI & LOGGING =====
        error_log("=== CAIRKAN DANA SERVICE ===");
        error_log("Kegiatan ID: " . $kegiatanId);
        error_log("Data Pencairan: " . print_r($dataPencairan, true));
        
        $jumlah = $dataPencairan['jumlah'] ?? 0;
        $tanggalCair = $dataPencairan['tanggal'] ?? date('Y-m-d');
        $metode = $dataPencairan['metode'] ?? 'penuh';
        $tahapan = $dataPencairan['tahapan'] ?? [];
        $catatan = $dataPencairan['catatan'] ?? '';

        // ===== VALIDASI JUMLAH =====
        if (!is_numeric($jumlah) || $jumlah <= 0) {
            throw new Exception("Jumlah pencairan tidak valid: " . var_export($jumlah, true));
        }
        
        $jumlah = (float) $jumlah; // Pastikan tipe data float
        error_log("Jumlah final (float): " . $jumlah);

        // Logic untuk menentukan Base Date perhitungan LPJ
        $baseDateForLpj = $tanggalCair;
        $jsonTahapan = null;

        if ($metode === 'bertahap' && !empty($tahapan)) {
            // Validasi total persentase
            $totalPersen = array_sum(array_column($tahapan, 'persentase'));
            if (abs($totalPersen - 100) > 0.01) {
                throw new Exception("Total persentase tahapan harus 100%. Saat ini: {$totalPersen}%");
            }

            // Encode JSON
            $jsonTahapan = json_encode($tahapan);
            error_log("JSON Tahapan: " . $jsonTahapan);

            // Ambil tanggal tahap terakhir untuk deadline LPJ
            $lastStage = end($tahapan);
            $baseDateForLpj = $lastStage['tanggal'];
        }

        $tenggatLpj = $this->calculateLpjDeadline($baseDateForLpj);
        error_log("Tenggat LPJ: " . $tenggatLpj);

        // ===== MULAI TRANSAKSI =====
        mysqli_begin_transaction($this->db);
        
        try {
            // 1. Update Kegiatan
            $query = "UPDATE tbl_kegiatan 
                    SET tanggalPencairan = ?, 
                        jumlahDicairkan = ?, 
                        metodePencairan = ?, 
                        catatanBendahara = ?,
                        statusUtamaId = 5,
                        posisiId = 1 
                    WHERE kegiatanId = ?";

            $stmt = mysqli_prepare($this->db, $query);
            
            if (!$stmt) {
                throw new Exception("Prepare statement gagal: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param(
                $stmt,
                "sdssi",
                $tanggalCair,
                $jumlah,
                $metode,
                $catatan,
                $kegiatanId
            );

            error_log("Executing UPDATE query...");
            $execResult = mysqli_stmt_execute($stmt);
            
            if (!$execResult) {
                throw new Exception("Gagal update data pencairan: " . mysqli_stmt_error($stmt));
            }
            
            $affectedRows = mysqli_stmt_affected_rows($stmt);
            error_log("Affected rows: " . $affectedRows);
            
            if ($affectedRows === 0) {
                // Cek apakah kegiatan ada
                $checkQuery = "SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ?";
                $checkStmt = mysqli_prepare($this->db, $checkQuery);
                mysqli_stmt_bind_param($checkStmt, "i", $kegiatanId);
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);
                
                if (mysqli_num_rows($checkResult) === 0) {
                    throw new Exception("Kegiatan dengan ID {$kegiatanId} tidak ditemukan");
                }
                
                mysqli_stmt_close($checkStmt);
                error_log("WARNING: Update berhasil tapi tidak ada row yang berubah (kemungkinan data sudah sama)");
            }
            
            mysqli_stmt_close($stmt);

            // 2. Update/Create LPJ Deadline
            error_log("Creating/Updating LPJ placeholder...");
            if (!$this->createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)) {
                throw new Exception("Gagal set tenggat LPJ.");
            }

            // 3. Log History
            $statusDisetujui = 3;
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, timestamp) VALUES (?, ?, NOW())";
            $stmtHist = mysqli_prepare($this->db, $historyQuery);
            
            if (!$stmtHist) {
                throw new Exception("Prepare history gagal: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($stmtHist, "ii", $kegiatanId, $statusDisetujui);
            mysqli_stmt_execute($stmtHist);
            mysqli_stmt_close($stmtHist);

            // ===== COMMIT TRANSAKSI =====
            mysqli_commit($this->db);
            error_log("Transaction committed successfully");

            // --- NOTIFICATION TRIGGER ---
            try {
                $kegiatan = $this->bendaharaModel->getDetailPencairan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $pesan = "Dana untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah dicairkan sebesar Rp " . number_format($jumlah, 0, ',', '.');
                    if ($metode === 'bertahap') {
                        $pesan .= " (Metode Bertahap)";
                    }

                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'PENCAIRAN',
                        $pesan,
                        $kegiatanId,
                        'INFO',
                        $kegiatanId
                    );
                    
                    error_log("Notification created successfully");
                }
            } catch (Throwable $e) {
                error_log("Gagal kirim notifikasi pencairan: " . $e->getMessage());
            }
            // ----------------------------

            error_log("=== CAIRKAN DANA SUCCESS ===");
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("=== CAIRKAN DANA FAILED ===");
            error_log("Error: " . $e->getMessage());
            error_log("File: " . $e->getFile() . ":" . $e->getLine());
            throw $e;
        }
    }

    /**
     * Helper: Hitung Tenggat LPJ (14 Hari Kerja setelah pencairan).
     */
    private function calculateLpjDeadline($startDate)
    {
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
     * Membuat atau update row LPJ saat dana dicairkan
     */
    private function createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)
    {
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
}
