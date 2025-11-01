<?php
// File: src/controllers/Verifikator/VerifikatorTelaahController.php

// 1. Memuat Controller inti (PENTING UNTUK MENGHINDARI ERROR)
require_once '../src/core/Controller.php';
// 2. (Nanti) Muat Model Anda di sini
// require_once '../src/models/Usulan.php'; 

class VerifikatorTelaahController extends Controller {
    
    /**
     * --- SIMULASI DATABASE MASTER (KONSISTEN DENGAN DASHBOARD) ---
     * Ini adalah "sumber kebenaran" yang akan digunakan oleh SEMUA method di controller ini.
     * Nanti, Anda akan mengganti ini dengan panggilan ke Model Anda.
     */
    private $list_usulan_all = [
        1 => [
            'id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'Putra (NIM), Prodi', 'status' => 'Disetujui', 
            'kode_mak' => '123.45.67', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Putra (NIM), Prodi', 'nama_kegiatan' => 'Seminar Nasional', 'gambaran_umum' => 'Gambaran seminar...', 'penerima_manfaat' => 'Mahasiswa'],
            'iku' => ['Mendapat Pekerjaan'],
            'indikator' => [['bulan' => 'Oktober', 'nama' => 'Peserta Hadir', 'target' => 100]],
            'rab' => ['Belanja Barang' => [['id'=>1, 'uraian'=>'Snack', 'rincian'=>'Box', 'volume'=>100, 'satuan'=>'Box', 'harga'=>15000]]]
        ],
        2 => [
            'id' => 2, 'nama' => 'Seminar BEM', 'pengusul' => 'Yopan (NIM), Prodi', 'status' => 'Revisi', 
            'kode_mak' => '', 'komentar' => ['rab_belanja_jasa' => 'Harga sewa sound system terlalu mahal.'], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Yopan (NIM), Prodi', 'nama_kegiatan' => 'Seminar BEM', 'gambaran_umum' => 'Gambaran workshop...', 'penerima_manfaat' => 'Anggota BEM'],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'November', 'nama' => 'Peserta Workshop', 'target' => 50]],
            'rab' => ['Belanja Jasa' => [['id'=>2, 'uraian'=>'Sewa Sound System', 'rincian'=>'Sewa 1 hari', 'volume'=>1, 'satuan'=>'Hari', 'harga'=>500000]]]
        ],
        3 => [
            'id' => 3, 'nama' => 'Kulum', 'pengusul' => 'Bernadya (NIM), Prodi', 'status' => 'Telah Direvisi', 
            'kode_mak' => '', 'komentar' => ['gambaran_umum' => 'OK, sudah diperbaiki oleh Admin.'], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Bernadya (NIM), Prodi', 'nama_kegiatan' => 'Kulum', 'gambaran_umum' => 'Gambaran Kulum...', 'penerima_manfaat' => 'Umum'],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [],
            'rab' => []
        ],
        4 => [
            'id' => 4, 'nama' => 'Seminar Himatik', 'pengusul' => 'Fidel (NIM), Prodi', 'status' => 'Menunggu', 
            'kode_mak' => '', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Fidel (NIM), Prodi', 'nama_kegiatan' => 'Seminar Himatik', 'gambaran_umum' => 'Gambaran Himatik...', 'penerima_manfaat' => 'Mahasiswa TI'],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'Desember', 'nama' => 'Jumlah Tim', 'target' => 30]],
            'rab' => ['Belanja Hadiah' => [['id'=>4, 'uraian'=>'Piala', 'rincian'=>'Set Piala', 'volume'=>3, 'satuan'=>'Set', 'harga'=>250000]]]
        ],
        5 => [
            'id' => 5, 'nama' => 'Disnatalis', 'pengusul' => 'Anton(NIM), Prodi', 'status' => 'Disetujui', 
            'kode_mak' => '123.45.99', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Anton(NIM), Prodi', 'nama_kegiatan' => 'Disnatalis', 'gambaran_umum' => '...','penerima_manfaat' => '...'],
            'iku' => ['Prestasi'],
            'indikator' => [],
            'rab' => []
        ],
        6 => [
            'id' => 6, 'nama' => 'Seminar Expektik', 'pengusul' => 'Bambang (NIM), Prodi', 'status' => 'Ditolak', 
            'kode_mak' => '123.45.88', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'Bambang (NIM), Prodi', 'nama_kegiatan' => 'Seminar Expektik', 'gambaran_umum' => '...','penerima_manfaat' => '...'],
            'iku' => ['Prestasi'],
            'indikator' => [],
            'rab' => []
        ],
    ];

    
    /**
     * Menampilkan halaman LIST usulan yang perlu ditelaah.
     * Dipanggil oleh rute: /verifikator/pengajuan-telaah
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data dari Model ---
        // $list_usulan = $this->usulanModel->getUsulanUntukVerifikasi(); // (Query: WHERE status='Menunggu' OR status='Telah Direvisi')
        
        // Filter data dummy (HANYA 'Menunggu' dan 'Telah Direvisi')
        $list_usulan_dummy = [];
        foreach ($this->list_usulan_all as $item) {
            if (strtolower($item['status']) === 'menunggu' || strtolower($item['status']) === 'telah direvisi') {
                $list_usulan_dummy[] = $item;
            }
        }
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan Telaah',
            'list_usulan' => $list_usulan_dummy 
        ]);

        // Gunakan view baru 'pengajuan_telaah.php' dan layout 'verifikator'
        $this->view('pages/verifikator/pengajuan_telaah', $data, 'verifikator');
    }

    /**
     * Menampilkan HALAMAN DETAIL untuk Verifikator menelaah KAK/RAB.
     * Dipanggil oleh rute: /verifikator/telaah/show/{id}
     */
    public function show($id, $data_dari_router = []) {
        
        // --- 1. Tentukan URL Kembali (Dinamis) ---
        $ref = $_GET['ref'] ?? 'telaah_list'; // Default kembali ke list telaah
        $base_url = "/docutrack/public/verifikator";
        $back_url = ($ref === 'dashboard')
        ? $base_url . '/dashboard'
        : (($ref === 'pengajuan-telaah')
            ? $base_url . '/pengajuan-telaah'
            : $base_url . '/riwayat-verifikasi');


        // --- 2. Ambil Data dari Master List (Simulasi) ---
        $usulan_dipilih = $this->list_usulan_all[$id] ?? null;
        
        if (!$usulan_dipilih) {
            return not_found("Usulan dengan ID $id tidak ditemukan.");
        }
        
        $status = $usulan_dipilih['status'];
        // --- Akhir Simulasi ---

        // 3. Kirim data (TERMASUK ROLE & STATUS) ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Telaah Usulan - ' . htmlspecialchars($usulan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'verifikator', // <-- Mengirim 'verifikator'
            
            // Data Payload
            'kegiatan_data' => $usulan_dipilih['kak'],
            'iku_data' => $usulan_dipilih['iku'],
            'indikator_data' => $usulan_dipilih['indikator'],
            'rab_data' => $usulan_dipilih['rab'],
            'kode_mak' => $usulan_dipilih['kode_mak'],
            'komentar_revisi' => $usulan_dipilih['komentar'],
            'komentar_penolakan' => $usulan_dipilih['komentar_penolakan'] ?? '',
            'back_url' => $back_url
        ]);

        // Panggil view 'detail_kegiatan' (yang cerdas)
        // dan gunakan layout 'verifikator'
        $this->view('pages/verifikator/telaah_detail', $data, 'verifikator');
    }
}