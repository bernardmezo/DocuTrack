<?php
// File: src/controllers/PPK/RiwayatController.php

require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php';

class PPKRiwayatController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Panggil Model
        $model = new ppkModel();

        // 2. Ambil Data Riwayat
        $list_riwayat = $model->getRiwayat();

        // 3. Kirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi PPK',
            'list_riwayat' => $list_riwayat
        ]);

        $this->view('pages/ppk/riwayat_verifikasi', $data, 'ppk');
    }
}
?>