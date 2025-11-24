<?php
// File: src/controllers/Super_Admin/MonitoringController.php

require_once '../src/core/Controller.php';

class SuperadminMonitoringController extends Controller {
    
    // --- Data Dummy Master (Simulasi Database) ---
    private $all_proposals = [
        ['id' => 1, 'nama' => 'Seminar Nasional: Inovasi teknologi', 'pengusul' => 'Putra (NIM)', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 2, 'nama' => 'Seminar BEM: Education', 'pengusul' => 'Yopan (NIM)', 'jurusan' => 'Teknik Elektro', 'status' => 'Ditolak', 'tahap_sekarang' => 'ACC PPK'],
        ['id' => 3, 'nama' => 'Kulum: Education', 'pengusul' => 'Bernadya (NIM)', 'jurusan' => 'Teknik Mesin', 'status' => 'In Process', 'tahap_sekarang' => 'ACC WD'],
        ['id' => 4, 'nama' => 'Seminar Himatik', 'pengusul' => 'Fidel (NIM)', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
        ['id' => 5, 'nama' => 'Disnatalis', 'pengusul' => 'Anton (NIM)', 'jurusan' => 'Teknik Sipil', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 6, 'nama' => 'Seminar Expektik', 'pengusul' => 'Bambang (NIM)', 'jurusan' => 'Teknik Elektro', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 7, 'nama' => 'Lomba Desain', 'pengusul' => 'Citra (NIM)', 'jurusan' => 'Akuntansi', 'status' => 'In Process', 'tahap_sekarang' => 'Verifikasi'],
        ['id' => 8, 'nama' => 'Hackathon 2025', 'pengusul' => 'Deni (NIM)', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
        ['id' => 9, 'nama' => 'Workshop IoT', 'pengusul' => 'Eka (NIM)', 'jurusan' => 'Teknik Elektro', 'status' => 'In Process', 'tahap_sekarang' => 'ACC WD'],
        ['id' => 10, 'nama' => 'Seminar Kewirausahaan', 'pengusul' => 'Fajar (NIM)', 'jurusan' => 'Administrasi Bisnis', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 11, 'nama' => 'Kompetisi Robotik', 'pengusul' => 'Gita (NIM)', 'jurusan' => 'Teknik Mesin', 'status' => 'In Process', 'tahap_sekarang' => 'Verifikasi'],
        ['id' => 12, 'nama' => 'Expo Konstruksi', 'pengusul' => 'Hadi (NIM)', 'jurusan' => 'Teknik Sipil', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
    ];
    
    // Daftar Jurusan di PNJ
    private $list_jurusan = [
        'Teknik Informatika dan Komputer',
        'Teknik Elektro',
        'Teknik Mesin',
        'Teknik Sipil',
        'Akuntansi',
        'Administrasi Bisnis',
    ];
    
    /**
     * Method INDEX - Server-side Processing (Tanpa JSON)
     */
    public function index($data_dari_router = []) { 
        // Ambil parameter filter dari URL
        $page = (int)($_GET['page'] ?? 1);
        $status_filter = strtolower($_GET['status'] ?? 'semua');
        $jurusan_filter = $_GET['jurusan'] ?? 'semua';
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5;

        // --- Filter Data ---
        $filtered_data = array_filter($this->all_proposals, function($item) use ($status_filter, $jurusan_filter, $search_text) {
            $status_match = ($status_filter === 'semua') || (strtolower($item['status']) === $status_filter);
            $jurusan_match = ($jurusan_filter === 'semua') || ($item['jurusan'] === $jurusan_filter);
            $search_match = empty($search_text) || str_contains(strtolower($item['nama']), $search_text);
            return $status_match && $jurusan_match && $search_match;
        });

        // --- Pagination ---
        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);
        $page = max(1, min($page, $total_pages ?: 1)); // Batasi halaman valid
        
        $offset = ($page - 1) * $per_page;
        $paginated_items = array_slice($filtered_data, $offset, $per_page);

        // --- Pagination Info ---
        $showing_from = $total_items > 0 ? $offset + 1 : 0;
        $showing_to = $total_items > 0 ? $offset + count($paginated_items) : 0;

        // --- Kirim data ke View ---
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $paginated_items,
            'list_jurusan' => $this->list_jurusan,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_items' => $total_items,
                'per_page' => $per_page,
                'showing_from' => $showing_from,
                'showing_to' => $showing_to
            ],
            'filters' => [
                'status' => $_GET['status'] ?? 'Semua',
                'jurusan' => $_GET['jurusan'] ?? 'semua',
                'search' => $_GET['search'] ?? ''
            ]
        ]);
        
        $this->view('pages/Super_Admin/monitoring', $data, 'super_admin'); 
    }
}