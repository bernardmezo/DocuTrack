<?php
// File: src/controllers/Verifikator/DashboardController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class VerifikatorDashboardController extends Controller {
    
    /**
     * Menampilkan halaman Dashboard Verifikator.
     */
    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---
        
        // 1. Data untuk Kartu Statistik
        // $usulanModel = new Usulan();
        // $stats = [
        //     'total' => $usulanModel->getCountForVerifikator('total'),
        //     'disetujui' => $usulanModel->getCountForVerifikator('disetujui'),
        //     'ditolak' => $usulanModel->getCountForVerifikator('ditolak'),
        //     'pending' => $usulanModel->getCountForVerifikator('pending') // (Menunggu + Revisi)
        // ];
        $stats_dummy = [
            'total' => 15,
            'disetujui' => 10,
            'ditolak' => 2,
            'pending' => 3 
        ];
        
        // 2. Data untuk Tabel List Usulan (Menampilkan SEMUA status)
        // $list_usulan = $usulanModel->getAllForVerifikator();
        $list_usulan_dummy = [
            ['id' => 3, 'nama' => 'Kulum', 'pengusul' => 'Bernadya (NIM), Prodi', 'status' => 'Telah Direvisi'], // Aksi
            ['id' => 4, 'nama' => 'Seminar Himatik', 'pengusul' => 'Fidel (NIM), Prodi', 'status' => 'Menunggu'], // Aksi
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'Putra (NIM), Prodi', 'status' => 'Disetujui'], // Riwayat
            ['id' => 2, 'nama' => 'Seminar BEM', 'pengusul' => 'Yopan (NIM), Prodi', 'status' => 'Revisi'], // Riwayat (Menunggu Admin)
            ['id' => 6, 'nama' => 'Seminar Expektik', 'pengusul' => 'Bambang (NIM), Prodi', 'status' => 'Ditolak'], // Riwayat
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Verifikator',
            'stats' => $stats_dummy,
            'list_usulan' => $list_usulan_dummy
        ]);

        // Panggil view 'dashboard' milik verifikator dan gunakan layout 'verifikator'
        $this->view('pages/verifikator/dashboard', $data, 'verifikator'); 
    }
}