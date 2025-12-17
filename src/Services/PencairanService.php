<?php

namespace App\Services;

use App\Models\Bendahara\BendaharaModel;
use App\Services\LogStatusService;
use Exception;
use DateTime;
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
     * Get Pencairan total Dana yang sudah dicairkan by Kegiatan ID
     */
    public function getTotalDicairkanByKegiatan($id)
    {
        return $this->bendaharaModel->getTotalDicairkanByKegiatan($id);
    }

    /**
     * Get Riwayat Pencairan by Kegiatan ID
     */
    public function getRiwayatPencairanByKegiatan($id)
    {
        return $this->bendaharaModel->getRiwayatPencairanByKegiatan($id);
    }

    /**
     * Proses Pencairan Dana Bertahap (Ported from Dev)
     * 
     * @param int $kegiatanId ID kegiatan
     * @param array $dataPencairan [
     *   'jumlah' => float (total anggaran),
     *   'tanggal' => string (tanggal pencairan pertama),
     *   'metode' => string ('bertahap' | 'penuh'),
     *   'tahapan' => array [
     *     ['tanggal' => 'Y-m-d', 'termin' => 'string', 'nominal' => float],
     *     ...
     *   ],
     *   'catatan' => string (optional)
     * ]
     * @return bool
     * @throws Exception
     */
    public function cairkanDana($kegiatanId, $dataPencairan)
    {
        $metode = $dataPencairan['metode'] ?? 'penuh';
        $catatan = $dataPencairan['catatan'] ?? '';
        $userId = $_SESSION['user_id'] ?? 0;

        mysqli_begin_transaction($this->db);
        
        try {
            if ($metode === 'bertahap') {
                // PENCAIRAN BERTAHAP - Handle multiple termin
                $tahapan = $dataPencairan['tahapan'] ?? [];
                
                $totalAnggaran = $dataPencairan['jumlah'] ?? 0; // total anggaran yang harus dicairkan (dari RAB)
                $totalDicairkan = 0;
                
                // Validasi dan simpan setiap termin
                foreach ($tahapan as $index => $tahap) {
                    $tanggal = $tahap['tanggal'] ?? null;
                    $termin = $tahap['termin'] ?? "Termin " . ($index + 1);
                    $nominal = floatval($tahap['nominal'] ?? 0);
                    
                    if (!$tanggal || $nominal <= 0) {
                        throw new Exception("Data tahap " . ($index + 1) . " tidak valid");
                    }
                    
                    $totalDicairkan += $nominal;
                    
                    // Simpan ke tbl_pencairan_dana (via Model)
                    $pencairanData = [
                        'kegiatan_id' => $kegiatanId,
                        'tanggal_pencairan' => $tanggal,
                        'termin' => $termin,
                        'nominal' => $nominal,
                        'catatan' => $catatan,
                        'created_by' => $userId
                    ];
                    
                    if (!$this->bendaharaModel->simpanPencairanDana($pencairanData)) {
                        throw new Exception("Gagal menyimpan data pencairan termin " . ($index + 1));
                    }
                }
                
                // Update status pencairan di tbl_kegiatan (Update total dicairkan dan status)
                // Jika totalDicairkan < totalAnggaran, status belum lunas (6)
                // Jika totalDicairkan >= totalAnggaran, status lunas/cair (5)
                if (!$this->bendaharaModel->updateStatusPencairan($kegiatanId, $totalDicairkan, $totalAnggaran)) {
                    throw new Exception("Gagal update status pencairan");
                }
                
                // Update metadata kegiatan (tanggal pencairan pertama, metode)
                $tanggalPertama = $tahapan[0]['tanggal'];
                $queryUpdate = "UPDATE tbl_kegiatan 
                            SET tanggalPencairan = ?,
                                metodePencairan = 'bertahap',
                                catatanBendahara = ?,
                                posisiId = 1
                            WHERE kegiatanId = ?";
                
                $stmt = mysqli_prepare($this->db, $queryUpdate);
                mysqli_stmt_bind_param($stmt, "ssi", $tanggalPertama, $catatan, $kegiatanId);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal update data kegiatan");
                }
                mysqli_stmt_close($stmt);
                
                // Hitung tenggat LPJ dari tanggal termin terakhir
                $tanggalTerakhir = end($tahapan)['tanggal'];
                $tenggatLpj = $this->calculateLpjDeadline($tanggalTerakhir);
                
                if (!$this->createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)) {
                    throw new Exception("Gagal set tenggat LPJ");
                }
                
            } else {
                // PENCAIRAN PENUH
                $jumlah = $dataPencairan['jumlah'] ?? 0;
                $tanggalCair = $dataPencairan['tanggal'] ?? date('Y-m-d');
                
                // Update tbl_kegiatan directly
                $query = "UPDATE tbl_kegiatan 
                          SET tanggalPencairan = ?, 
                              jumlahDicairkan = ?, 
                              metodePencairan = 'penuh', 
                              catatanBendahara = ?,
                              statusUtamaId = 5,
                              statusPencairanId = 5,
                              posisiId = 1 
                          WHERE kegiatanId = ?";

                $stmt = mysqli_prepare($this->db, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $tanggalCair, $jumlah, $catatan, $kegiatanId);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal memproses pencairan penuh");
                }
                mysqli_stmt_close($stmt);

                // Hitung tenggat LPJ
                $tenggatLpj = $this->calculateLpjDeadline($tanggalCair);
                if (!$this->createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)) {
                     throw new Exception("Gagal set tenggat LPJ");
                }
            }
            
            // Log History (Unified)
            $statusDisetujui = 3; // Status Disetujui (Internal History) or 5 (Cair)? Using 3 per Dev code.
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
            throw $e;
        }
    }

    /**
     * Hitung Tenggat LPJ (14 Hari Kerja setelah tanggal tertentu)
     * 
     * @param string $startDate Format Y-m-d
     * @return string Format Y-m-d
     */
    private function calculateLpjDeadline($startDate)
    {
        $date = new DateTime($startDate);
        $workingDaysToAdd = 14;

        while ($workingDaysToAdd > 0) {
            $date->modify('+1 day');
            $dayOfWeek = (int) $date->format('N'); // 1=Monday, 7=Sunday
            
            // Hitung hanya hari kerja (Senin-Jumat)
            if ($dayOfWeek <= 5) {
                $workingDaysToAdd--;
            }
        }

        return $date->format('Y-m-d');
    }

    /**
     * Membuat atau update row LPJ saat dana dicairkan
     * 
     * @param int $kegiatanId
     * @param string $tenggatLpj
     * @return bool
     */
    private function createOrUpdateLpjPlaceholder($kegiatanId, $tenggatLpj)
    {
        // Cek apakah sudah ada LPJ untuk kegiatan ini
        $checkQuery = "SELECT lpjId FROM tbl_lpj WHERE kegiatanId = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $checkQuery);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($existing) {
            // Update existing LPJ
            $updateQuery = "UPDATE tbl_lpj SET tenggatLpj = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $tenggatLpj, $kegiatanId);
            return mysqli_stmt_execute($stmt);
        }

        // Insert new LPJ
        $insertQuery = "INSERT INTO tbl_lpj (kegiatanId, tenggatLpj) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $insertQuery);
        mysqli_stmt_bind_param($stmt, "is", $kegiatanId, $tenggatLpj);
        return mysqli_stmt_execute($stmt);
    }
}