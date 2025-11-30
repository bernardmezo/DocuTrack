<?php
// File: src/controllers/ppk/DashboardController.php

require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php'; // Load Model

class PPKDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Panggil Model
        $model = new ppkModel();

        // 2. Ambil Data Real dari Database
        $stats = $model->getDashboardStats();
        $list_usulan_all = $model->getDashboardKAK();

        // 3. LOGIKA FILTER JURUSAN (Tetap dipertahankan di sisi Controller/PHP)
        $selected_jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';
        
        // Array penampung data yang sudah difilter
        $list_usulan_filtered = $list_usulan_all;

        // Jika ada filter jurusan, lakukan penyaringan
        if (!empty($selected_jurusan)) {
            $list_usulan_filtered = array_filter($list_usulan_all, function($item) use ($selected_jurusan) {
                // Gunakan strtolower agar pencarian tidak case-sensitive
                return strtolower($item['jurusan']) === strtolower($selected_jurusan);
            });
        }

        // 4. LOGIKA PAGINATION
        $items_per_page = 10;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_items = count($list_usulan_filtered);
        $total_pages = ceil($total_items / $items_per_page);
        
        if ($total_pages > 0) {
            $current_page = min($current_page, $total_pages);
        } else {
             $current_page = 1; // Reset ke 1 jika data kosong
        }
        
        $offset = ($current_page - 1) * $items_per_page;
        $list_usulan_paginated = array_slice($list_usulan_filtered, $offset, $items_per_page);

        // 5. DAFTAR JURUSAN UNTUK DROPDOWN
        // Ambil unique 'jurusan' dari data asli untuk opsi filter
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        // Hapus nilai kosong/dash jika ada
        $jurusan_list = array_filter($jurusan_list, fn($j) => $j !== '-' && !empty($j));
        sort($jurusan_list);

        // 6. Kirim Data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard PPK',
            'stats' => $stats, // Statistik Real
            'list_usulan' => $list_usulan_paginated, // Data Tabel Real
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'jurusan_list' => $jurusan_list,
            'selected_jurusan' => $selected_jurusan
        ]);

        $this->view('pages/ppk/dashboard', $data, 'ppk'); 
    }
}