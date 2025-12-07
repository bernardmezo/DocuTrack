<?php

namespace App\Services;

use App\Models\BendaharaModel;
use Exception;

class BendaharaService
{
    private $model;

    public function __construct($db)
    {
        $this->model = new BendaharaModel($db);
    }

    public function getDashboardStatistik()
    {
        return $this->model->getDashboardStatistik();
    }

    public function getListKegiatanDashboard($limit = 10)
    {
        return $this->model->getListKegiatanDashboard($limit);
    }

    public function getAntrianLPJ()
    {
        return $this->model->getAntrianLPJ();
    }

    public function getRiwayatVerifikasi()
    {
        return $this->model->getRiwayatVerifikasi();
    }

    public function getListJurusan()
    {
        return $this->model->getListJurusan();
    }

    // You can add other methods from BendaharaModel that are used by the service here.
    // For now, only adding the ones identified in the error log.
    // If other methods from BendaharaModel are intended to be called directly
    // through the service (e.g., from a controller), they should also be explicitly
    // defined here.

    // Example of how to add more methods, if needed:
    // public function getDetailPencairan($kegiatanId) {
    //     return $this->model->getDetailPencairan($kegiatanId);
    // }
    // public function cairkanDana($kegiatanId, $dataPencairan) {
    //     return $this->model->cairkanDana($kegiatanId, $dataPencairan);
    // }
}
