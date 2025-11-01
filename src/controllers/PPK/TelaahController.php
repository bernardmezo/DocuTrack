<?php
// File: src/controllers/PPK/TelaahController.php

// 1. Memuat Controller inti (PENTING)
require_once '../src/core/Controller.php';
// 2. (Nanti) Muat Model Anda di sini
// require_once '../src/models/Usulan.php'; 

class PPKTelaahController extends Controller {
    
    /**
     * --- SIMULASI DATABASE MASTER (KONSISTEN) ---
     * Ini adalah "sumber kebenaran" untuk PPK.
     * Hanya berisi status: Menunggu, Disetujui.
     */
    private $list_usulan_all = [
        1 => [
            'id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui', 
            'kak' => ['nama_pengusul' => 'User A', 'nama_kegiatan' => 'Seminar Nasional', 'gambaran_umum' => 'Gambaran seminar...', 'penerima_manfaat' => 'Mahasiswa'],
            'iku' => ['Mendapat Pekerjaan'],
            'indikator' => [['bulan' => 'Oktober', 'nama' => 'Peserta Hadir', 'target' => 100]],
            'rab' => ['Belanja Barang' => [['id'=>1, 'uraian'=>'Snack', 'rincian'=>'Box', 'volume'=>100, 'satuan'=>'Box', 'harga'=>15000]]],
            'komentar_penolakan' => ''
        ],
        3 => [
            'id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu', 
            'kak' => ['nama_pengusul' => 'User C', 'nama_kegiatan' => 'Lomba Cerdas Cermat', 'gambaran_umum' => 'Gambaran lomba...', 'penerima_manfaat' => 'Siswa SMA'],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'Desember', 'nama' => 'Jumlah Tim', 'target' => 30]],
            'rab' => ['Belanja Hadiah' => [['id'=>4, 'uraian'=>'Piala', 'rincian'=>'Set Piala', 'volume'=>3, 'satuan'=>'Set', 'harga'=>250000]]],
            'komentar_penolakan' => ''
        ],
        9 => [
            'id' => 9, 'nama' => 'Webinar', 'pengusul' => 'User H', 'status' => 'Menunggu', 
            'kak' => ['nama_pengusul' => 'User H', 'nama_kegiatan' => 'Webinar', 'gambaran_umum' => '...','penerima_manfaat' => '...'],
            'iku' => ['Prestasi'],
            'indikator' => [],
            'rab' => [],
            'komentar_penolakan' => ''
        ],
    ];

    /**
     * Menampilkan HALAMAN DETAIL untuk PPK menelaah KAK/RAB.
     * Dipanggil oleh rute: /PPK/telaah/show/{id}
     */
    public function show($id, $data_dari_router = []) {
        
        // --- 1. Tentukan URL Kembali (Dinamis) ---
        $base_url = "/docutrack/public/ppk";
        $ref = $_GET['ref'] ?? '';

        switch ($ref) {
            case 'pengajuan-kegiatan':
                $back_url = $base_url . '/pengajuan-kegiatan';
                break;
            case 'riwayat-verifikasi':
                $back_url = $base_url . '/riwayat-verifikasi';
                break;
            case 'dashboard':
                $back_url = $base_url . '/dashboard';
                break;
            default:
                // fallback otomatis ke halaman sebelumnya jika ada
                $back_url = $_SERVER['HTTP_REFERER'] ?? $base_url . '/dashboard';
                break;
        }

        // --- 2. Ambil Data dari Master List (Simulasi) ---
        // $usulanModel = new Usulan();
        // $usulan_dipilih = $usulanModel->getFullDetailById($id);
        
        $usulan_dipilih = $this->list_usulan_all[$id] ?? null;
        
        if (!$usulan_dipilih) {
            return not_found("Usulan dengan ID $id tidak ditemukan.");
        }
        
        $status = $usulan_dipilih['status'];
        // --- Akhir Simulasi ---

        // 3. Kirim data (TERMASUK ROLE & STATUS) ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Persetujuan PPK - ' . htmlspecialchars($usulan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'PPK', // <-- Mengirim 'PPK'
            
            // Data Payload
            'kegiatan_data' => $usulan_dipilih['kak'],
            'iku_data' => $usulan_dipilih['iku'],
            'indikator_data' => $usulan_dipilih['indikator'],
            'rab_data' => $usulan_dipilih['rab'],
            'komentar_penolakan' => $usulan_dipilih['komentar_penolakan'] ?? '',
            'back_url' => $back_url
        ]);

        // Panggil view 'telaah_detail' KHUSUS PPK
        // dan gunakan layout 'PPK'
        $this->view('pages/ppk/telaah_detail', $data, 'PPK');
    }
}