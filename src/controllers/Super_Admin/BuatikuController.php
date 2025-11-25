<?php
// File: src/controllers/Super_Admin/BuatikuController.php

require_once '../src/core/Controller.php';

class SuperadminBuatikuController extends Controller {
    
    // --- Data Dummy IKU (Sesuai Gambar) ---
    private $all_iku = [
        ['id' => 1, 'nama' => 'Mendapat Pekerjaan', 'deskripsi' => 'Lulusan berhasil mendapat pekerjaan'],
        ['id' => 2, 'nama' => 'Melanjutkan Studi', 'deskripsi' => 'Lulusan melanjutkan studi ke jenjang lebih tinggi'],
        ['id' => 3, 'nama' => 'Menjadi Wiraswasta', 'deskripsi' => 'Lulusan membuka usaha sendiri'],
        ['id' => 4, 'nama' => 'Menjalankan kegiatan pembelajaran di luar program studi', 'deskripsi' => 'Mahasiswa mengambil SKS di luar prodi'],
        ['id' => 5, 'nama' => 'Dosen berkegiatan di luar kampus', 'deskripsi' => 'Dosen praktisi atau magang industri'],
        ['id' => 6, 'nama' => 'Praktisi mengajar di dalam kampus', 'deskripsi' => 'Kelas kolaborasi dengan praktisi'],
        ['id' => 7, 'nama' => 'Hasil kerja dosen digunakan oleh masyarakat', 'deskripsi' => 'Pengabdian masyarakat atau paten'],
        ['id' => 8, 'nama' => 'Program studi bekerjasama dengan mitra kelas dunia', 'deskripsi' => 'Kerjasama internasional'],
    ];
    
    public function index() { 
        // 1. Ambil Parameter
        $page = (int)($_GET['page'] ?? 1);
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5; // Sesuai gambar "Showing 1 to 5"

        // 2. Logika Filter (Search)
        $filtered_data = array_filter($this->all_iku, function($item) use ($search_text) {
            if (empty($search_text)) return true;
            return str_contains(strtolower($item['nama']), $search_text);
        });

        // 3. Pagination Logic
        $filtered_data = array_values($filtered_data); // Reset key
        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);
        
        // Prevent page number out of range
        if ($page < 1) $page = 1;
        if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

        $offset = ($page - 1) * $per_page;
        $display_data = array_slice($filtered_data, $offset, $per_page);

        // 4. Data Passing ke View
        $data = [
            'title' => 'Buat IKU',
            'list_iku' => $display_data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ($total_pages > 0) ? $total_pages : 1,
                'total_items' => $total_items,
                'showing_from' => ($total_items > 0) ? $offset + 1 : 0,
                'showing_to' => ($total_items > 0) ? min($offset + count($display_data), $total_items) : 0,
                'per_page' => $per_page
            ],
            'filters' => [
                'search' => $_GET['search'] ?? ''
            ]
        ];

        // Panggil View
        $this->view('pages/Super_Admin/buat-iku', $data, 'super_admin'); 
    }
}