<?php
// File: src/controllers/Admin/PengajuanUsulanController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php';

class AdminPengajuanUsulanController extends Controller {

    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // Filter data: Ambil KAK yang butuh aksi (bukan yang sudah 'Disetujui')
        // $antrian_kak = $usulanModel->getKakButuhAksi(); 

        // Data dummy (HANYA Menunggu, Revisi, Ditolak)
        $antrian_kak_dummy = [
            ['id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi'],
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu'],
            ['id' => 4, 'nama' => 'Kulum', 'pengusul' => 'User D', 'status' => 'Ditolak'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan Usulan KAK',
            // Data ini digunakan oleh tabel antrian di 'pengajuan_usulan.php'
            'antrian_kak' => $antrian_kak_dummy 
        ]);

        // Panggil view form (yang juga berisi tabel antrian)
        $this->view('pages/admin/pengajuan_usulan', $data, 'app'); 
    }

    // Nanti Anda akan punya method 'store' untuk menyimpan data form
    // public function store() { /* ... */ }
}