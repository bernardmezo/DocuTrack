<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\LpjService;
use App\Services\KegiatanService;

class DashboardController extends Controller
{
    private $lpjService;
    private $kegiatanService;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->lpjService = new LpjService($this->db);
        $this->kegiatanService = new KegiatanService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $stats = $this->kegiatanService->getDashboardStats();
        $jurusan = $_SESSION['user_jurusan'] ?? null;
        $list_kak = $this->kegiatanService->getDashboardKAK($jurusan);
        $list_lpj = $this->lpjService->getDashboardLPJ();
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC PPK', 'ACC WD', 'Dana Cair'];
        $tahap_sekarang_kak = 'Pengajuan';
        $icons_kak = [
            'Pengajuan' => 'fa-file-alt', 'Validasi' => 'fa-check-double', 'ACC PPK' => 'fa-stamp',
            'ACC WD' => 'fa-user-check', 'Dana Cair' => 'fa-wallet'
        ];
        $tahapan_lpj = ['Upload Bukti', 'Validasi', 'ACC Bendahara', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi';
        $icons_lpj = [
            'Upload Bukti' => 'fa-upload', 'Validasi' => 'fa-check-double',
            'ACC Bendahara' => 'fa-file-invoice-dollar', 'Selesai' => 'fa-flag-checkered'
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'tahapan_kak' => $tahapan_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,
            'icons_kak' => $icons_kak,
            'tahapan_lpj' => $tahapan_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj,
            'icons_lpj' => $icons_lpj,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj
        ]);

        $this->view('pages/admin/dashboard', $data, 'app');
    }
}
