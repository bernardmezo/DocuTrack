<?php

namespace App\Services;

use App\Models\kegiatan\KegiatanModel;
use App\Models\PpkModel;
use App\Services\LogStatusService;
use App\Services\ValidationService;
use App\Services\WorkflowService;
use Exception;
use Throwable;

class PpkService
{
    private PpkModel $ppkModel;
    private LogStatusService $logStatusService;
    private ValidationService $validationService;
    private KegiatanModel $kegiatanModel;
    private WorkflowService $workflowService;

    public function __construct(
        $ppkModelOrDb,
        $logStatusService = null,
        $validationService = null,
        $kegiatanModel = null
    ) {
        if ($ppkModelOrDb instanceof PpkModel) {
            $this->ppkModel = $ppkModelOrDb;
            $this->logStatusService = $logStatusService;
            $this->validationService = $validationService;
            $this->kegiatanModel = $kegiatanModel;
            
            // Get database from model for WorkflowService
            $db = $this->ppkModel->db ?? db();
            $this->workflowService = new WorkflowService($db);
        } else {
             // Assume $ppkModelOrDb is $db
            $db = $ppkModelOrDb;
            $this->ppkModel = new PpkModel($db);
            $this->logStatusService = new LogStatusService($db);
            $this->validationService = new ValidationService();
            $this->kegiatanModel = new KegiatanModel($db);
            $this->workflowService = new WorkflowService($db);
        }
    }

    public function getDashboardStats()
    {
        return $this->ppkModel->getDashboardStats();
    }

    public function getDashboardKAK()
    {
        return $this->ppkModel->getDashboardKAK();
    }

    public function getDetailKegiatan($kegiatanId)
    {
        return $this->ppkModel->getDetailKegiatan($kegiatanId);
    }

    public function getIndikatorByKAK($kakId)
    {
        return $this->ppkModel->getIndikatorByKAK($kakId);
    }

    public function getTahapanByKAK($kakId)
    {
        return $this->ppkModel->getTahapanByKAK($kakId);
    }

    public function getRABByKAK($kakId)
    {
        return $this->ppkModel->getRABByKAK($kakId);
    }

    public function getRiwayat()
    {
        return $this->ppkModel->getRiwayat();
    }

    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter)
    {
        return $this->ppkModel->getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter);
    }

    public function getListJurusanDistinct()
    {
        return $this->ppkModel->getListJurusanDistinct();
    }

    /**
     * Menyetujui usulan sebagai PPK dan memicu notifikasi.
     */
    public function approveUsulan(int $kegiatanId, string $rekomendasi = ''): bool
    {
        $this->validationService->validate(['kegiatan_id' => $kegiatanId], ['kegiatan_id' => 'required|numeric']);

        $result = $this->ppkModel->approveUsulan($kegiatanId, $rekomendasi);

        if ($result) {
            try {
                $kegiatan = $this->ppkModel->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'APPROVAL',
                        "Proposal kegiatan \"{$kegiatan['namaKegiatan']}\" Anda telah disetujui oleh PPK.",
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                error_log("Gagal membuat notifikasi persetujuan PPK untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }

        return $result;
    }
}
