<?php
namespace App\Controllers\PPK;

use App\Core\Controller;
use App\Services\PpkService;

class PengajuanKegiatanController extends Controller {
    
    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new PpkService($this->db);
    }

    public function index($data_dari_router = []) { 
        
        // 2. Ambil Semua Data Real dari DB
        $all_data = $this->safeModelCall($this->model, 'getDashboardKAK', [], []);

        // 3. Filter Data: Hanya Tampilkan yang Statusnya 'Disetujui Verifikator'
        $antrian_ppk = array_filter($all_data, function($item) {
            $posisi = isset($item['posisi']) ? strtolower((string)$item['posisi']) : '4'; 
            return $posisi === 'ppk' || $posisi === '4';
        });

        $antrian_ppk = array_values($antrian_ppk);

        // 4. Siapkan Daftar Jurusan Unik
        $jurusan_list = array_unique(array_column($antrian_ppk, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j) && $j !== '-');
        sort($jurusan_list);
        
        // 5. Kirim Data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Kegiatan',
            'list_usulan' => $antrian_ppk,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/ppk/pengajuan_kegiatan', $data, 'ppk');
    }
}
