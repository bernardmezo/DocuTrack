<?php

namespace App\Services;

use App\Models\kegiatan\KegiatanModel;
use App\Models\PpkModel;
use App\Services\LogStatusService;
use App\Services\ValidationService;
use Exception;
use Throwable;

class PpkService
{
    private PpkModel $ppkModel;
    private LogStatusService $logStatusService;
    private ValidationService $validationService;
    private KegiatanModel $kegiatanModel;

    public function __construct(
        PpkModel $ppkModel,
        LogStatusService $logStatusService,
        ValidationService $validationService,
        KegiatanModel $kegiatanModel
    ) {
        $this->ppkModel = $ppkModel;
        $this->logStatusService = $logStatusService;
        $this->validationService = $validationService;
        $this->kegiatanModel = $kegiatanModel;
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
