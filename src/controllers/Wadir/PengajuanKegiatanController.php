<?php
// File: src/controllers/Wadir/WadirPengajuanKegiatanController.php

// PASTIKAN INI ADA untuk menghindari error 'Call to undefined function view()'
require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class WadirPengajuanKegiatanController extends Controller {
    
    /**
     * Menampilkan HALAMAN LIST usulan yang menunggu persetujuan Wadir.
     * Dipanggil oleh rute: /wadir/pengajuan-kegiatan
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // $list_usulan = $usulanModel->getUsulanByStatus('Menunggu'); // Query: WHERE status='Menunggu'
        
        // Data dummy (HANYA 'Menunggu')
        $list_usulan_dummy = [
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu'],
            ['id' => 9, 'nama' => 'Webinar', 'pengusul' => 'User H', 'status' => 'Menunggu'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Kegiatan',
            'list_usulan' => $list_usulan_dummy 
        ]);

        // Gunakan view baru 'pengajuan_kegiatan.php' dan layout 'wadir'
        $this->view('pages/wadir/pengajuan_kegiatan', $data, 'wadir');
    }
}