<?php
// File: src/controllers/PPK/MonitoringController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; 

class PPKMonitoringController extends Controller {
    
    // --- Data Dummy Master (Simulasi Database) ---
    private $all_proposals = [
        ['id' => 1, 'nama' => 'Seminar Nasional: Inovasi teknologi', 'pengusul' => 'Putra (NIM), Prodi', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 2, 'nama' => 'Seminar BEM: Education', 'pengusul' => 'Yopan (NIM), Prodi', 'status' => 'Ditolak', 'tahap_sekarang' => 'ACC PPK'],
        ['id' => 3, 'nama' => 'Kulum: Education', 'pengusul' => 'Bernadya (NIM), Prodi', 'status' => 'In Process', 'tahap_sekarang' => 'ACC WD'],
        ['id' => 4, 'nama' => 'Seminar Himatik', 'pengusul' => 'Fidel (NIM), Prodi', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
        ['id' => 5, 'nama' => 'Disnatalis', 'pengusul' => 'Anton(NIM), Prodi', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 6, 'nama' => 'Seminar Expektik', 'pengusul' => 'Bambang (NIM), Prodi', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 7, 'nama' => 'Lomba Desain', 'pengusul' => 'Citra (NIM), Prodi', 'status' => 'In Process', 'tahap_sekarang' => 'Verifikasi'],
        ['id' => 8, 'nama' => 'Hackathon 2025', 'pengusul' => 'Deni (NIM), Prodi', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
    ];
    
    /**
     * 1. Method INDEX
     * Hanya menampilkan "cangkang" HTML. Data akan dimuat oleh JavaScript.
     */
    public function index($data_dari_router = []) { 
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal'
            // Tidak perlu mengirim list_proposal lagi
        ]);
        $this->view('pages/ppk/monitoring', $data, 'ppk');
    }

    /**
     * 2. Method GET DATA (API)
     * Dipanggil oleh JavaScript (fetch) untuk mendapatkan data JSON.
     */
    public function getData() {
        // Ambil parameter filter dari request JavaScript
        $page = (int)($_GET['page'] ?? 1);
        $status_filter = strtolower($_GET['status'] ?? 'semua');
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5; // Tampilkan 5 item per halaman

        // --- Simulasi Query Database ---
        $filtered_data = array_filter($this->all_proposals, function($item) use ($status_filter, $search_text) {
            $status_match = ($status_filter === 'semua') || (strtolower($item['status']) === $status_filter);
            $search_match = empty($search_text) || str_contains(strtolower($item['nama']), $search_text);
            return $status_match && $search_match;
        });

        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);
        
        // Paginate (potong array)
        $offset = ($page - 1) * $per_page;
        $paginated_items = array_slice($filtered_data, $offset, $per_page);
        // --- Akhir Simulasi ---

        // Siapkan data untuk dikirim sebagai JSON
        $response = [
            'proposals' => $paginated_items,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $total_pages,
                'totalItems' => $total_items,
                'perPage' => $per_page,
                'showingFrom' => $total_items > 0 ? $offset + 1 : 0,
                'showingTo' => $total_items > 0 ? $offset + count($paginated_items) : 0
            ]
        ];

        // Kirim sebagai JSON dan hentikan eksekusi
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}