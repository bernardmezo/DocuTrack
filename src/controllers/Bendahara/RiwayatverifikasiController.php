<?php
// File: src/controllers/bendahara/RiwayatverifikasiController.php

// Pastikan ini ada
require_once '../src/core/Controller.php';
// require_once '../src/models/Usulan.php'; // (Nanti load model Anda)

class BendaharaRiwayatverifikasiController extends Controller {
    
    /**
     * Menampilkan halaman Riwayat Verifikasi Wadir.
     * Mengambil semua usulan yang telah diproses (Disetujui, Ditolak).
     */
    public function index($data_dari_router = []) { 
        
        // --- TODO: Ganti dengan data asli dari Model ---
        // $usulanModel = new Usulan();
        // $list_riwayat = $usulanModel->getRiwayatWadir(); // (Query: WHERE status='Disetujui' OR status='Ditolak')
        
        // Data dummy (HANYA 'Disetujui' dan 'Ditolak')
        $list_riwayat_dummy = [
            [
            'id' => 1,
            'nama' => 'Workshop Teknologi Web Modern',
            'pengusul' => 'Ahmad Rizki',
            'nim' => '2021001',
            'jurusan' => 'Teknik Informatika',
            'tanggal_pengajuan' => '2024-01-15',
            'tgl_verifikasi' => '2024-01-20',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 2,
            'nama' => 'Seminar Kewirausahaan Digital',
            'pengusul' => 'Siti Nurhaliza',
            'nim' => '2021002',
            'jurusan' => 'Manajemen',
            'tanggal_pengajuan' => '2024-01-16',
            'tgl_verifikasi' => '2024-01-21',
            'status' => 'Revisi'
        ],
        [
            'id' => 3,
            'nama' => 'Pelatihan UI/UX Design',
            'pengusul' => 'Budi Santoso',
            'nim' => '2021003',
            'jurusan' => 'Desain Grafis',
            'tanggal_pengajuan' => '2024-01-17',
            'tgl_verifikasi' => '2024-01-22',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 4,
            'nama' => 'Kompetisi Business Plan',
            'pengusul' => 'Dewi Lestari',
            'nim' => '2021004',
            'jurusan' => 'Akuntansi',
            'tanggal_pengajuan' => '2024-01-18',
            'tgl_verifikasi' => '2024-01-23',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 5,
            'nama' => 'Workshop Cyber Security',
            'pengusul' => 'Eko Prasetyo',
            'nim' => '2021005',
            'jurusan' => 'Sistem Informasi',
            'tanggal_pengajuan' => '2024-01-19',
            'tgl_verifikasi' => '2024-01-24',
            'status' => 'Revisi'
        ],
        [
            'id' => 6,
            'nama' => 'Seminar Digital Marketing',
            'pengusul' => 'Fitri Amelia',
            'nim' => '2021006',
            'jurusan' => 'Manajemen',
            'tanggal_pengajuan' => '2024-01-20',
            'tgl_verifikasi' => '2024-01-25',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 7,
            'nama' => 'Pelatihan Data Science',
            'pengusul' => 'Gita Permata',
            'nim' => '2021007',
            'jurusan' => 'Teknik Informatika',
            'tanggal_pengajuan' => '2024-01-21',
            'tgl_verifikasi' => '2024-01-26',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 8,
            'nama' => 'Workshop Fotografi Produk',
            'pengusul' => 'Hendra Wijaya',
            'nim' => '2021008',
            'jurusan' => 'Desain Grafis',
            'tanggal_pengajuan' => '2024-01-22',
            'tgl_verifikasi' => '2024-01-27',
            'status' => 'Revisi'
        ],
        [
            'id' => 9,
            'nama' => 'Kompetisi Hackathon 2024',
            'pengusul' => 'Indra Kusuma',
            'nim' => '2021009',
            'jurusan' => 'Sistem Informasi',
            'tanggal_pengajuan' => '2024-01-23',
            'tgl_verifikasi' => '2024-01-28',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 10,
            'nama' => 'Pelatihan Analisis Laporan Keuangan',
            'pengusul' => 'Joko Widodo',
            'nim' => '2021010',
            'jurusan' => 'Akuntansi',
            'tanggal_pengajuan' => '2024-01-24',
            'tgl_verifikasi' => '2024-01-29',
            'status' => 'Dana Diberikan'
        ],
        [
            'id' => 11,
            'nama' => 'Workshop Mobile App Development',
            'pengusul' => 'Kartika Sari',
            'nim' => '2021011',
            'jurusan' => 'Teknik Informatika',
            'tanggal_pengajuan' => '2024-01-25',
            'tgl_verifikasi' => '2024-01-30',
            'status' => 'Revisi'
        ],
        [
            'id' => 12,
            'nama' => 'Seminar Public Speaking',
            'pengusul' => 'Linda Wijayanti',
            'nim' => '2021012',
            'jurusan' => 'Manajemen',
            'tanggal_pengajuan' => '2024-01-26',
            'tgl_verifikasi' => '2024-01-31',
            'status' => 'Dana Diberikan'
        ]
        ];
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat_dummy 
        ]);

        $this->view('pages/bendahara/riwayat-verifikasi', $data, 'bendahara');
    }
}