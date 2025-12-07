<?php
namespace App\Controllers\Wadir;

use App\Core\Controller;
use App\Services\WadirService;

class RiwayatController extends Controller {
    
    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new WadirService($this->db);
    }

    public function index($data_dari_router = []) { 
        
        $list_riwayat = $this->safeModelCall($this->model, 'getRiwayat', [], []);

        $jurusan_list = array_unique(array_column($list_riwayat, 'prodi'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Persetujuan Wadir',
            'list_riwayat' => $list_riwayat,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/wadir/riwayat_verifikasi', $data, 'wadir');
    }
}
