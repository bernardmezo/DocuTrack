<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\KegiatanService;

class PengajuanKegiatanController extends Controller
{
    private $kegiatanService;

    public function __construct()
    {
        parent::__construct();
        $this->kegiatanService = new KegiatanService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $list_kegiatan_disetujui = $this->kegiatanService->getKegiatanByStatus(1, 3);

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => $list_kegiatan_disetujui
        ]);

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    public function show($id, $data_dari_router = [])
    {
        $kegiatanDB = $this->kegiatanService->getDetailLengkap($id);

        if (!$kegiatanDB) {
            $this->view('pages/errors/404');
            return;
        }

        $data = [
             'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
             'kegiatan_data' => $kegiatanDB,
             //... mapping data lainnya ...
        ];

        if (($_GET['mode'] ?? '') === 'rincian') {
            $this->view('pages/admin/detail_kegiatan', $data, 'app');
        } else {
            $this->view('pages/admin/detail_kak', $data, 'app');
        }
    }
}
