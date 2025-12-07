<?php
namespace App\Controllers\PPK;

use App\Core\Controller;
use App\Services\PpkService;

class RiwayatController extends Controller {
    
    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new PpkService($this->db);
    }

    public function index($data_dari_router = []) {
        
        $list_riwayat = $this->safeModelCall($this->model, 'getRiwayat', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi PPK',
            'list_riwayat' => $list_riwayat
        ]);

        $this->view('pages/ppk/riwayat_verifikasi', $data, 'ppk');
    }
}
