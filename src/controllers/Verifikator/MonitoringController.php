<?php

namespace App\Controllers\Verifikator;

use App\Core\Controller;
use App\Services\VerifikatorService;

class MonitoringController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new VerifikatorService($this->db);
    }

    public function index($data_dari_router = [])
    {

        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];

        $list_proposal = $this->safeModelCall($this->model, 'getProposalMonitoring', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $list_proposal,
            'tahapan_all' => $tahapan_all
        ]);

        $this->view('pages/verifikator/monitoring', $data, 'verifikator');
    }
}
