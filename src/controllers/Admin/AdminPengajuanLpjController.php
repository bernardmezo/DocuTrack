<?php
// File: src/controllers/Admin/AdminPengajuanLpjController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Lpj.php'; 
// require_once '../src/models/Kegiatan.php'; 

class AdminPengajuanLpjController extends Controller {
    
    /**
     * Menampilkan HALAMAN LIST
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data dari Model ---
        // $lpjModel = new Lpj();
        // $list_lpj = $lpjModel->getAll(); // Ambil semua status LPJ
        
        // Data dummy (SEMUA STATUS LPJ)
        $list_lpj_dummy = [
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Setuju'],
            ['id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi'],
            ['id' => 7, 'nama' => 'Workshop UI/UX', 'pengusul' => 'User D', 'status' => 'Menunggu'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan LPJ',
            'list_lpj' => $list_lpj_dummy 
        ]);

        $this->view('pages/admin/pengajuan_lpj_list', $data, 'app');
    }

    /**
     * Menampilkan HALAMAN DETAIL RAB (Perbandingan Rencana vs Realisasi)
     */
    public function show($id, $data_dari_router = []) {
        
        // --- 1. Tentukan URL Kembali (Dinamis) ---
        $ref = $_GET['ref'] ?? 'lpj'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

        // --- 2. Simulasi Pengambilan Data dari Model ---
        $list_lpj_all = [
             1 => ['nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Setuju'],
             2 => ['nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi'],
             7 => ['nama' => 'Workshop UI/UX', 'pengusul' => 'User D', 'status' => 'Menunggu'],
        ];
        
        $kegiatan_dipilih = $list_lpj_all[$id] ?? null;
        if (!$kegiatan_dipilih) {
            return not_found("LPJ ID $id tidak ditemukan.");
        }
        
        $status = $kegiatan_dipilih['status'];
        // --- Akhir Simulasi ---

        // --- 3. Siapkan Data Dummy Lainnya ---
        $kegiatan_data_dummy = ['nama_kegiatan' => $kegiatan_dipilih['nama'] . " (ID: $id)"];
        $rab_items_merged = [
            'Belanja Jasa' => [
                ['id' => 1, 'uraian' => 'Sewa Sound System', 'harga_plan' => 500000, 'harga_realisasi' => ($status === 'Revisi' ? 450000 : 500000), 'bukti_file' => 'nota.pdf', 'komentar' => ($status === 'Revisi' ? 'Jumlah tidak sesuai.' : null)]
            ]
        ];
        $komentar_revisi_dummy = ($status === 'Revisi') ? ['rab_belja_jasa' => 'Jumlah tidak sesuai.'] : [];
        // --- Akhir Data Dummy ---

        // 4. Kirim data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data_dummy['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data_dummy,
            'rab_items' => $rab_items_merged,
            'komentar_revisi' => $komentar_revisi_dummy,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_lpj', $data, 'app');
    }
}