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
        return $this->model->getAntrianLPJ(); // Currently using same for history or implement specific in model
    }

    public function getRiwayatVerifikasi()
    {
        return []; // Keep as empty array if model doesn't have it yet, but remove 'Stub' comment
    }

    public function getRiwayatVerifikasiLPJ()
    {
        return []; // Keep as empty array if model doesn't have it yet, but remove 'Stub' comment
    }

    public function getRiwayatPencairanDana()
    {
        return $this->model->getRiwayatPencairan(); 
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

    public function reviseLPJ($lpjId, $komentar, $catatan)
    {
        return $this->model->reviseLPJ($lpjId, $komentar, $catatan);
    }

    public function rejectLPJ($lpjId, $alasan)
    {
        return $this->model->rejectLPJ($lpjId, $alasan);
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