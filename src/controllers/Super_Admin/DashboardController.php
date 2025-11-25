<?php
// File: src/controllers/Super_Admin/DashboardController.php

require_once '../src/core/Controller.php';

class SuperadminDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Data Stats (Ringkasan)
        $stats = [
            'total' => 255, 
            'disetujui' => 10, 
            'ditolak' => 2, 
            'menunggu' => 3,
            'revisi' => 1
        ];

        // 2. Daftar Program Studi
        $list_prodi = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Desain Grafis',
            'Manajemen',
            'Akuntansi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Psikologi'
        ];

        // 3. Data List KAK dengan created_at untuk grafik
        $list_kak_dummy = [
            [
                'id' => 101,
                'nama' => 'Seminar Nasional Artificial Intelligence 2025',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'jurusan' => 'Teknik Informatika',
                'status' => 'menunggu',
                'pengusul' => 'Himpunan Mahasiswa TI',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'tanggal' => date('Y-m-d', strtotime('-2 hours'))
            ],
            [
                'id' => 102,
                'nama' => 'Workshop UI/UX Design Fundamental',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '461701002',
                'jurusan' => 'Desain Grafis',
                'status' => 'disetujui',
                'pengusul' => 'UKM Multimedia',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'tanggal' => date('Y-m-d', strtotime('-1 days'))
            ],
            [
                'id' => 103,
                'nama' => 'Studi Banding ke Startup Unicorn',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '441701003',
                'jurusan' => 'Sistem Informasi',
                'status' => 'revisi',
                'pengusul' => 'BEM Fakultas',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'tanggal' => date('Y-m-d', strtotime('-3 days'))
            ],
            [
                'id' => 104,
                'nama' => 'Turnamen E-Sport Kampus',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '431701004',
                'jurusan' => 'Manajemen',
                'status' => 'ditolak',
                'pengusul' => 'Komunitas Gaming',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'tanggal' => date('Y-m-d', strtotime('-5 days'))
            ],
            [
                'id' => 105,
                'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
                'nama_mahasiswa' => 'Rina Wati',
                'nim' => '421701005',
                'jurusan' => 'Akuntansi',
                'status' => 'menunggu',
                'pengusul' => 'Hima Akuntansi',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'tanggal' => date('Y-m-d', strtotime('-1 week'))
            ],
            [
                'id' => 106,
                'nama' => 'Pameran Seni Rupa Digital',
                'nama_mahasiswa' => 'Doni Irawan',
                'nim' => '461701006',
                'jurusan' => 'Desain Grafis',
                'status' => 'disetujui',
                'pengusul' => 'UKM Seni',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
                'tanggal' => date('Y-m-d', strtotime('-2 weeks'))
            ],
            [
                'id' => 107,
                'nama' => 'Lomba Karya Tulis Ilmiah',
                'nama_mahasiswa' => 'Lisa Andriani',
                'nim' => '481701007',
                'jurusan' => 'Teknik Informatika',
                'status' => 'disetujui',
                'pengusul' => 'BEM Universitas',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 month')),
                'tanggal' => date('Y-m-d', strtotime('-1 month'))
            ],
            [
                'id' => 108,
                'nama' => 'Seminar Cybersecurity',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '481701008',
                'jurusan' => 'Teknik Informatika',
                'status' => 'menunggu',
                'pengusul' => 'Himpunan TI',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 months')),
                'tanggal' => date('Y-m-d', strtotime('-2 months'))
            ],
        ];
        
        // 4. Data List LPJ dengan created_at untuk grafik
        $list_lpj_dummy = [
            [
                'id' => 201,
                'nama' => 'Laporan Kegiatan Dies Natalis ke-50',
                'nama_mahasiswa' => 'Citra Dewi',
                'nim' => '481701010',
                'jurusan' => 'Teknik Informatika',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 days')),
                'status' => 'menunggu',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'tanggal' => date('Y-m-d', strtotime('-1 days'))
            ],
            [
                'id' => 202,
                'nama' => 'LPJ Workshop Python Data Science',
                'nama_mahasiswa' => 'Eko Purnomo',
                'nim' => '441701011',
                'jurusan' => 'Sistem Informasi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-10 days')),
                'status' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'tanggal' => date('Y-m-d', strtotime('-10 days'))
            ],
            [
                'id' => 203,
                'nama' => 'LPJ Kunjungan Industri Jakarta',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '431701012',
                'jurusan' => 'Manajemen',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-13 days')),
                'status' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-13 days')),
                'tanggal' => date('Y-m-d', strtotime('-13 days'))
            ],
            [
                'id' => 204,
                'nama' => 'LPJ Bakti Sosial Desa Binaan',
                'nama_mahasiswa' => 'Gita Gutawa',
                'nim' => '421701013',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-20 days')),
                'status' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
                'tanggal' => date('Y-m-d', strtotime('-20 days'))
            ],
            [
                'id' => 205,
                'nama' => 'LPJ Lomba Desain Poster Nasional',
                'nama_mahasiswa' => 'Hendra Gunawan',
                'nim' => '461701014',
                'jurusan' => 'Desain Grafis',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 days')),
                'status' => 'revisi',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'tanggal' => date('Y-m-d', strtotime('-2 days'))
            ],
            [
                'id' => 206,
                'nama' => 'LPJ Seminar Kewirausahaan',
                'nama_mahasiswa' => 'Indah Permata',
                'nim' => '431701015',
                'jurusan' => 'Manajemen',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-14 days')),
                'status' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
                'tanggal' => date('Y-m-d', strtotime('-14 days'))
            ],
            [
                'id' => 207,
                'nama' => 'LPJ Pelatihan Public Speaking',
                'nama_mahasiswa' => 'Joko Widodo',
                'nim' => '421701016',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 month')),
                'status' => 'ditolak',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 month')),
                'tanggal' => date('Y-m-d', strtotime('-1 month'))
            ],
            [
                'id' => 208,
                'nama' => 'LPJ Study Tour Bali',
                'nama_mahasiswa' => 'Kartika Sari',
                'nim' => '461701017',
                'jurusan' => 'Desain Grafis',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 months')),
                'status' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 months')),
                'tanggal' => date('Y-m-d', strtotime('-2 months'))
            ],
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_prodi' => $list_prodi,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/Super_Admin/dashboard', $data, 'super_admin'); 
    }
}