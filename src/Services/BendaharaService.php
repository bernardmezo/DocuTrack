<?php

namespace App\Services;

use App\Models\Bendahara\BendaharaModel;
use App\Services\WorkflowService;
use Exception;

class BendaharaService
{
    private $model;
    private WorkflowService $workflowService;

    public function __construct($db)
    {
        $this->model = new BendaharaModel($db);
        $this->workflowService = new WorkflowService($db);
    }

    public function getDashboardStatistik()
    {
        return $this->model->getDashboardStats();
    }

    public function getListKegiatanDashboard($limit = 10)
    {
        return $this->model->getListKegiatanDashboard($limit);
    }

    public function getAntrianLPJ()
    {
        return $this->model->getAntrianLPJ();
    }

    public function getAllLPJHistory()
    {
        // return $this->model->getAllLPJHistory();
        return []; // Stub
    }

    public function getRiwayatVerifikasi()
    {
        // return $this->model->getRiwayatVerifikasi();
        return []; // Stub
    }

    public function getRiwayatVerifikasiLPJ()
    {
        // return $this->model->getRiwayatVerifikasiLPJ();
        return []; // Stub
    }

    public function getRiwayatPencairanDana()
    {
        return $this->model->getRiwayatPencairanByKegiatan(0); // Stub or needs specific logic
    }

    public function getListJurusan()
    {
        return $this->model->getListJurusan();
    }

    public function getDetailLPJ($lpjId)
    {
        // return $this->model->getDetailLPJ($lpjId);
        return []; // Stub, handled by LpjService mostly or needs to be added to Model
    }

    public function getLPJItems($lpjId)
    {
        // return $this->model->getLPJItems($lpjId);
        return []; // Stub
    }

    public function approveLPJ($lpjId)
    {
        // return $this->model->approveLPJ($lpjId);
        return false; // Stub
    }

    // Pencairan Dana methods
    public function getDetailPencairan($kegiatanId)
    {
        return $this->model->getDetailPencairan($kegiatanId);
    }

    public function cairkanDana($kegiatanId, $dataPencairan)
    {
        return $this->model->cairkanDana($kegiatanId, $dataPencairan);
    }
}