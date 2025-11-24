<?php
// File: src/controllers/Super_Admin/KelolaAkunController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/User.php'; // Uncomment when model is ready

class SuperadminKelolaakunController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- Data Dummy Users ---
        $list_users = [
                    ['id' => 1, 'nama' => 'Dr. Ahmad Pengusul', 'email' => 'ahmad.p@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Aktif', 'last_login' => '2024-01-15 08:30:00'],
        ['id' => 2, 'nama' => 'Siti Verifikator, M.Ak', 'email' => 'siti.v@pnj.ac.id', 'role' => 'Verifikator', 'jurusan' => 'Akuntansi', 'status' => 'Aktif', 'last_login' => '2024-01-15 09:15:00'],
        ['id' => 3, 'nama' => 'Budi PPK, S.T', 'email' => 'budi.ppk@pnj.ac.id', 'role' => 'PPK', 'jurusan' => 'Teknik Sipil', 'status' => 'Aktif', 'last_login' => '2024-01-14 14:20:00'],
        ['id' => 4, 'nama' => 'Rina Bendahara', 'email' => 'rina.b@pnj.ac.id', 'role' => 'Bendahara', 'jurusan' => 'Administrasi Niaga', 'status' => 'Aktif', 'last_login' => '2024-01-15 10:00:00'],
        ['id' => 5, 'nama' => 'Prof. Wadir Dua', 'email' => 'wadir2@pnj.ac.id', 'role' => 'Wadir', 'jurusan' => 'Manajemen Pusat', 'status' => 'Aktif', 'last_login' => '2024-01-10 11:00:00'],
        ['id' => 6, 'nama' => 'Dian Grafika', 'email' => 'dian.g@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'status' => 'Tidak Aktif', 'last_login' => '2023-12-20 15:00:00'],
        ['id' => 7, 'nama' => 'Joko Mesin', 'email' => 'joko.m@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Mesin', 'status' => 'Aktif', 'last_login' => '2024-01-15 13:30:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
        ];
        
        // --- Akhir Data Dummy ---
        
        $data = array_merge($data_dari_router, [
            'title' => 'Kelola Akun Pengguna',
            'list_users' => $list_users
        ]);

        $this->view('pages/Super_Admin/kelola-akun', $data, 'super_admin'); 
    }
    
    /**
     * View detail user
     */
    public function show($id) {
        // TODO: Implement view detail
        echo "View user detail: " . $id;
    }
    
    /**
     * Edit user
     */
    public function edit($id) {
        // TODO: Implement edit user
        echo "Edit user: " . $id;
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        // TODO: Implement delete user
        echo "Delete user: " . $id;
    }
    
    /**
     * Create new user
     */
    public function create() {
        // TODO: Implement create user
        echo "Create new user";
    }
}