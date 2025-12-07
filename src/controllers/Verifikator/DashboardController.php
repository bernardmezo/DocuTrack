<?php
namespace App\Controllers\Verifikator;

use App\Core\Controller;
use App\Services\VerifikatorService;

class DashboardController extends Controller {
    
    private $service;

    public function __construct($db) {
        parent::__construct($db);
        $this->service = new VerifikatorService($this->db);
    }
    
    public function index($data_dari_router = []) {
        $stats = $this->safeModelCall($this->service, 'getDashboardStats', [], []);
        $list_usulan = $this->safeModelCall($this->service, 'getDashboardKAK', [], []);
        $stats = [
            'total' => $stats['total'] ?? 0,
            'disetujui' => $stats['disetujui'] ?? 0,
            'ditolak' => $stats['ditolak'] ?? 0,
            'pending' => $stats['pending'] ?? 0
        ];
        $jurusan_list = $this->safeModelCall($this->service, 'getListJurusan', [], []);
        if (is_array($jurusan_list) && !empty($jurusan_list)) {
            $jurusan_list = array_map(fn($r) => $r['namaJurusan'] ?? null, $jurusan_list);
            $jurusan_list = array_values(array_filter(array_unique($jurusan_list)));
            sort($jurusan_list);
        } else {
            $jurusan_list = array_values(array_unique(array_column($list_usulan, 'jurusan')));
            sort($jurusan_list);
        }
        
        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Verifikator', 'stats' => $stats, 'list_usulan' => $list_usulan,
            'jurusan_list' => $jurusan_list, 'current_page' => 1, 'total_pages' => 1
        ]);

        $this->view('pages/verifikator/dashboard', $data, 'verifikator');
    }
}