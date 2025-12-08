<?php

namespace App\Services;

use App\Models\BendaharaModel;
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

    public function getAllLPJHistory()
    {
        return $this->model->getAllLPJHistory();
    }

    public function getRiwayatVerifikasi()
    {
        return $this->model->getRiwayatVerifikasi();
    }

    public function getRiwayatVerifikasiLPJ()
    {
        return $this->model->getRiwayatVerifikasiLPJ();
    }

    public function getRiwayatPencairanDana()
    {
        return $this->model->getRiwayatPencairanDana();
    }

    public function getListJurusan()
    {
        return $this->model->getListJurusan();
    }

    public function getDetailLPJ($lpjId)
    {
        return $this->model->getDetailLPJ($lpjId);
    }

    public function getLPJItems($lpjId)
    {
        return $this->model->getLPJItems($lpjId);
    }

    public function approveLPJ($lpjId)
    {
        return $this->model->approveLPJ($lpjId);
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
