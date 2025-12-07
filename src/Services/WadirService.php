<?php

namespace App\Services;

use App\Models\WadirModel;
use App\Services\LogStatusService;
use Exception;
use Throwable;

class WadirService
{
    private $model;
    private $logStatusService;

    public function __construct($db)
    {
        $this->model = new WadirModel($db);
        $this->logStatusService = new LogStatusService($db);
    }

    // Explicit proxy methods to resolve "Method not found" errors
    public function getDashboardStats()
    {
        return $this->model->getDashboardStats();
    }

    public function getDashboardKAK()
    {
        return $this->model->getDashboardKAK();
    }

    public function getListJurusanDistinct()
    {
        return $this->model->getListJurusanDistinct();
    }

    public function getRiwayat()
    {
        return $this->model->getRiwayat();
    }

    /**
     * Menyetujui usulan dan mengirim notifikasi.
     *
     * @param int $kegiatanId
     * @param string $rekomendasi
     * @return bool
     */
    public function approveUsulan($kegiatanId, $rekomendasi = '')
    {
        $result = $this->model->approveUsulan($kegiatanId, $rekomendasi);

        if ($result) {
            try {
                // Ambil detail kegiatan untuk mendapatkan userId pengusul
                $kegiatan = $this->model->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'APPROVAL',
                        "Usulan kegiatan \"{$kegiatan['namaKegiatan']}\" telah disetujui oleh Wakil Direktur.",
                        $kegiatanId,
                        'APPROVAL',
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                // Jangan gagalkan proses approval hanya karena notifikasi gagal
                error_log("Gagal membuat notifikasi approval Wadir untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }

        return $result;
    }

    // Fallback for any other methods not explicitly defined
    public function __call($name, $arguments)
    {
        if (method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new Exception("Method {$name} not found in WadirModel or WadirService");
    }
}