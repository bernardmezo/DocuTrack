<?php
// File: src/controllers/Wadir/RiwayatController.php

require_once '../src/core/Controller.php';
require_once '../src/model/wadirModel.php';

class WadirRiwayatController extends Controller {
    
    public function index($data_dari_router = []) { 
        
        $model = new wadirModel($this->db);
        
        // Ambil Data Riwayat (Posisi 5 / Ditolak)
        $list_riwayat = $model->getRiwayat();

        // Jurusan untuk filter
        $jurusan_list = array_unique(array_column($list_riwayat, 'prodi')); // Atau jurusan
        $jurusan_list = array_filter($jurusan_list, fn($j) => !empty($j));
        sort($jurusan_list);

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Persetujuan Wadir',
            'list_riwayat' => $list_riwayat,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/wadir/riwayat_verifikasi', $data, 'wadir');
    }
}