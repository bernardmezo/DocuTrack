<?php
// File: src/controllers/Admin/PengajuanUsulanController.php

require_once '../src/core/Controller.php';
// 1. Load Model Admin
require_once '../src/model/adminModel.php';

class AdminPengajuanUsulanController extends Controller {

    public function index($data_dari_router = []) {
        
        // --- MODIFIKASI: BYPASS PENGAMBILAN DATA ---
        
        // 1. Kita tidak menginstansiasi Model di sini
        // $modelAdmin = new adminModel(); 

        // 2. Kita set $antrian_kak menjadi array kosong
        // Ini penting agar view tidak error saat mencoba loop data
        $antrian_kak = []; 

        // 3. Kirim data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan Usulan KAK',
            'antrian_kak' => $antrian_kak 
        ]);

        // 4. Panggil view
        // Halaman akan tampil, tapi tabel antrian akan kosong (tertulis "Belum ada data")
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