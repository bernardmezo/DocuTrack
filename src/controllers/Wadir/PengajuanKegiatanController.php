<?php
// File: src/controllers/Wadir/PengajuanKegiatanController.php

require_once '../src/core/Controller.php';
require_once '../src/model/wadirModel.php';

class WadirPengajuanKegiatanController extends Controller {
    
    public function index($data_dari_router = []) { 
        
        $model = new wadirModel($this->db);
        
        // Ambil data yang ada di meja Wadir (Posisi 3)
        $list_usulan_all = $model->getDashboardKAK();

        // Daftar jurusan
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);
        
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Wadir',
            'list_usulan' => $list_usulan_all,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/wadir/pengajuan_kegiatan', $data, 'wadir');
    }
}