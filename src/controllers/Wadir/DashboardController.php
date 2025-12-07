<?php

namespace App\Controllers\Wadir;

use App\Core\Controller;
use App\Services\WadirService;

class DashboardController extends Controller
{
    private $service;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->service = new WadirService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $stats = $this->safeModelCall($this->service, 'getDashboardStats', [], []);
        $stats = [
            'total' => $stats['total'] ?? 0,
            'disetujui' => $stats['disetujui'] ?? 0,
            'menunggu' => $stats['menunggu'] ?? 0
        ];
        $list_usulan_all = $this->safeModelCall($this->service, 'getDashboardKAK', [], []);
        $selected_jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';
        $list_usulan_filtered = $list_usulan_all;
        if (!empty($selected_jurusan)) {
            $list_usulan_filtered = array_filter($list_usulan_all, function ($item) use ($selected_jurusan) {
                return strtolower($item['jurusan']) === strtolower($selected_jurusan);
            });
        }
        $items_per_page = 10;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_items = count($list_usulan_filtered);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = ($total_pages > 0) ? min($current_page, $total_pages) : 1;
        $offset = ($current_page - 1) * $items_per_page;
        $list_usulan_paginated = array_slice($list_usulan_filtered, $offset, $items_per_page);
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Wadir', 'stats' => $stats, 'list_usulan' => $list_usulan_paginated,
            'current_page' => $current_page, 'total_pages' => $total_pages,
            'jurusan_list' => $jurusan_list, 'selected_jurusan' => $selected_jurusan
        ]);
        $this->view('pages/wadir/dashboard', $data, 'wadir');
    }
}
