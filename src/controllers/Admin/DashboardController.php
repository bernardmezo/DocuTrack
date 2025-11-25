<?php
// File: src/controllers/Admin/DashboardController.php

require_once '../src/core/Controller.php';
// 1. Load Model Admin
require_once '../src/model/adminModel.php';

class AdminDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 2. Instansiasi Model
        $model = new ModelAdmin();

        // 3. Ambil Data Real dari Database
        // Data Statistik (Kartu di atas)
        $stats = $model->getDashboardStats(); 
        
        // Data Tabel List KAK
        $list_kak = $model->getDashboardKAK(); 
        
        // Data Tabel List LPJ
        $list_lpj = $model->getDashboardLPJ(); 

        // -------------------------------------------------------
        // Data Statis untuk Alur Progress (Jarang berubah)
        // -------------------------------------------------------
        
        // Alur KAK
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC PPK', 'ACC WD', 'Dana Cair'];
        $tahap_sekarang_kak = 'ACC WD'; // Nanti bisa dibuat dinamis per user/kegiatan jika perlu
        $icons_kak = [ 
            'Pengajuan' => 'fa-file-alt', 
            'Validasi' => 'fa-check-double', 
            'ACC PPK' => 'fa-stamp', 
            'ACC WD' => 'fa-user-check', 
            'Dana Cair' => 'fa-wallet'
        ];
        
        // Alur LPJ
        $tahapan_lpj = ['Upload Bukti', 'Validasi', 'ACC Bendahara', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi'; // Nanti bisa dibuat dinamis
        $icons_lpj = [ 
            'Upload Bukti' => 'fa-upload', 
            'Validasi' => 'fa-check-double', 
            'ACC Bendahara' => 'fa-file-invoice-dollar', 
            'Selesai' => 'fa-flag-checkered' 
        ];

        // 4. Gabungkan semua data untuk dikirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Admin Dashboard',
            'stats' => $stats, // Data Real dari DB
            
            // Data Alur (Statis)
            'tahapan_kak' => $tahapan_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,
            'icons_kak' => $icons_kak,
            'tahapan_lpj' => $tahapan_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj,
            'icons_lpj' => $icons_lpj,
            
            // Data List Tabel (Real dari DB)
            'list_kak' => $list_kak, 
            'list_lpj' => $list_lpj  
        ]);

        // 5. Tampilkan View
        $this->view('pages/admin/dashboard', $data, 'app'); 
    }
}