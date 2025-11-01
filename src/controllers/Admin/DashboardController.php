<?php
// File: src/controllers/Admin/DashboardController.php

require_once '../src/core/Controller.php';
// (Nanti, load model Anda di sini)
// require_once '../src/models/Usulan.php'; 
// require_once '../src/models/Lpj.php'; 

class AdminDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---

        // 1. Data Stats (Ringkasan)
        $stats = ['total' => 15, 'disetujui' => 10, 'ditolak' => 2, 'menunggu' => 3];

        // 2. Data Progres Bar (Contoh)
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        $tahap_sekarang_kak = 'ACC PPK';
        $icons_kak = [ 'Pengajuan' => 'fa-file-alt', 'Validasi' => 'fa-check-double', 'ACC WD' => 'fa-user-check', 'ACC PPK' => 'fa-stamp', 'Dana Cair' => 'fa-wallet', 'LPJ' => 'fa-file-invoice-dollar' ];
        $tahapan_lpj = ['Pengajuan', 'Validasi', 'ACC WD', 'ACC PPK', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi';
        $icons_lpj = [ 'Pengajuan' => 'fa-file-invoice', 'Validasi' => 'fa-check-double', 'ACC WD' => 'fa-user-graduate', 'ACC PPK' => 'fa-gavel', 'Selesai' => 'fa-flag-checkered' ];

        // 3. Data List KAK (SEMUA STATUS: Menunggu, Revisi, Disetujui, Ditolak)
        // $usulanModel = new Usulan();
        // $list_kak = $usulanModel->getAll();
        $list_kak_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui'],
            ['id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi'],
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu'],
            ['id' => 4, 'nama' => 'Kulum', 'pengusul' => 'User D', 'status' => 'Ditolak'],
        ];
        
        // 4. Data List LPJ (SEMUA STATUS)
        // $lpjModel = new Lpj();
        // $list_lpj = $lpjModel->getAll();
        $list_lpj_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Setuju'],
            ['id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi'],
            ['id' => 7, 'nama' => 'Workshop UI/UX', 'pengusul' => 'User D', 'status' => 'Menunggu'],
        ];
        
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'tahapan_kak' => $tahapan_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,
            'icons_kak' => $icons_kak,
            'tahapan_lpj' => $tahapan_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj,
            'icons_lpj' => $icons_lpj,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/admin/dashboard', $data, 'app'); 
    }
}