<?php

namespace App\Controllers\Direktur;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index($data_dari_router = [])
    {

        // Data masih dummy
        $stats = [
            'total' => 255, 'disetujui' => 10, 'ditolak' => 2, 'menunggu' => 3, 'revisi' => 1
        ];
        $list_prodi = ['Teknik Informatika', 'Sistem Informasi', 'Desain Grafis', 'Manajemen'];
        $list_kak_dummy = [];
        $list_lpj_dummy = [];

        $data = array_merge($data_dari_router, [
            'title' => 'Direktur Dashboard',
            'stats' => $stats,
            'list_prodi' => $list_prodi,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/Direktur/dashboard', $data, 'direktur');
    }
}
