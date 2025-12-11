<?php

namespace App\Controllers\Verifikator;

use App\Core\Controller;
use App\Services\VerifikatorService;

class RiwayatController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new VerifikatorService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $userJurusan = $_SESSION['user_jurusan'] ?? null;
        $list_riwayat = $this->safeModelCall($this->model, 'getRiwayat', [$userJurusan], []);

        $jurusan_list = array_unique(array_column($list_riwayat, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/verifikator/riwayat_verifikasi', $data, 'verifikator');
    }
}
