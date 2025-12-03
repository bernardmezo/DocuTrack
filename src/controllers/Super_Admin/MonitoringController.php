<?php
// File: src/controllers/Super_Admin/MonitoringController.php

require_once '../src/core/Controller.php';
require_once '../src/model/superAdminModel.php';

class SuperadminMonitoringController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new superAdminModel($this->db);
    }
    
    /**
     * Method INDEX - Server-side Processing dengan data real dari Database
     */
    public function index($data_dari_router = []) { 
        // Ambil parameter filter dari URL
        $page = (int)($_GET['page'] ?? 1);
        $status_filter = strtolower($_GET['status'] ?? 'semua');
        $jurusan_filter = $_GET['jurusan'] ?? 'semua';
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5;

        // ✅ AMBIL DATA DARI DATABASE
        $filters = [
            'status' => $status_filter,
            'jurusan' => $jurusan_filter,
            'search' => $search_text
        ];
        
        $all_proposals = $this->model->getProposalMonitoring($filters);
        $list_jurusan = $this->model->getListJurusan();

        // --- Pagination ---
        $total_items = count($all_proposals);
        $total_pages = ceil($total_items / $per_page);
        $page = max(1, min($page, $total_pages ?: 1));
        
        $offset = ($page - 1) * $per_page;
        $paginated_items = array_slice($all_proposals, $offset, $per_page);

        // --- Pagination Info ---
        $showing_from = $total_items > 0 ? $offset + 1 : 0;
        $showing_to = $total_items > 0 ? $offset + count($paginated_items) : 0;

        // --- Kirim data ke View ---
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $paginated_items,
            'list_jurusan' => $list_jurusan,
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