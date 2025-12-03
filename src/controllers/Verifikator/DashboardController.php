<?php
// File: src/controllers/Verifikator/DashboardController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php';

class VerifikatorDashboardController extends Controller {
    
    /**
     * --- DATA MASTER DUMMY (STRUKTUR PNJ) ---
     * 'jurusan' = Induk (Digunakan untuk Filter)
     * 'prodi'   = Spesifik (Digunakan untuk Tampilan)
     */
    
    public function index($data_dari_router = []) {
        
        $model = new verifikatorModel($this->db);

        $stats = $model->getDashboardStats();

        $list_usulan = $model->getDashboardKAK();

        $stats = [
            'total' => $stats['total'],
            'disetujui' => $stats['disetujui'],
            'ditolak' => $stats['ditolak'],
            'pending' => $stats['pending']
        ];
        
        // Daftar Jurusan Unik (Untuk Dropdown Filter)
        // Ambil dari master table tbl_jurusan lewat model jika tersedia,
        // jika kosong gunakan fallback dari list_usulan (kolom 'jurusan').
        $jurusan_list = [];
        $jurusan_rows = $model->getListJurusan();

        if (!empty($jurusan_rows)) {
            // Map ke nama jurusan; coba beberapa kemungkinan nama kolom yang umum
            $jurusan_list = array_map(function($r) {
                if (isset($r['nama_jurusan'])) return $r['namaJurusan'];
                // fallback: ambil nilai pertama non-empty pada row
                foreach ($r as $v) { if ($v !== null && $v !== '') return $v; }
                return null;
            }, $jurusan_rows);
            
            // normalisasi: hapus null/empty, unik, urutkan
            $jurusan_list = array_values(array_filter(array_unique($jurusan_list), fn($v) => $v !== null && $v !== ''));
            sort($jurusan_list);
        } else {
            // fallback ke cara lama: ambil kolom 'jurusan' dari data usulan
            $jurusan_list = array_unique(array_column($list_usulan, 'jurusan'));
            sort($jurusan_list);
        }
        
        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Verifikator',
            'stats' => $stats,
            'list_usulan' => $list_usulan,
            'jurusan_list' => $jurusan_list,
            'current_page' => 1,
            'total_pages' => 1
        ]);

        $this->view('pages/verifikator/dashboard', $data, 'verifikator');
    }
}