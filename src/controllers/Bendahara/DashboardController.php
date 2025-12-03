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
        
        // ✅ AMBIL DATA DARI DATABASE dengan error handling
        $stats = $this->safeModelCall($this->model, 'getDashboardStatistik', [], [
            'total' => 0,
            'danaDiberikan' => 0,
            'ditolak' => 0,
            'menunggu' => 0
        ]);
        
        $list_kak = $this->safeModelCall($this->model, 'getListKegiatanDashboard', [10], []);
        $list_lpj = $this->safeModelCall($this->model, 'getAntrianLPJ', [], []);
        
        // Get flash messages from session
        $success_msg = $_SESSION['flash_message'] ?? null;
        $error_msg = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);
        
        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_kak' => $list_kak ?? [],
            'list_lpj' => $list_lpj ?? [],
            'success_message' => $success_msg,
            'error_message' => $error_msg
        ]);
        
        $this->view('pages/bendahara/dashboard', $data, 'bendahara'); 
    }
}