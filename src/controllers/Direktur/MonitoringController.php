<?php
namespace App\Controllers\Direktur;

use App\Core\Controller;

class MonitoringController extends Controller {
    
    // Data masih dummy
    private $all_proposals = [
        ['id' => 1, 'nama' => 'Seminar Nasional: Inovasi teknologi', 'pengusul' => 'Putra (NIM)', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 2, 'nama' => 'Seminar BEM: Education', 'pengusul' => 'Yopan (NIM)', 'jurusan' => 'Teknik Elektro', 'status' => 'Ditolak', 'tahap_sekarang' => 'ACC PPK'],
    ];
    private $list_jurusan = [
        'Teknik Informatika dan Komputer', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Akuntansi', 'Administrasi Bisnis',
    ];
    
    public function index($data_dari_router = []) { 
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $this->all_proposals,
            'list_jurusan' => $this->list_jurusan,
            'pagination' => ['current_page' => 1, 'total_pages' => 1, 'total_items' => count($this->all_proposals)],
            'filters' => []
        ]);
        
        $this->view('pages/Direktur/monitoring', $data, 'direktur'); 
    }
}