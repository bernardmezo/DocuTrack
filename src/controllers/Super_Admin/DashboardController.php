<?php
// File: src/controllers/Super_Admin/DashboardController.php

require_once '../src/core/Controller.php';
require_once '../src/model/superAdminModel.php';

class SuperadminDashboardController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new superAdminModel();
    }
    
    public function index($data_dari_router = []) {
        
        // ✅ AMBIL DATA DARI DATABASE (bukan dummy)
        $stats = $this->model->getDashboardStats();
        $list_prodi = $this->model->getListProdi();
        $list_kak = $this->model->getListKegiatan(20);
        $list_lpj = $this->model->getListLPJ(10);

        $data = array_merge($data_dari_router, [
            'title' => 'Super Admin Dashboard',
            'stats' => $stats,
            'list_prodi' => $list_prodi,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj
        ]);

        $this->view('pages/Super_Admin/dashboard', $data, 'super_admin'); 
    }
}