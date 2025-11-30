<?php
// File: src/controllers/Super_Admin/BuatikuController.php

require_once '../src/core/Controller.php';
require_once '../src/model/superAdminModel.php';

class SuperadminBuatikuController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new superAdminModel();
    }
    
    public function index() { 
        // 1. Ambil Parameter
        $page = (int)($_GET['page'] ?? 1);
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5;

        // ✅ AMBIL DATA DARI DATABASE
        $all_iku = $this->model->getAllIKU();
        
        // 2. Logika Filter (Search)
        $filtered_data = array_filter($all_iku, function($item) use ($search_text) {
            if (empty($search_text)) return true;
            return str_contains(strtolower($item['nama']), $search_text);
        });

        // 3. Pagination Logic
        $filtered_data = array_values($filtered_data);
        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);
        
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

        $this->view('pages/Super_Admin/buat-iku', $data, 'super_admin'); 
    }
    
    /**
     * Tambah IKU baru
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/super_admin/buat-iku');
            exit;
        }
        
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        
        if (empty($nama)) {
            $_SESSION['flash_error'] = 'Nama IKU wajib diisi!';
            header('Location: /docutrack/public/super_admin/buat-iku');
            exit;
        }
        
        if ($this->model->createIKU($nama, $deskripsi)) {
            $_SESSION['flash_message'] = 'IKU berhasil ditambahkan!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal menambahkan IKU.';
        }
        
        header('Location: /docutrack/public/super_admin/buat-iku');
        exit;
    }
    
    /**
     * Edit IKU
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/super_admin/buat-iku');
            exit;
        }
        
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        
        if ($this->model->updateIKU($id, $nama, $deskripsi)) {
            $_SESSION['flash_message'] = 'IKU berhasil diupdate!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal mengupdate IKU.';
        }
        
        header('Location: /docutrack/public/super_admin/buat-iku');
        exit;
    }
    
    /**
     * Hapus IKU
     */
    public function delete($id) {
        if ($this->model->deleteIKU($id)) {
            $_SESSION['flash_message'] = 'IKU berhasil dihapus!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus IKU.';
        }
        
        header('Location: /docutrack/public/super_admin/buat-iku');
        exit;
    }
}