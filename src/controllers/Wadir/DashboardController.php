<?php
// File: src/controllers/Wadir/DashboardController.php

require_once '../src/core/Controller.php';
require_once '../src/model/wadirModel.php'; // Load Model Baru

class WadirDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Panggil Model
        $model = new wadirModel($this->db);

        // 2. Ambil Data Real
        $stats = $model->getDashboardStats();

        $stats = [
            'total' => $stats['total'],
            'disetujui'=> $stats['disetujui'],
            'menunggu'=> $stats['menunggu']
        ];

        $list_usulan_all = $model->getDashboardKAK(); // Hanya ambil yg posisiId = 3

        // 3. Filter Jurusan
        $selected_jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';
        $list_usulan_filtered = $list_usulan_all;

        if (!empty($selected_jurusan)) {
            $list_usulan_filtered = array_filter($list_usulan_all, function($item) use ($selected_jurusan) {
                return strtolower($item['jurusan']) === strtolower($selected_jurusan);
            });
        }

        // 4. Pagination
        $items_per_page = 10;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_items = count($list_usulan_filtered);
        $total_pages = ceil($total_items / $items_per_page);
        
        if ($total_pages > 0) $current_page = min($current_page, $total_pages);
        
        $offset = ($current_page - 1) * $items_per_page;
        $list_usulan_paginated = array_slice($list_usulan_filtered, $offset, $items_per_page);

        // 5. Dropdown Jurusan
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Wadir',
            'stats' => $stats,
            'list_usulan' => $list_usulan_paginated,
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'jurusan_list' => $jurusan_list,
            'selected_jurusan' => $selected_jurusan
        ]);

        $this->view('pages/wadir/dashboard', $data, 'wadir'); 
    }
}