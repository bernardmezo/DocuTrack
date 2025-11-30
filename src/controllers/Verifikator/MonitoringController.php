<?php
// File: src/controllers/Verifikator/MonitoringController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php';

class VerifikatorMonitoringController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new VerifikatorModel();
    }
    
    /**
     * Menampilkan halaman Monitoring Progres Proposal.
     */
    public function index($data_dari_router = []) { 
        
        // Definisikan tahapan universal
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        
        // ✅ AMBIL DATA DARI DATABASE
        $list_proposal = $this->model->getProposalMonitoring();

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $list_proposal,
            'tahapan_all' => $tahapan_all
        ]);

        $this->view('pages/verifikator/monitoring', $data, 'verifikator'); 
    }
}