<?php
// File: src/controllers/Super_Admin/KelolaAkunController.php

require_once '../src/core/Controller.php';
require_once '../src/model/superAdminModel.php';

class SuperadminKelolaakunController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new superAdminModel();
    }
    
    public function index($data_dari_router = []) {
        
        // ✅ AMBIL DATA DARI DATABASE (bukan dummy)
        $list_users = $this->model->getAllUsers();
        $list_roles = $this->model->getAllRoles();
        $list_jurusan = $this->model->getListJurusan();
        
        $data = array_merge($data_dari_router, [
            'title' => 'Kelola Akun Pengguna',
            'list_users' => $list_users,
            'list_roles' => $list_roles,
            'list_jurusan' => $list_jurusan
        ]);

        $this->view('pages/Super_Admin/kelola-akun', $data, 'super_admin'); 
    }
    
    /**
     * View detail user
     */
    public function show($id, $data_dari_router = []) {
        $user = $this->model->getUserById($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'User tidak ditemukan.';
            header('Location: /docutrack/public/super_admin/kelola-akun');
            exit;
        }
        
        $data = array_merge($data_dari_router, [
            'title' => 'Detail User - ' . htmlspecialchars($user['nama']),
            'user' => $user
        ]);
        
        $this->view('pages/Super_Admin/kelola-akun-detail', $data, 'super_admin');
    }
    
    /**
     * Edit user
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/super_admin/kelola-akun');
            exit;
        }
        
        $updateData = [];
        
        if (!empty($_POST['nama'])) {
            $updateData['nama'] = trim($_POST['nama']);
        }
        if (!empty($_POST['email'])) {
            $updateData['email'] = trim($_POST['email']);
        }
        if (!empty($_POST['password'])) {
            $updateData['password'] = $_POST['password'];
        }
        if (isset($_POST['roleId'])) {
            $updateData['roleId'] = (int)$_POST['roleId'];
        }
        if (isset($_POST['namaJurusan'])) {
            $updateData['namaJurusan'] = $_POST['namaJurusan'];
        }
        
        if ($this->model->updateUser($id, $updateData)) {
            $_SESSION['flash_message'] = 'User berhasil diupdate!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal mengupdate user.';
        }
        
        header('Location: /docutrack/public/super_admin/kelola-akun');
        exit;
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        if ($this->model->deleteUser($id)) {
            $_SESSION['flash_message'] = 'User berhasil dihapus!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus user.';
        }
        
        header('Location: /docutrack/public/super_admin/kelola-akun');
        exit;
    }
    
    /**
     * Create new user
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Tampilkan form create
            $list_roles = $this->model->getAllRoles();
            $list_jurusan = $this->model->getListJurusan();
            
            $data = [
                'title' => 'Buat Akun Baru',
                'list_roles' => $list_roles,
                'list_jurusan' => $list_jurusan
            ];
            
            $this->view('pages/Super_Admin/kelola-akun-create', $data, 'super_admin');
            return;
        }
        
        // Process create
        $userData = [
            'nama' => trim($_POST['nama'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'roleId' => (int)($_POST['roleId'] ?? 1),
            'namaJurusan' => $_POST['namaJurusan'] ?? null
        ];
        
        if (empty($userData['nama']) || empty($userData['email']) || empty($userData['password'])) {
            $_SESSION['flash_error'] = 'Nama, email, dan password wajib diisi!';
            header('Location: /docutrack/public/super_admin/kelola-akun/create');
            exit;
        }
        
        if ($this->model->createUser($userData)) {
            $_SESSION['flash_message'] = 'User berhasil dibuat!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_error'] = 'Gagal membuat user.';
        }
        
        header('Location: /docutrack/public/super_admin/kelola-akun');
        exit;
    }
}