<?php
// File: src/controllers/Bendahara/DashboardController.php
require_once '../src/core/Controller.php';
require_once '../src/model/bendaharaModel.php';

class BendaharaDashboardController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new bendaharaModel();
    }
    
    public function index($data_dari_router = []) {
        
        // ✅ AMBIL DATA DARI DATABASE (bukan dummy)
        $stats = $this->model->getDashboardStatistik();
        $list_kak = $this->model->getListKegiatanDashboard(10);
        $list_lpj = $this->model->getAntrianLPJ();
        
        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj
        ]);
        
        $this->view('pages/bendahara/dashboard', $data, 'bendahara'); 
    }
}