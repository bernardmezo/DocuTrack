<?php
// File: src/controllers/bendahara/RiwayatverifikasiController.php

require_once '../src/core/Controller.php';

class BendaharaRiwayatverifikasiController extends Controller {
    
    /**
     * Menampilkan halaman Riwayat Verifikasi Bendahara.
     * Mengambil semua usulan yang telah diproses (Dana Diberikan, Revisi/Ditolak).
     */
    public function index($data_dari_router = []) { 
        
        // Data dummy diperbarui dengan struktur PNJ (Jurusan & Prodi)
        // 'jurusan' = Induk (Untuk Filter)
        // 'prodi'   = Spesifik (Untuk Tampilan)
        $list_riwayat_dummy = [
            [
                'id' => 1,
                'nama' => 'Workshop Teknologi Web Modern',
                'pengusul' => 'Ahmad Rizki',
                'nim' => '2021001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Informatika',
                'tanggal_pengajuan' => '2024-01-15',
                'tgl_verifikasi' => '2024-01-20',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 2,
                'nama' => 'Seminar Kewirausahaan Digital',
                'pengusul' => 'Siti Nurhaliza',
                'nim' => '2021002',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'Administrasi Bisnis',
                'tanggal_pengajuan' => '2024-01-16',
                'tgl_verifikasi' => '2024-01-21',
                'status' => 'Revisi'
            ],
            [
                'id' => 3,
                'nama' => 'Pelatihan UI/UX Design',
                'pengusul' => 'Budi Santoso',
                'nim' => '2021003',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'Desain Grafis',
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
                'prodi' => 'Keuangan dan Perbankan',
                'tanggal_pengajuan' => '2024-01-18',
                'tgl_verifikasi' => '2024-01-23',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 5,
                'nama' => 'Workshop Cyber Security',
                'pengusul' => 'Eko Prasetyo',
                'nim' => '2021005',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Multimedia Jaringan',
                'tanggal_pengajuan' => '2024-01-19',
                'tgl_verifikasi' => '2024-01-24',
                'status' => 'Revisi'
            ],
            [
                'id' => 6,
                'nama' => 'Seminar MICE International',
                'pengusul' => 'Fitri Amelia',
                'nim' => '2021006',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'MICE',
                'tanggal_pengajuan' => '2024-01-20',
                'tgl_verifikasi' => '2024-01-25',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 7,
                'nama' => 'Pelatihan Data Science',
                'pengusul' => 'Gita Permata',
                'nim' => '2021007',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Multimedia Digital',
                'tanggal_pengajuan' => '2024-01-21',
                'tgl_verifikasi' => '2024-01-26',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 8,
                'nama' => 'Workshop Fotografi Produk',
                'pengusul' => 'Hendra Wijaya',
                'nim' => '2021008',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'Penerbitan',
                'tanggal_pengajuan' => '2024-01-22',
                'tgl_verifikasi' => '2024-01-27',
                'status' => 'Revisi'
            ],
            [
                'id' => 9,
                'nama' => 'Rancang Bangun Konstruksi',
                'pengusul' => 'Indra Kusuma',
                'nim' => '2021009',
                'jurusan' => 'Teknik Sipil',
                'prodi' => 'Konstruksi Gedung',
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
                'prodi' => 'Akuntansi Keuangan',
                'tanggal_pengajuan' => '2024-01-24',
                'tgl_verifikasi' => '2024-01-29',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 11,
                'nama' => 'Workshop Mobile App Development',
                'pengusul' => 'Kartika Sari',
                'nim' => '2021011',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Informatika',
                'tanggal_pengajuan' => '2024-01-25',
                'tgl_verifikasi' => '2024-01-30',
                'status' => 'Revisi'
            ],
            [
                'id' => 12,
                'nama' => 'Seminar English for Business',
                'pengusul' => 'Linda Wijayanti',
                'nim' => '2021012',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'Bahasa Inggris untuk Komunikasi Bisnis',
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