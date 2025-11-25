<?php
// File: src/controllers/Admin/DashboardController.php

require_once '../src/core/Controller.php';

class AdminDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Data Stats (akan dihitung otomatis dari data KAK)
        
        // 2. Data Progres Bar - Alur KAK
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC PPK', 'ACC WD', 'Dana Cair'];
        $tahap_sekarang_kak = 'ACC WD'; // Tahap saat ini di alur KAK
        $icons_kak = [ 
            'Pengajuan' => 'fa-file-alt', 
            'Validasi' => 'fa-check-double', 
            'ACC PPK' => 'fa-stamp', 
            'ACC WD' => 'fa-user-check', 
            'Dana Cair' => 'fa-wallet'
        ];
        
        // 3. Data Progres Bar - Alur LPJ
        $tahapan_lpj = ['Upload Bukti', 'Validasi', 'ACC Bendahara', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi'; // Tahap saat ini di alur LPJ
        $icons_lpj = [ 
            'Upload Bukti' => 'fa-upload', 
            'Validasi' => 'fa-check-double', 
            'ACC Bendahara' => 'fa-file-invoice-dollar', 
            'Selesai' => 'fa-flag-checkered' 
        ];

        // 4. Data List KAK - Data dummy dengan PRODI dan JURUSAN
        $list_kak_dummy = [
            [
                'id' => 101,
                'nama' => 'Seminar Nasional Artificial Intelligence 2025',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'status' => 'Menunggu',
                'pengusul' => 'Himpunan Mahasiswa TI',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('now'))
            ],
            [
                'id' => 102,
                'nama' => 'Workshop UI/UX Design Fundamental',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '461701002',
                'prodi' => 'D4 Teknik Grafika dan Penerbitan',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'status' => 'Disetujui',
                'pengusul' => 'UKM Multimedia',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 days'))
            ],
            [
                'id' => 103,
                'nama' => 'Studi Banding ke Startup Unicorn',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '441701003',
                'prodi' => 'D4 Sistem Informasi Kota Cerdas',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'status' => 'Revisi',
                'pengusul' => 'BEM Fakultas',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-5 days'))
            ],
            [
                'id' => 104,
                'nama' => 'Turnamen E-Sport Kampus',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '431701004',
                'prodi' => 'D4 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'status' => 'Ditolak',
                'pengusul' => 'Komunitas Gaming',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 week'))
            ],
            [
                'id' => 105,
                'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
                'nama_mahasiswa' => 'Rina Wati',
                'nim' => '421701005',
                'prodi' => 'D3 Akuntansi',
                'jurusan' => 'Akuntansi',
                'status' => 'Menunggu',
                'pengusul' => 'Hima Akuntansi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-10 days'))
            ],
            [
                'id' => 106,
                'nama' => 'Pameran Seni Rupa Digital',
                'nama_mahasiswa' => 'Doni Irawan',
                'nim' => '461701006',
                'prodi' => 'D3 Desain Grafis',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'status' => 'Disetujui',
                'pengusul' => 'UKM Seni',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 weeks'))
            ],
            [
                'id' => 107,
                'nama' => 'Lomba Business Plan Competition',
                'nama_mahasiswa' => 'Maya Putri',
                'nim' => '431701007',
                'prodi' => 'D4 Manajemen Pemasaran',
                'jurusan' => 'Administrasi Niaga',
                'status' => 'Disetujui',
                'pengusul' => 'Hima Manajemen',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-3 days'))
            ],
            [
                'id' => 108,
                'nama' => 'Workshop Cyber Security',
                'nama_mahasiswa' => 'Rizky Firmansyah',
                'nim' => '481701008',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'status' => 'Menunggu',
                'pengusul' => 'UKM Teknologi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 day'))
            ],
            [
                'id' => 109,
                'nama' => 'Pelatihan Digital Marketing',
                'nama_mahasiswa' => 'Fitri Handayani',
                'nim' => '431701009',
                'prodi' => 'D3 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'status' => 'Revisi',
                'pengusul' => 'BEM Fakultas',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-4 days'))
            ],
            [
                'id' => 110,
                'nama' => 'Seminar Perpajakan Modern',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '421701010',
                'prodi' => 'D4 Akuntansi Manajerial',
                'jurusan' => 'Akuntansi',
                'status' => 'Disetujui',
                'pengusul' => 'Hima Akuntansi',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-6 days'))
            ],
            [
                'id' => 111,
                'nama' => 'Workshop Internet of Things',
                'nama_mahasiswa' => 'Dewi Lestari',
                'nim' => '451701001',
                'prodi' => 'D4 Teknik Elektronika',
                'jurusan' => 'Teknik Elektro',
                'status' => 'Menunggu',
                'pengusul' => 'Lab Embedded System',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-8 days'))
            ],
            [
                'id' => 112,
                'nama' => 'Pelatihan CAD Design untuk Mesin',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '411701001',
                'prodi' => 'D4 Teknik Mesin',
                'jurusan' => 'Teknik Mesin',
                'status' => 'Disetujui',
                'pengusul' => 'Hima Mesin',
                'tanggal_pengajuan' => date('Y-m-d', strtotime('-12 days'))
            ],
        ];
        
        // 5. Data List LPJ - Sesuai dengan struktur yang benar
        $list_lpj_dummy = [
            // LPJ yang sudah disetujui bendahara (16 hari lalu - deadline terlewat)
            [
                'id' => 1,
                'nama' => 'Seminar Nasional Teknologi AI',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '190101001',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-16 days')),
                'status' => 'Setuju'
            ],
            
            // LPJ yang perlu revisi dari bendahara
            [
                'id' => 2,
                'nama' => 'Workshop UI/UX Design 2024',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '190101002',
                'prodi' => 'D4 Teknik Grafika dan Penerbitan',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'status' => 'Revisi'
            ],
            
            // LPJ menunggu verifikasi bendahara
            [
                'id' => 3,
                'nama' => 'Lomba Robotika Nasional',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '190101003',
                'prodi' => 'D4 Teknik Telekomunikasi',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'status' => 'Menunggu'
            ],
            
            // LPJ yang masih perlu upload bukti
            [
                'id' => 4,
                'nama' => 'Pentas Seni dan Kewirausahaan',
                'nama_mahasiswa' => 'Dewi Lestari',
                'nim' => '190101004',
                'prodi' => 'D4 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => 'Menunggu_Upload'
            ],
            
            // LPJ lain yang perlu upload bukti
            [
                'id' => 5,
                'nama' => 'Kunjungan Industri Manufaktur',
                'nama_mahasiswa' => 'Riko Saputra',
                'nim' => '190101005',
                'prodi' => 'D3 Teknik Mesin',
                'jurusan' => 'Teknik Mesin',
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'status' => 'Menunggu_Upload'
            ],
            
            // LPJ yang sudah disetujui (10 hari lalu)
            [
                'id' => 6,
                'nama' => 'Lomba Coding Tingkat Kampus',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '190101006',
                'prodi' => 'D4 Sistem Informasi Kota Cerdas',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'status' => 'Setuju'
            ],
            
            // LPJ baru perlu upload
            [
                'id' => 7,
                'nama' => 'Festival Musik Kampus',
                'nama_mahasiswa' => 'Linda Sari',
                'nim' => '190101007',
                'prodi' => 'D3 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'status' => 'Menunggu_Upload'
            ],
            
            // LPJ dalam proses validasi
            [
                'id' => 8,
                'nama' => 'Bakti Sosial Lingkungan',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '190101008',
                'prodi' => 'D4 Teknik Elektronika',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'status' => 'Menunggu'
            ],
        ];
        
        // Hitung statistik dari data KAK
        $total = count($list_kak_dummy);
        $disetujui = count(array_filter($list_kak_dummy, fn($item) => strtolower($item['status']) === 'disetujui'));
        $ditolak = count(array_filter($list_kak_dummy, fn($item) => strtolower($item['status']) === 'ditolak'));
        $menunggu = count(array_filter($list_kak_dummy, fn($item) => strtolower($item['status']) === 'menunggu'));
        
        $stats = [
            'total' => $total,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
            'menunggu' => $menunggu
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'tahapan_kak' => $tahapan_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,
            'icons_kak' => $icons_kak,
            'tahapan_lpj' => $tahapan_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj,
            'icons_lpj' => $icons_lpj,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/admin/dashboard', $data, 'app'); 
    }
}