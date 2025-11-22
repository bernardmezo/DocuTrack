<?php
// File: src/controllers/Admin/PengajuanUsulanController.php

require_once '../src/core/Controller.php';
// 1. Load Model Admin
require_once '../src/model/adminModel.php';

class AdminPengajuanUsulanController extends Controller {

    public function index($data_dari_router = []) {
        
        // 2. Instansiasi Model
        $modelAdmin = new adminModel();

        // 3. Ambil Data Real dari Database
        // Fungsi ini akan me-return array yang strukturnya sudah cocok dengan View
        // (key: 'id', 'nama', 'pengusul', 'status')
        $antrian_kak = $modelAdmin->getAntrianKAK();

        // 4. Kirim data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan Usulan KAK',
            'antrian_kak' => $antrian_kak 
        ]);

        // Panggil view
        $this->view('pages/admin/pengajuan_usulan', $data, 'app'); 
    }

    // Tambahkan method ini di dalam Class
    public function store() {
        // 1. Cek apakah methodnya POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/admin/pengajuan-usulan');
            exit;
        }

        // 2. Panggil Model
        $modelAdmin = new adminModel();

        // 3. Kirim seluruh data $_POST ke Model untuk disimpan
        // Hasilnya true (berhasil) atau false (gagal)
        $berhasil = $modelAdmin->simpanPengajuan($_POST);

        if ($berhasil) {
            // Set pesan sukses (opsional, pakai Session Flash message nanti)
            // Redirect kembali ke halaman antrian
            header('Location: /docutrack/public/admin/pengajuan-usulan?status=success');
        } else {
            // Redirect error
            header('Location: /docutrack/public/admin/pengajuan-usulan?status=error');
        }
        exit;
    }
}