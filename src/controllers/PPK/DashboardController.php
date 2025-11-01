<?php
// File: src/controllers/ppk/DashboardController.php

require_once '../src/core/Controller.php';

class PPKDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---
        
        // Data Stats (Sekarang lengkap 4 status)
        $stats_dummy = [
            'total' => 15,
            'disetujui' => 12,
            'menunggu' => 2 // 'Menunggu' menggantikan 'Pending'
        ];

        // Data List (Data ini tetap sama)
        $list_usulan_dummy = [
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu'],
            ['id' => 9, 'nama' => 'Webinar', 'pengusul' => 'User H', 'status' => 'Menunggu'],
            ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui'],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard ppk',
            'stats' => $stats_dummy,
            'list_usulan' => $list_usulan_dummy
        ]);

        $this->view('pages/ppk/dashboard', $data, 'ppk'); 
    }
}