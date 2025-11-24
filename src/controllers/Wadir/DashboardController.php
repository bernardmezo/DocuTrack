<?php
// File: src/controllers/wadir/DashboardController.php

require_once '../src/core/Controller.php';

class WadirDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // --- DATA DUMMY DENGAN STRUKTUR JURUSAN & PRODI PNJ ---
        // Jurusan digunakan untuk Filter, Prodi digunakan untuk Tampilan Tabel
        $list_usulan_all = [
            // JURUSAN: TEKNIK INFORMATIKA DAN KOMPUTER
            ['id' => 1, 'nama' => 'Seminar Nasional Teknologi', 'pengusul' => 'Ahmad Rizki', 'nim' => '2201001', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-01-15'],
            ['id' => 2, 'nama' => 'Workshop AI & Machine Learning', 'pengusul' => 'Siti Nurhaliza', 'nim' => '2201002', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-01-18'],
            ['id' => 7, 'nama' => 'Bakti Sosial Desa Binaan', 'pengusul' => 'Gilang Ramadhan', 'nim' => '2201003', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-05'],
            ['id' => 12, 'nama' => 'Pelatihan Public Speaking', 'pengusul' => 'Lukman Hakim', 'nim' => '2201004', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknologi Industri Cetak Kemasan', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-18'],
            ['id' => 15, 'nama' => 'Kompetisi Programming', 'pengusul' => 'Olivia Gunawan', 'nim' => '2201005', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-25'],
            ['id' => 20, 'nama' => 'Lomba Debat Bahasa Inggris', 'pengusul' => 'Taufik Hidayat', 'nim' => '2201006', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-10'],
            ['id' => 24, 'nama' => 'Pelatihan Data Science', 'pengusul' => 'Xena Puspita', 'nim' => '2201007', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-20'],

            // JURUSAN: TEKNIK ELEKTRO
            ['id' => 3, 'nama' => 'Lomba Cerdas Cermat', 'pengusul' => 'Budi Santoso', 'nim' => '2202001', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-01-20'],
            ['id' => 5, 'nama' => 'Kompetisi Robotika', 'pengusul' => 'Eko Prasetyo', 'nim' => '2202002', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Telekomunikasi', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-01-25'],
            ['id' => 10, 'nama' => 'Study Tour Industri', 'pengusul' => 'Joko Susilo', 'nim' => '2202003', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Instrumentasi dan Kontrol Industri', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-12'],
            ['id' => 18, 'nama' => 'Pelatihan IoT', 'pengusul' => 'Reza Pahlevi', 'nim' => '2202004', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Broadband Multimedia', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-05'],
            ['id' => 25, 'nama' => 'Lomba Inovasi Teknologi', 'pengusul' => 'Yoga Aditya', 'nim' => '2202005', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Listrik', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-22'],

            // JURUSAN: AKUNTANSI
            ['id' => 6, 'nama' => 'Seminar Kewirausahaan', 'pengusul' => 'Fitri Handayani', 'nim' => '2204001', 'jurusan' => 'Akuntansi', 'prodi' => 'Akuntansi Keuangan', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-01'],
            ['id' => 9, 'nama' => 'Webinar Digital Marketing', 'pengusul' => 'Intan Permata', 'nim' => '2204002', 'jurusan' => 'Akuntansi', 'prodi' => 'Keuangan dan Perbankan', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-10'],
            ['id' => 19, 'nama' => 'Seminar Perpajakan', 'pengusul' => 'Sari Indah', 'nim' => '2204003', 'jurusan' => 'Akuntansi', 'prodi' => 'Manajemen Keuangan', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-08'],

            // JURUSAN: ADMINISTRASI NIAGA
            ['id' => 4, 'nama' => 'Pelatihan Web Development', 'pengusul' => 'Dewi Lestari', 'nim' => '2203001', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'Administrasi Bisnis', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-01-22'],
            ['id' => 8, 'nama' => 'Turnamen E-Sports', 'pengusul' => 'Hendra Wijaya', 'nim' => '2203002', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'MICE', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-08'],
            ['id' => 14, 'nama' => 'Donor Darah Bersama', 'pengusul' => 'Nanda Pratama', 'nim' => '2203003', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'Bahasa Inggris untuk Komunikasi Bisnis', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-22'],
            ['id' => 16, 'nama' => 'Seminar Keamanan Siber', 'pengusul' => 'Putra Mahendra', 'nim' => '2203004', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'Manajemen Pemasaran', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-28'],
            ['id' => 23, 'nama' => 'Seminar Blockchain Technology', 'pengusul' => 'Wahyu Nugroho', 'nim' => '2203005', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'MICE', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-18'],

            // JURUSAN: TEKNIK MESIN
            ['id' => 11, 'nama' => 'Lomba Karya Tulis Ilmiah', 'pengusul' => 'Kartika Sari', 'nim' => '2205001', 'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Mesin', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-02-15'],
            ['id' => 21, 'nama' => 'Pekan Olahraga', 'pengusul' => 'Umar Faruq', 'nim' => '2205002', 'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Konversi Energi', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-12'],

            // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN
            ['id' => 13, 'nama' => 'Festival Seni Budaya', 'pengusul' => 'Maya Anggraini', 'nim' => '2206001', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-02-20'],
            ['id' => 17, 'nama' => 'Workshop Fotografi', 'pengusul' => 'Qori Amanda', 'nim' => '2206002', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Penerbitan', 'status' => 'Menunggu', 'tanggal_pengajuan' => '2024-03-01'],
            ['id' => 22, 'nama' => 'Workshop UI/UX Design', 'pengusul' => 'Vina Melinda', 'nim' => '2206003', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Teknik Grafika', 'status' => 'Disetujui', 'tanggal_pengajuan' => '2024-03-15'],
        ];

        // 1. LOGIKA FILTER JURUSAN
        // Ambil jurusan yang dipilih dari URL (misal: ?jurusan=Teknik Elektro)
        $selected_jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';
        
        // Array penampung data yang sudah difilter
        $list_usulan_filtered = $list_usulan_all;

        // Jika ada filter jurusan, lakukan penyaringan
        if (!empty($selected_jurusan)) {
            $list_usulan_filtered = array_filter($list_usulan_all, function($item) use ($selected_jurusan) {
                return $item['jurusan'] === $selected_jurusan;
            });
        }

        // 2. LOGIKA PAGINATION (Berdasarkan data yang sudah difilter)
        $items_per_page = 10;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_items = count($list_usulan_filtered);
        $total_pages = ceil($total_items / $items_per_page);
        
        // Pastikan current_page tidak melebihi total_pages
        if ($total_pages > 0) {
            $current_page = min($current_page, $total_pages);
        }
        
        // Potong array untuk halaman saat ini
        $offset = ($current_page - 1) * $items_per_page;
        $list_usulan_paginated = array_slice($list_usulan_filtered, $offset, $items_per_page);

        // 3. DAFTAR JURUSAN UNTUK DROPDOWN
        // Ambil unique 'jurusan' dari data asli untuk opsi filter
        $jurusan_list = array_unique(array_column($list_usulan_all, 'jurusan'));
        sort($jurusan_list);

        // Hitung statistik sederhana dari data asli (atau bisa dari data terfilter jika diinginkan)
        $stats_dummy = [
            'total' => count($list_usulan_all),
            'disetujui' => count(array_filter($list_usulan_all, fn($i) => $i['status'] == 'Disetujui')),
            'menunggu' => count(array_filter($list_usulan_all, fn($i) => $i['status'] == 'Menunggu'))
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Dashboard Wadir',
            'stats' => $stats_dummy,
            'list_usulan' => $list_usulan_paginated, // Kirim data yang sudah difilter & dipaginasi
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'jurusan_list' => $jurusan_list,
            'selected_jurusan' => $selected_jurusan // Kirim status filter saat ini ke view
        ]);

        $this->view('pages/wadir/dashboard', $data, 'wadir'); 
    }
}