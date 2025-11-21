<?php
// File: src/controllers/Bendahara/PengajuanlpjController.php

require_once '../src/core/Controller.php';
// (Nanti, load model Anda di sini)
// require_once '../src/models/Usulan.php'; 
// require_once '../src/models/Lpj.php'; 

class BendaharaPengajuanlpjController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- TODO: Ganti dengan data asli dari Model ---

        // 1. Data Stats (Ringkasan)
        $stats = ['total' => 15, 'danaDiberikan' => 10, 'ditolak' => 2, 'menunggu' => 3];
        
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
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-10 days')), 
        'status' => 'Menunggu'
    ],
    [
        'id' => 203,
        'nama' => 'LPJ Kunjungan Industri Jakarta',
        'nama_mahasiswa' => 'Fajar Nugraha',
        'nim' => '431701012',
        'jurusan' => 'Manajemen',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-13 days')), 
        'status' => 'Menunggu'
    ],
    [
        'id' => 204,
        'nama' => 'LPJ Bakti Sosial Desa Binaan',
        'nama_mahasiswa' => 'Gita Gutawa',
        'nim' => '421701013',
        'jurusan' => 'Akuntansi',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-20 days')), 
        'status' => 'Menunggu'
    ],
    [
        'id' => 205,
        'nama' => 'LPJ Lomba Desain Poster Nasional',
        'nama_mahasiswa' => 'Hendra Gunawan',
        'nim' => '461701014',
        'jurusan' => 'Desain Grafis',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-2 days')),
        'status' => 'Menunggu'
    ],
    [
        'id' => 206,
        'nama' => 'LPJ Seminar Kewirausahaan',
        'nama_mahasiswa' => 'Indah Permata',
        'nim' => '431701015',
        'jurusan' => 'Manajemen',
        'tanggal_pengajuan' => date('Y-m-d', strtotime('-14 days')), 
        'status' => 'Menunggu'
    ],
    ];
        
        // --- Akhir Data Dummy ---

        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Pengajuan LPJ',
            'stats' => $stats,
            'list_lpj' => $list_lpj_dummy
        ]);

        $this->view('pages/bendahara/pengajuan-lpj', $data, 'bendahara'); 
    }
}