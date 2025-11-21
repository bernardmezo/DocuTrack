<?php
// File: src/controllers/Bendahara/DashboardController.php

require_once '../src/core/Controller.php';
// (Nanti, load model Anda di sini)
// require_once '../src/models/Usulan.php'; 
// require_once '../src/models/Lpj.php'; 

class BendaharaDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---

        // 1. Data Stats (Ringkasan)
        $stats = ['total' => 15, 'danaDiberikan' => 10, 'ditolak' => 2, 'menunggu' => 3];

        // 3. Data List KAK (SEMUA STATUS: Menunggu, Revisi, Disetujui, Ditolak)
        // $usulanModel = new Usulan();
        // $list_kak = $usulanModel->getAll();
        $list_kak_dummy = [
        [
        'id' => 101,
        'nama' => 'Seminar Nasional Artificial Intelligence 2025',
        'nama_mahasiswa' => 'Andi Pratama',
        'nim' => '481701001',
        'jurusan' => 'Teknik Informatika',
        'status' => 'Menunggu', // Test filter: Menunggu
        'pengusul' => 'Himpunan Mahasiswa TI'
    ],
    [
        'id' => 102,
        'nama' => 'Workshop UI/UX Design Fundamental',
        'nama_mahasiswa' => 'Siti Aminah',
        'nim' => '461701002',
        'jurusan' => 'Desain Grafis',
        'status' => 'Dana Diberikan', // Test filter: Disetujui (Warna Hijau)
        'pengusul' => 'UKM Multimedia'
    ],
    [
        'id' => 103,
        'nama' => 'Studi Banding ke Startup Unicorn',
        'nama_mahasiswa' => 'Budi Santoso',
        'nim' => '441701003',
        'jurusan' => 'Sistem Informasi',
        'status' => 'Revisi', // Test filter: Revisi (Warna Kuning)
        'pengusul' => 'BEM Fakultas'
    ],
    [
        'id' => 104,
        'nama' => 'Turnamen E-Sport Kampus',
        'nama_mahasiswa' => 'Joko Susilo',
        'nim' => '431701004',
        'jurusan' => 'Manajemen',
        'status' => 'Dana Diberikan', // Test filter: Ditolak (Warna Merah)
        'pengusul' => 'Komunitas Gaming'
    ],
    [
        'id' => 105,
        'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
        'nama_mahasiswa' => 'Rina Wati',
        'nim' => '421701005',
        'jurusan' => 'Akuntansi',
        'status' => 'Menunggu',
        'pengusul' => 'Hima Akuntansi'
    ],
    [
        'id' => 106,
        'nama' => 'Pameran Seni Rupa Digital',
        'nama_mahasiswa' => 'Doni Irawan',
        'nim' => '461701006',
        'jurusan' => 'Desain Grafis',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 days')),
        'status' => 'Dana Diberikan',
        'pengusul' => 'UKM Seni'
    ],
    ];
        
        // 4. Data List LPJ (SEMUA STATUS)
        // $lpjModel = new Lpj();
        // $list_lpj = $lpjModel->getAll();
        
        $list_lpj_dummy = [
    [
        'id' => 201,
        'nama' => 'Laporan Kegiatan Dies Natalis ke-50',
        'nama_mahasiswa' => 'Citra Dewi',
        'nim' => '481701010',
        'jurusan' => 'Teknik Informatika',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 days')), // Baru diajukan kemarin (Aman)
        'status' => 'Menunggu'
    ],
    [
        'id' => 202,
        'nama' => 'LPJ Workshop Python Data Science',
        'nama_mahasiswa' => 'Eko Purnomo',
        'nim' => '441701011',
        'jurusan' => 'Sistem Informasi',
        // Disetujui 10 hari lalu -> Deadline 14 hari -> Sisa 4 hari (Warna Biru/Hijau di JS)
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-10 days')), 
        'status' => 'Setuju'
    ],
    [
        'id' => 203,
        'nama' => 'LPJ Kunjungan Industri Jakarta',
        'nama_mahasiswa' => 'Fajar Nugraha',
        'nim' => '431701012',
        'jurusan' => 'Manajemen',
        // Disetujui 13 hari lalu -> Deadline 14 hari -> Sisa 1 hari (Warna Orange/Warning di JS)
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-13 days')), 
        'status' => 'Setuju'
    ],
    [
        'id' => 204,
        'nama' => 'LPJ Bakti Sosial Desa Binaan',
        'nama_mahasiswa' => 'Gita Gutawa',
        'nim' => '421701013',
        'jurusan' => 'Akuntansi',
        // Disetujui 20 hari lalu -> Deadline 14 hari -> Terlewat 6 hari (Warna Merah/Danger di JS)
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-20 days')), 
        'status' => 'Setuju'
    ],
    [
        'id' => 205,
        'nama' => 'LPJ Lomba Desain Poster Nasional',
        'nama_mahasiswa' => 'Hendra Gunawan',
        'nim' => '461701014',
        'jurusan' => 'Desain Grafis',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 days')),
        'status' => 'Revisi' // Status Revisi
    ],
    [
        'id' => 206,
        'nama' => 'LPJ Seminar Kewirausahaan',
        'nama_mahasiswa' => 'Indah Permata',
        'nim' => '431701015',
        'jurusan' => 'Manajemen',
        // Disetujui 14 hari lalu -> Deadline HARI INI (Warna Merah/Bell di JS)
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-14 days')), 
        'status' => 'Setuju'
    ],
    ];
        
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/bendahara/dashboard', $data, 'bendahara'); 
    }
}