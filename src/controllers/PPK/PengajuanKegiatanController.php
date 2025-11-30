<?php
// File: src/controllers/PPK/PengajuanKegiatanController.php

require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php'; // Load Model

class PPKPengajuanKegiatanController extends Controller {
    
    public function index($data_dari_router = []) { 
        
        // 1. Panggil Model
        $model = new ppkModel();

        // 2. Ambil Semua Data Real dari DB
        $all_data = $model->getDashboardKAK();

        // 3. Filter Data: Hanya Tampilkan yang Statusnya 'Disetujui Verifikator'
        // Ini adalah antrian masuk untuk PPK (yang sudah lolos dari Verifikator)
        $antrian_ppk = array_filter($all_data, function($item) {
            $posisi = isset($item['posisi']) ? strtolower($item['posisi']) : '4'; // Pastikan posisiId adalah 4 untuk PPK
            return $posisi === 'ppk' || $posisi === '4';
        });

        // Re-index array agar urut (0, 1, 2...) untuk JSON JS
        $antrian_ppk = array_values($antrian_ppk);

        // 4. Siapkan Daftar Jurusan Unik untuk Filter Dropdown
        $jurusan_list = array_unique(array_column($antrian_ppk, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j) && $j !== '-');
        sort($jurusan_list);
        
        // 5. Kirim Data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Kegiatan',
            'list_usulan' => $antrian_ppk, // Data yang sudah difilter
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/ppk/pengajuan_kegiatan', $data, 'ppk');
    }
}
?>