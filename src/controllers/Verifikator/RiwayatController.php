<?php
// File: src/controllers/Verifikator/RiwayatController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; 

// PASTIKAN NAMA CLASS INI SESUAI
class RiwayatController extends Controller {
    
    /**
     * Menampilkan halaman Riwayat Verifikasi.
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        $list_riwayat_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'Putra (NIM), Prodi', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-26'],
            ['id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'Yopan (NIM), Prodi', 'status' => 'Revisi', 'tgl_verifikasi' => '2025-10-25'],
            ['id' => 5, 'nama' => 'Disnatalis', 'pengusul' => 'Anton(NIM), Prodi', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-24'],
            ['id' => 6, 'nama' => 'Seminar Expektik', 'pengusul' => 'Bambang (NIM), Prodi', 'status' => 'Ditolak', 'tgl_verifikasi' => '2025-10-23'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat_dummy 
        ]);

        $this->view('pages/verifikator/riwayat_verifikasi', $data, 'verifikator');
    }
}