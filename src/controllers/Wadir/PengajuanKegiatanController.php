<?php

namespace App\Controllers\Wadir;

use App\Core\Controller;
use App\Services\WadirService;

class PengajuanKegiatanController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new WadirService($this->db);
    }

    public function index($data_dari_router = [])
    {

        $list_usulan_all = $this->safeModelCall($this->model, 'getDashboardKAK', [], []);

        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Wadir',
            'list_usulan' => $list_usulan_all,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/wadir/pengajuan_kegiatan', $data, 'wadir');
    }
}
