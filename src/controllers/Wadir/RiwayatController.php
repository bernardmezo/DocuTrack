<?php
// File: src/controllers/Wadir/WadirRiwayatController.php

// Pastikan ini ada
require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class WadirRiwayatController extends Controller {
    
    /**
     * Menampilkan halaman Riwayat Verifikasi Wadir.
     * Mengambil semua usulan yang telah diproses (Disetujui, Ditolak).
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // $list_riwayat = $usulanModel->getRiwayatWadir(); // (Query: WHERE status='Disetujui' OR status='Ditolak')
        
        // Data dummy (HANYA 'Disetujui' dan 'Ditolak')
        $list_riwayat_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-26'],
            ['id' => 4, 'nama' => 'Kulum', 'pengusul' => 'User D', 'status' => 'Ditolak', 'tgl_verifikasi' => '2025-10-23'],
            ['id' => 5, 'nama' => 'Disnatalis', 'pengusul' => 'User E', 'status' => 'Disetujui', 'tgl_verifikasi' => '2025-10-24'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Persetujuan Wadir',
            'list_riwayat' => $list_riwayat_dummy 
        ]);

        // Gunakan view baru 'riwayat_verifikasi.php' dan layout 'wadir'
        $this->view('pages/wadir/riwayat_verifikasi', $data, 'wadir');
    }
}