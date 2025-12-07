<?php

namespace App\Services;

use App\Models\BendaharaModel;
use App\Services\LogStatusService;
use Exception;
use DateTime;
use Throwable;

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
        $jumlah = $dataPencairan['jumlah'] ?? 0;
        $tanggalCair = $dataPencairan['tanggal'] ?? date('Y-m-d');
        $metode = $dataPencairan['metode'] ?? 'full';
        $tahapan = $dataPencairan['tahapan'] ?? [];
        $catatan = $dataPencairan['catatan'] ?? '';

        // Logic untuk menentukan Base Date perhitungan LPJ
        $baseDateForLpj = $tanggalCair;
        $jsonTahapan = null;

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
            // REVISI: Kembalikan posisi ke Admin (1) dengan status (1) agar Admin bisa submit LPJ?
            // Atau sesuai remote: StatusUtama = 5 (Dana Cair)
            $query = "UPDATE tbl_kegiatan 
                      SET tanggalPencairan = ?, 
                          jumlahDicairkan = ?, 
                          metodePencairan = ?, 
                          catatanBendahara = ?,
                          pencairan_tahap_json = ?,
                          statusUtamaId = 5,
                          posisiId = 1 
                      WHERE kegiatanId = ?";

            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param(
                $stmt,
                "sdsssi",
                $tanggalCair,
                $jumlah,
                $metode,
                $catatan,
                $jsonTahapan,
                $kegiatanId
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update data pencairan: " . mysqli_error($this->db));
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
                }
            } catch (Throwable $e) {
                error_log("Gagal kirim notifikasi pencairan: " . $e->getMessage());
            }
            // ----------------------------

            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("cairkanDana Error: " . $e->getMessage());
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