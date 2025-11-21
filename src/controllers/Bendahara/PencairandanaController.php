<?php
// File: src/controllers/Bendahara/PencairandanaController.php

require_once '../src/core/Controller.php';

class BendaharaPencairandanaController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- 1. Data Stats (Ringkasan) ---
        $stats = [
            'total' => 15, 
            'danaDiberikan' => 10, 
            'ditolak' => 2, 
            'menunggu' => 3
        ];

        // --- 2. Data List KAK (Pencairan Dana) ---
        // Data ini mencakup semua status: Menunggu
        $list_kak_dummy = [
            [
                'id' => 101,
                'nama' => 'Seminar Nasional Artificial Intelligence 2025',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'jurusan' => 'Teknik Informatika',
                'status' => 'Menunggu', 
                'pengusul' => 'Himpunan Mahasiswa TI'
            ],
            [
                'id' => 102,
                'nama' => 'Workshop UI/UX Design Fundamental',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '461701002',
                'jurusan' => 'Desain Grafis',
                'status' => 'Menunggu', 
                'pengusul' => 'UKM Multimedia'
            ],
            [
                'id' => 103,
                'nama' => 'Studi Banding ke Startup Unicorn',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '441701003',
                'jurusan' => 'Sistem Informasi',
                'status' => 'Menunggu', 
                'pengusul' => 'BEM Fakultas'
            ],
            [
                'id' => 104,
                'nama' => 'Turnamen E-Sport Kampus',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '431701004',
                'jurusan' => 'Manajemen',
                'status' => 'Menunggu',
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
                'status' => 'Menunggu',
                'pengusul' => 'UKM Seni'
            ],
        ];

        // Gabungkan data
        $data = array_merge($data_dari_router, [
            'title' => 'Pencairan Dana - Bendahara',
            'stats' => $stats,
            'list_kak' => $list_kak_dummy
        ]);

        // Panggil View
        $this->view('pages/bendahara/pencairan-dana', $data, 'bendahara'); 
    }
}