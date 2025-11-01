<?php
// File: src/controllers/PPK/RiwayatController.php

// Pastikan ini ada
require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class PPKRiwayatController extends Controller {
    
    /**
     * Menampilkan halaman Riwayat Verifikasi PPK.
     * Mengambil semua usulan yang telah diproses (Disetujui, Ditolak).
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // $list_riwayat = $usulanModel->getRiwayatPPK(); // (Query: WHERE status='Disetujui' OR status='Ditolak')
        
        // Data dummy (HANYA 'Disetujui' dan 'Ditolak')
        $list_riwayat_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-26'],
            ['id' => 8, 'nama' => 'Kulum', 'pengusul' => 'User D', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-23'],
            ['id' => 9, 'nama' => 'Disnatalis', 'pengusul' => 'User E', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-24'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Persetujuan PPK',
            'list_riwayat' => $list_riwayat_dummy 
        ]);

        // Gunakan view baru 'riwayat_verifikasi.php' dan layout 'PPK'
        $this->view('pages/ppk/riwayat_verifikasi', $data, 'ppk');
    }
}