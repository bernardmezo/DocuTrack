<?php
// File: src/controllers/Verifikator/DashboardController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php';

class VerifikatorDashboardController extends Controller {
    
    /**
     * --- DATA MASTER DUMMY (STRUKTUR PNJ) ---
     * 'jurusan' = Induk (Digunakan untuk Filter)
     * 'prodi'   = Spesifik (Digunakan untuk Tampilan)
     */
    
    public function index($data_dari_router = []) {
        
        $model = new verifikatorModel();

        $stats = $model->getDashboardStats();

        $list_usulan = $model->getDashboardKAK();

        
        // Hitung statistik
        $total = count($list_usulan);
        $disetujui = count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'disetujui'));
        $ditolak = count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'ditolak'));
        $pending = count(array_filter($list_usulan, function($u) {
            $s = strtolower($u['status']);
            return $s === 'menunggu' || $s === 'telah direvisi';
        }));
        
        $stats = [
            'total' => $total,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
            'pending' => $pending
        ];
        
        // Daftar Jurusan Unik (Untuk Dropdown Filter)
        $jurusan_list = array_unique(array_column($list_usulan, 'jurusan'));
        sort($jurusan_list);
        
        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Verifikator',
            'stats' => $stats,
            'list_usulan' => $list_usulan,
            'jurusan_list' => $jurusan_list,
            'current_page' => 1,
            'total_pages' => 1
        ]);

        $this->view('pages/verifikator/dashboard', $data, 'verifikator'); 
    }
}