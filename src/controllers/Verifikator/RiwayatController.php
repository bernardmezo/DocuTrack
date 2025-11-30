<?php
// File: src/controllers/Verifikator/RiwayatController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php'; // Load Model

class VerifikatorRiwayatController extends Controller {
    
    public function index($data_dari_router = []) { 
        
        // 1. Panggil Model
        $model = new verifikatorModel();
        
        // 2. Ambil Data Riwayat Real dari DB
        $list_riwayat = $model->getRiwayat();

        // 3. Siapkan Daftar Jurusan untuk Filter (Ambil unik dari data riwayat)
        $jurusan_list = array_unique(array_column($list_riwayat, 'jurusan')); // Bisa pakai 'jurusan' atau 'prodi'
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j)); // Hapus yang kosong
        sort($jurusan_list);

        // 4. Kirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/verifikator/riwayat_verifikasi', $data, 'verifikator');
    }
}
?>