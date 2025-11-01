<?php
// File: src/controllers/Admin/AdminPengajuanKegiatanController.php

// 1. Memuat Controller inti
require_once '../src/core/Controller.php';
// 2. (Nanti) Muat Model Anda di sini
// require_once '../src/models/Usulan.php'; 

class AdminPengajuanKegiatanController extends Controller {
    
    /**
     * --- SIMULASI DATABASE MASTER (KONSISTEN) ---
     * Ini adalah "sumber kebenaran" yang akan digunakan oleh SEMUA method di controller ini.
     * Nanti, Anda akan mengganti ini dengan panggilan ke Model Anda.
     */
    private $list_kegiatan_all = [
        1 => [
            'id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'User A', 'status' => 'Disetujui', 
            'kode_mak' => '123.45.67', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'User A', 'nama_kegiatan' => 'Seminar Nasional', 'gambaran_umum' => 'Gambaran seminar...', 'penerima_manfaat' => 'Mahasiswa'],
            'iku' => ['Mendapat Pekerjaan'],
            'indikator' => [['bulan' => 'Oktober', 'nama' => 'Peserta Hadir', 'target' => 100]],
            'rab' => ['Belanja Barang' => [['id'=>1, 'uraian'=>'Snack', 'rincian'=>'Box', 'volume'=>100, 'satuan'=>'Box', 'harga'=>15000]]]
        ],
        2 => [
            'id' => 2, 'nama' => 'Workshop BEM', 'pengusul' => 'User B', 'status' => 'Revisi', 
            'kode_mak' => '', 'komentar' => ['rab_belanja_jasa' => 'Harga sewa sound system terlalu mahal.'], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'User B', 'nama_kegiatan' => 'Workshop BEM', 'gambaran_umum' => 'Gambaran workshop...', 'penerima_manfaat' => 'Anggota BEM'],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'November', 'nama' => 'Peserta Workshop', 'target' => 50]],
            'rab' => ['Belanja Jasa' => [['id'=>2, 'uraian'=>'Sewa Sound System', 'rincian'=>'Sewa 1 hari', 'volume'=>1, 'satuan'=>'Hari', 'harga'=>500000]]]
        ],
        3 => [
            'id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'User C', 'status' => 'Menunggu', 
            'kode_mak' => '', 'komentar' => [], 'komentar_penolakan' => '',
            'kak' => ['nama_pengusul' => 'User C', 'nama_kegiatan' => 'Lomba Cerdas Cermat', 'gambaran_umum' => 'Gambaran lomba...', 'penerima_manfaat' => 'Siswa SMA'],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'Desember', 'nama' => 'Jumlah Tim', 'target' => 30]],
            'rab' => ['Belanja Hadiah' => [['id'=>4, 'uraian'=>'Piala', 'rincian'=>'Set Piala', 'volume'=>3, 'satuan'=>'Set', 'harga'=>250000]]]
        ],
        4 => [
            'id' => 4, 'nama' => 'Kulum', 'pengusul' => 'User D', 'status' => 'Ditolak', 
            'kode_mak' => '', 'komentar' => [], 'komentar_penolakan' => 'Anggaran tidak sesuai SBM. Harap ajukan ulang.',
            'kak' => ['nama_pengusul' => 'User D', 'nama_kegiatan' => 'Kulum', 'gambaran_umum' => 'Gambaran Kulum...', 'penerima_manfaat' => 'Umum'],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [['bulan' => 'September', 'nama' => 'Jumlah Audiens', 'target' => 50]],
            'rab' => ['Belanja Transport' => [['id'=>5, 'uraian'=>'Bensin', 'rincian'=>'Mobil Operasional', 'volume'=>1, 'satuan'=>'Paket', 'harga'=>300000]]]
        ]
    ];


    /**
     * Menampilkan HALAMAN LIST (Hanya yang Disetujui)
     * Dipanggil oleh rute: /admin/pengajuan-kegiatan
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data dari Model ---
        // $usulanModel = new Usulan();
        // $list_kegiatan = $usulanModel->getByStatus('Disetujui');
        
        // Filter data dummy (Hanya 'Disetujui')
        $list_kegiatan_dummy = [];
        foreach ($this->list_kegiatan_all as $item) {
            if (strtolower($item['status']) === 'disetujui') {
                $list_kegiatan_dummy[] = $item;
            }
        }
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => $list_kegiatan_dummy 
        ]);

        // Memanggil view list dan layout 'app' (Admin)
        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    /**
     * Menampilkan HALAMAN DETAIL (untuk Admin)
     * Dipanggil oleh rute: /admin/pengajuan-kegiatan/show/{id}
     * View ini akan menangani status 'Revisi' (untuk diedit) atau 'Disetujui' (read-only + print)
     */
    public function show($id, $data_dari_router = []) {
        
        // --- 1. Tentukan URL Kembali (Dinamis) ---
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        // --- 2. Ambil Data dari Master List ---
        // $usulanModel = new Usulan();
        // $kegiatan_dipilih = $usulanModel->getFullDetailById($id);
        
        $kegiatan_dipilih = $this->list_kegiatan_all[$id] ?? null;
        
        if (!$kegiatan_dipilih) {
            // Jika ID tidak ada di master list, mungkin default ke status lain
            // Untuk testing 'Ditolak' dari dashboard, pastikan ID 4 ada di master list
             return not_found("Kegiatan dengan ID $id tidak ditemukan.");
        }
        
        $status = $kegiatan_dipilih['status'];
        // --- Akhir Pengambilan Data ---

        // 3. Kirim data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'admin', // <-- Role adalah Admin
            'kegiatan_data' => $kegiatan_dipilih['kak'],
            'iku_data' => $kegiatan_dipilih['iku'],
            'indikator_data' => $kegiatan_dipilih['indikator'],
            'rab_data' => $kegiatan_dipilih['rab'],
            'kode_mak' => $kegiatan_dipilih['kode_mak'],
            'komentar_revisi' => $kegiatan_dipilih['komentar'],
            'komentar_penolakan' => $kegiatan_dipilih['komentar_penolakan'] ?? '',
            'back_url' => $back_url
        ]);

        // Panggil view 'detail_kegiatan' milik Admin dengan layout 'app'
        $this->view('pages/admin/detail_kegiatan', $data, 'app');
    }
}