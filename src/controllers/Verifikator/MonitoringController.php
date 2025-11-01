<?php
// File: src/controllers/Verifikator/rMonitoringController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class VerifikatorMonitoringController extends Controller {
    
    /**
     * Menampilkan halaman Monitoring Progres Proposal.
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // $list_proposal = $usulanModel->getAllProposalProgress();
        
        // Definisikan tahapan universal
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        
        // Data dummy (Menyimulasikan screenshot)
        $list_proposal_dummy = [
            [
                'id' => 1, 'nama' => 'Seminar Nasional: Inovasi teknologi masa depan', 'pengusul' => 'By Putra (2407411070), Teknik Informatika', 
                'status' => 'Approved', 'tahap_sekarang' => 'LPJ' // Selesai
            ],
            [
                'id' => 2, 'nama' => 'Seminar BEM: Education', 'pengusul' => 'By Putra (2407411070), Teknik Informatika', 
                'status' => 'Ditolak', 'tahap_sekarang' => 'ACC PPK' // Gagal di step 4
            ],
            [
                'id' => 3, 'nama' => 'Kulum: Education', 'pengusul' => 'By Putra (2407411070), Teknik Informatika', 
                'status' => 'In Process', 'tahap_sekarang' => 'ACC WD' // Aktif di step 3
            ],
            [
                'id' => 4, 'nama' => 'Seminar Himatik: Education', 'pengusul' => 'By Fidel (NIM), Prodi', 
                'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan' // Aktif di step 1
            ],
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $list_proposal_dummy,
            'tahapan_all' => $tahapan_all // Kirim daftar tahapan ke view
        ]);

        // Gunakan view baru 'monitoring.php' dan layout 'verifikator'
        $this->view('pages/verifikator/monitoring', $data, 'verifikator'); 
    }
}