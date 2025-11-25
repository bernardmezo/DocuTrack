<?php
// File: src/controllers/Verifikator/RiwayatController.php

require_once '../src/core/Controller.php';

class VerifikatorRiwayatController extends Controller {
    
    // Daftar Jurusan PNJ untuk Filter
    private $jurusan_list = [
        'Teknik Informatika dan Komputer',
        'Teknik Elektro',
        'Teknik Mesin',
        'Teknik Sipil',
        'Teknik Grafika dan Penerbitan',
        'Akuntansi',
        'Administrasi Niaga'
    ];

    public function index($data_dari_router = []) { 
        
        // --- DATA DUMMY LENGKAP (STRUKTUR PNJ) ---
        // 'jurusan' = Induk (Untuk Filter)
        // 'prodi'   = Anak (Untuk Tampilan)
        
        $list_riwayat_dummy = [
            // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER
            [
                'id' => 1, 'nama' => 'Seminar Nasional Teknologi', 'pengusul' => 'Ahmad Rizki', 'nim' => '2201001',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-01-16'
            ],
            [
                'id' => 2, 'nama' => 'Workshop AI & Machine Learning', 'pengusul' => 'Siti Nurhaliza', 'nim' => '2201002',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-01-19'
            ],
            [
                'id' => 4, 'nama' => 'Pelatihan Web Development', 'pengusul' => 'Dewi Lestari', 'nim' => '2203001',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-01-23'
            ],
            [
                'id' => 5, 'nama' => 'Turnamen E-Sport Kampus', 'pengusul' => 'Eko Prasetyo', 'nim' => '2203002',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
                'status' => 'Ditolak', 'tgl_verifikasi' => '2024-01-26'
            ],

            // JURUSAN: AKUNTANSI
            [
                'id' => 7, 'nama' => 'Pelatihan Dasar Akuntansi UMKM', 'pengusul' => 'Rina Wati', 'nim' => '2204002',
                'jurusan' => 'Akuntansi', 'prodi' => 'Keuangan dan Perbankan', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-06'
            ],
            [
                'id' => 9, 'nama' => 'Webinar Digital Marketing', 'pengusul' => 'Intan Permata', 'nim' => '2204003',
                'jurusan' => 'Akuntansi', 'prodi' => 'Manajemen Keuangan', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-02-11'
            ],

            // JURUSAN: TEKNIK MESIN
            [
                'id' => 10, 'nama' => 'Pelatihan Microsoft Office', 'pengusul' => 'Joko Susilo', 'nim' => '2205001',
                'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Mesin', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-16'
            ],

            // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN (Contoh Komunikasi masuk sini atau jurusan lain sesuai struktur PNJ lama/baru)
            // Asumsi: Komunikasi Pemasaran ada di Administrasi Niaga atau Penerbitan. Disini saya masukkan ke TGP -> Penerbitan
            [
                'id' => 11, 'nama' => 'Pelatihan Public Speaking', 'pengusul' => 'Maya Sari', 'nim' => '2206003',
                'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Penerbitan', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-03-06'
            ],

            // JURUSAN: TEKNIK ELEKTRO
            [
                'id' => 13, 'nama' => 'Workshop IoT & Robotika', 'pengusul' => 'Fahmi Kurniawan', 'nim' => '2202002',
                'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-03-11'
            ],

            // JURUSAN: AKUNTANSI (Lagi)
            [
                'id' => 14, 'nama' => 'Seminar Karir dan Bursa Kerja', 'pengusul' => 'Linda Permatasari', 'nim' => '2204004',
                'jurusan' => 'Akuntansi', 'prodi' => 'Akuntansi', 
                'status' => 'Revisi', 'tgl_verifikasi' => '2024-03-13'
            ],

            // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN
            [
                'id' => 15, 'nama' => 'Workshop Desain UI/UX', 'pengusul' => 'Reza Pratama', 'nim' => '2206004',
                'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-03-15'
            ],

            // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER (Lagi)
            [
                'id' => 16, 'nama' => 'Seminar Blockchain Technology', 'pengusul' => 'Fahmi Rahman', 'nim' => '2201005',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-03-18'
            ],

            // JURUSAN: TEKNIK ELEKTRO (Lagi)
            [
                'id' => 17, 'nama' => 'Workshop Robotika Dasar', 'pengusul' => 'Andi Wijaya', 'nim' => '2202003',
                'jurusan' => 'Teknik Elektro', 'prodi' => 'Broadband Multimedia', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-03-20'
            ],
            [
                'id' => 18, 'nama' => 'Lomba Startup Competition', 'pengusul' => 'Dina Mariana', 'nim' => '2204005',
                'jurusan' => 'Akuntansi', 'prodi' => 'Akuntansi Keuangan', 
                'status' => 'Ditolak', 'tgl_verifikasi' => '2024-03-22'
            ],

            // JURUSAN: TEKNIK MESIN (Lagi)
            [
                'id' => 20, 'nama' => 'Seminar Industrial Revolution 4.0', 'pengusul' => 'Bambang Setiawan', 'nim' => '2205002',
                'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Konversi Energi', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-03-26'
            ],
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat_dummy,
            'jurusan_list' => $this->jurusan_list
        ]);

        $this->view('pages/verifikator/riwayat_verifikasi', $data, 'verifikator');
    }
}