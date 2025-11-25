<?php
// File: src/controllers/Verifikator/DashboardController.php

require_once '../src/core/Controller.php';

class VerifikatorDashboardController extends Controller {
    
    /**
     * --- DATA MASTER DUMMY (STRUKTUR PNJ) ---
     * 'jurusan' = Induk (Digunakan untuk Filter)
     * 'prodi'   = Spesifik (Digunakan untuk Tampilan)
     */
    private $list_usulan_all = [
        // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER
        [
            'id' => 1, 'nama' => 'Seminar Nasional Teknologi', 'pengusul' => 'Ahmad Rizki', 'nim' => '2201001', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
            'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-01-15'
        ],
        [
            'id' => 2, 'nama' => 'Workshop AI & Machine Learning', 'pengusul' => 'Siti Nurhaliza', 'nim' => '2201002', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 
            'status' => 'Revisi', 'tanggal_pengajuan' => '2024-01-18'
        ],
        [
            'id' => 13, 'nama' => 'Workshop Video Editing', 'pengusul' => 'Muhammad Iqbal', 'nim' => '2201003', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 
            'status' => 'Revisi', 'tanggal_pengajuan' => '2024-02-22'
        ],

        // JURUSAN: TEKNIK ELEKTRO
        [
            'id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'Budi Santoso', 'nim' => '2202001', 
            'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 
            'status' => 'Telah Direvisi', 'tanggal_pengajuan' => '2024-01-20'
        ],
        [
            'id' => 15, 'nama' => 'Bakti Sosial Ramadhan', 'pengusul' => 'Oktavia Ningsih', 'nim' => '2202002', 
            'jurusan' => 'Teknik Elektro', 'prodi' => 'Broadband Multimedia', 
            'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-28'
        ],

        // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER (Prodi Lain - TIK biasanya menaungi TI, TMD, TMJ, TICK)
        [
            'id' => 4, 'nama' => 'Pelatihan Web Development', 'pengusul' => 'Dewi Lestari', 'nim' => '2203001', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknologi Industri Cetak Kemasan', 
            'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-01-22'
        ],
        [
            'id' => 5, 'nama' => 'Turnamen E-Sport Kampus', 'pengusul' => 'Eko Prasetyo', 'nim' => '2203002', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
            'status' => 'Ditolak', 'tanggal_pengajuan' => '2024-01-25'
        ],
        [
            'id' => 14, 'nama' => 'Donor Darah Bersama', 'pengusul' => 'Nanda Pratama', 'nim' => '2203003', 
            'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 
            'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-25'
        ],

        // JURUSAN: AKUNTANSI
        [
            'id' => 6, 'nama' => 'Seminar Kewirausahaan', 'pengusul' => 'Fitri Handayani', 'nim' => '2204001', 
            'jurusan' => 'Akuntansi', 'prodi' => 'Akuntansi Keuangan', 
            'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-01'
        ],
        [
            'id' => 7, 'nama' => 'Pelatihan Dasar Akuntansi UMKM', 'pengusul' => 'Rina Wati', 'nim' => '2204002', 
            'jurusan' => 'Akuntansi', 'prodi' => 'Keuangan dan Perbankan', 
            'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-05'
        ],
        [
            'id' => 9, 'nama' => 'Webinar Digital Marketing', 'pengusul' => 'Intan Permata', 'nim' => '2204003', 
            'jurusan' => 'Akuntansi', 'prodi' => 'Manajemen Keuangan', 
            'status' => 'Telah Direvisi', 'tanggal_pengajuan' => '2024-02-10'
        ],

        // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN
        [
            'id' => 8, 'nama' => 'Workshop Fotografi', 'pengusul' => 'Qori Amanda', 'nim' => '2206002', 
            'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 
            'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-01'
        ],
        [
            'id' => 12, 'nama' => 'Pelatihan Public Speaking', 'pengusul' => 'Linda Wijaya', 'nim' => '2206001', 
            'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Penerbitan', 
            'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-20'
        ],

        // JURUSAN: TEKNIK MESIN
        [
            'id' => 10, 'nama' => 'Pelatihan Microsoft Office', 'pengusul' => 'Joko Susilo', 'nim' => '2205001', 
            'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Mesin', 
            'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-15'
        ],
        [
            'id' => 11, 'nama' => 'Lomba Karya Tulis Ilmiah', 'pengusul' => 'Kartika Sari', 'nim' => '2205002', 
            'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Konversi Energi', 
            'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-18'
        ],
    ];
    
    public function index($data_dari_router = []) {
        
        $list_usulan = $this->list_usulan_all;
        
        // Hitung statistik
        $total = count($list_usulan);
        $disetujui = count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'disetujui'));
        $ditolak = count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'ditolak'));
        $pending = count(array_filter($list_usulan, function($u) {
            $s = strtolower($u['status']);
            return $s === 'menunggu' || $s === 'telah direvisi';
        }));
        
        $stats = [
            'total' => $total,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
            'pending' => $pending
        ];
        
        // Daftar Jurusan Unik (Untuk Dropdown Filter)
        $jurusan_list = array_unique(array_column($list_usulan, 'jurusan'));
        sort($jurusan_list);
        
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