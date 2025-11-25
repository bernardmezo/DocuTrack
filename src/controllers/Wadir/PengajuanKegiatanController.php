<?php
// File: src/controllers/Wadir/PengajuanKegiatanController.php

require_once '../src/core/Controller.php';

class WadirPengajuanKegiatanController extends Controller {
    
    public function index($data_dari_router = []) { 
        
        // --- DATA DUMMY LENGKAP ---
        $list_usulan_all = [
            // JURUSAN: TEKNIK ELEKTRO
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'Budi Santoso', 'nim' => '2202001', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-01-20'],
            ['id' => 19, 'nama' => 'Lomba Robotik Nasional', 'pengusul' => 'Sandi Kurnia', 'nim' => '2202019', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Telekomunikasi', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-08'],

            // JURUSAN: TEKNIK MESIN
            ['id' => 5, 'nama' => 'Kunjungan Industri', 'pengusul' => 'Rahmat Hidayat', 'nim' => '2205005', 'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Konversi Energi', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-05'],
            ['id' => 11, 'nama' => 'Lomba Karya Tulis Ilmiah', 'pengusul' => 'Kartika Sari', 'nim' => '2205001', 'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Mesin', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-15'],
            ['id' => 21, 'nama' => 'Pekan Olahraga', 'pengusul' => 'Umar Faruq', 'nim' => '2205002', 'jurusan' => 'Teknik Mesin', 'prodi' => 'Alat Berat', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-12'],

            // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN
            ['id' => 7, 'nama' => 'Pameran Tugas Akhir', 'pengusul' => 'Bagas Saputra', 'nim' => '2206007', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Teknik Grafika', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-01'],
            ['id' => 10, 'nama' => 'Lomba Desain Poster', 'pengusul' => 'Kevin Sanjaya', 'nim' => '2206010', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-12'],
            ['id' => 17, 'nama' => 'Workshop Fotografi', 'pengusul' => 'Qori Amanda', 'nim' => '2206002', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Penerbitan', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-01'],

            // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER
            ['id' => 8, 'nama' => 'Workshop IoT Dasar', 'pengusul' => 'Dina Marlina', 'nim' => '2201008', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-05'],
            ['id' => 14, 'nama' => 'Donor Darah Bersama', 'pengusul' => 'Nanda Pratama', 'nim' => '2203003', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-22'],

            // JURUSAN: AKUNTANSI
            ['id' => 9, 'nama' => 'Webinar Digital Marketing', 'pengusul' => 'Intan Permata', 'nim' => '2204002', 'jurusan' => 'Akuntansi', 'prodi' => 'Keuangan dan Perbankan', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-10'],
            ['id' => 13, 'nama' => 'Seminar Audit Forensik', 'pengusul' => 'Mawar Melati', 'nim' => '2204013', 'jurusan' => 'Akuntansi', 'prodi' => 'Akuntansi', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-20'],

            // JURUSAN: ADMINISTRASI NIAGA
            ['id' => 16, 'nama' => 'Pentas Seni Kampus', 'pengusul' => 'Putri Ayu', 'nim' => '2207016', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'Administrasi Bisnis', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-28'],
        ];

        // Daftar jurusan untuk filter (ambil unique jurusan)
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        sort($jurusan_list);
        
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Persetujuan Kegiatan',
            'list_usulan' => $list_usulan_all, // KIRIM DATA FULL (TANPA PAGINATION PHP)
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/wadir/pengajuan_kegiatan', $data, 'wadir');
    }
}