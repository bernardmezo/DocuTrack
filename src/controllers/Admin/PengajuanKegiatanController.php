<?php
// File: src/controllers/Admin/AdminPengajuanKegiatanController.php

require_once '../src/core/Controller.php';

class AdminPengajuanKegiatanController extends Controller {
    
    /**
     * --- SIMULASI DATABASE MASTER (LENGKAP DENGAN PRODI) ---
     */
    private function getAllKegiatan() {
        return [
            102 => [
                'id' => 102, 
                'nama' => 'Seminar Nasional Artificial Intelligence 2025', 
                'pengusul' => 'Himpunan Mahasiswa TI',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2025-01-05',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.A.521211',
                'komentar' => [], 
                'komentar_penolakan' => '',
                'kak' => [
                    'nama_pengusul' => 'Andi Pratama',
                    'nim' => '481701001',
                    'jurusan' => 'Teknik Informatika dan Komputer',
                    'prodi' => 'D4 Teknik Informatika',
                    'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence 2025',
                    'gambaran_umum' => 'Seminar nasional yang membahas perkembangan terkini dalam bidang Artificial Intelligence dan Machine Learning.',
                    'penerima_manfaat' => 'Mahasiswa Teknik Informatika, Sistem Informasi, dan umum.',
                    'surat_pengantar' => 'Surat_Pengantar_Seminar_AI_2025.pdf',
                    'tanggal_mulai' => '2025-01-15',
                    'tanggal_selesai' => '2025-01-16',
                    'penanggung_jawab' => 'Andi Pratama',
                    'pelaksana' => ['Budi Santoso', 'Siti Aminah', 'Joko Widodo']
                ],
                'iku' => ['Mendapat Pekerjaan', 'Prestasi Mahasiswa'],
                'indikator' => [
                    ['bulan' => 'Januari', 'nama' => 'Peserta Terdaftar', 'target' => 80],
                    ['bulan' => 'Januari', 'nama' => 'Peserta Hadir', 'target' => 90],
                    ['bulan' => 'Januari', 'nama' => 'Tingkat Kepuasan Peserta', 'target' => 85]
                ],
                'rab' => [
                    'Belanja Barang' => [
                        ['id'=>1, 'uraian'=>'Konsumsi Rapat', 'rincian'=>'Snack Box', 'vol1'=>20, 'sat1'=>'Orang', 'vol2'=>4, 'sat2'=>'Kali', 'harga'=>15000]
                    ],
                    'Belanja Jasa' => [
                        ['id'=>6, 'uraian'=>'Honorarium Pembicara', 'rincian'=>'Narasumber Eksternal', 'vol1'=>3, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>2000000]
                    ]
                ]
            ],
            103 => [
                'id' => 103, 
                'nama' => 'Workshop Mobile App Development', 
                'pengusul' => 'Lab Pemrograman',
                'nama_mahasiswa' => 'Siti Nurhaliza',
                'nim' => '481701002',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2025-01-08',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.A.521212',
                'komentar' => [], 
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop Mobile App Development'],
                'iku' => ['Prestasi Mahasiswa'],
                'indikator' => [],
                'rab' => []
            ],
            104 => [
                'id' => 104, 
                'nama' => 'Pelatihan Database Management', 
                'pengusul' => 'Hima SI',
                'nama_mahasiswa' => 'Rudi Hartono',
                'nim' => '441701001',
                'prodi' => 'D4 Sistem Informasi Kota Cerdas',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2025-01-12',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.B.521201',
                'komentar' => [], 
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Pelatihan Database Management'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            105 => [
                'id' => 105, 
                'nama' => 'Lomba Desain Poster Nasional', 
                'pengusul' => 'UKM Seni',
                'nama_mahasiswa' => 'Dina Amelia',
                'nim' => '461701001',
                'prodi' => 'D4 Teknik Grafika dan Penerbitan',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => '2025-01-14',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.C.521203',
                'komentar' => [], 
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Lomba Desain Poster Nasional'],
                'iku' => ['Prestasi Mahasiswa'],
                'indikator' => [],
                'rab' => []
            ],
            106 => [
                'id' => 106, 
                'nama' => 'Pameran Seni Rupa Digital', 
                'pengusul' => 'UKM Seni',
                'nama_mahasiswa' => 'Doni Irawan',
                'nim' => '461701006',
                'prodi' => 'D3 Desain Grafis',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => '2024-12-15',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.B.521212',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Pameran Seni Rupa Digital'],
                'iku' => ['Prestasi Mahasiswa', 'Reputasi Institusi'],
                'indikator' => [],
                'rab' => []
            ],
            107 => [
                'id' => 107, 
                'nama' => 'Workshop Keamanan Siber', 
                'pengusul' => 'Himpunan Mahasiswa TI',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '481701003',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2025-01-10',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.C.521213',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop Keamanan Siber'],
                'iku' => ['Kompetensi Lulusan', 'Prestasi Mahasiswa'],
                'indikator' => [],
                'rab' => []
            ],
            108 => [
                'id' => 108, 
                'nama' => 'Seminar Akuntansi Forensik', 
                'pengusul' => 'Hima Akuntansi',
                'nama_mahasiswa' => 'Rina Sari',
                'nim' => '421701001',
                'prodi' => 'D4 Akuntansi Manajerial',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => '2025-01-16',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.D.521204',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Seminar Akuntansi Forensik'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            109 => [
                'id' => 109, 
                'nama' => 'Workshop Digital Marketing', 
                'pengusul' => 'Hima Manajemen',
                'nama_mahasiswa' => 'Eko Prasetyo',
                'nim' => '431701001',
                'prodi' => 'D4 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => '2025-01-18',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.E.521205',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop Digital Marketing'],
                'iku' => ['Kerjasama dengan Industri'],
                'indikator' => [],
                'rab' => []
            ],
            110 => [
                'id' => 110, 
                'nama' => 'Kompetisi Robotika Tingkat Nasional', 
                'pengusul' => 'Lab Robotika',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '451701001',
                'prodi' => 'D4 Teknik Elektronika',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2025-01-20',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.F.521206',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Kompetisi Robotika Tingkat Nasional'],
                'iku' => ['Prestasi Mahasiswa'],
                'indikator' => [],
                'rab' => []
            ],
            111 => [
                'id' => 111, 
                'nama' => 'Pelatihan Public Speaking', 
                'pengusul' => 'BEM Kampus',
                'nama_mahasiswa' => 'Gita Pratiwi',
                'nim' => '431701002',
                'prodi' => 'D4 Manajemen Pemasaran',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => '2025-01-22',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.G.521207',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Pelatihan Public Speaking'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            112 => [
                'id' => 112, 
                'nama' => 'Workshop Video Editing', 
                'pengusul' => 'UKM Multimedia',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '461701002',
                'prodi' => 'D3 Desain Grafis',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => '2025-01-24',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.H.521208',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop Video Editing'],
                'iku' => ['Prestasi Mahasiswa'],
                'indikator' => [],
                'rab' => []
            ],
            113 => [
                'id' => 113, 
                'nama' => 'Seminar Perpajakan UMKM', 
                'pengusul' => 'Hima Akuntansi',
                'nama_mahasiswa' => 'Indah Permata',
                'nim' => '421701002',
                'prodi' => 'D3 Akuntansi',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => '2025-01-26',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.I.521209',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Seminar Perpajakan UMKM'],
                'iku' => ['Pengabdian Masyarakat'],
                'indikator' => [],
                'rab' => []
            ],
            114 => [
                'id' => 114, 
                'nama' => 'Lomba IoT Innovation', 
                'pengusul' => 'Hima TI',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '481701004',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2025-01-28',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.J.521210',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Lomba IoT Innovation'],
                'iku' => ['Prestasi Mahasiswa', 'Reputasi Institusi'],
                'indikator' => [],
                'rab' => []
            ],
            115 => [
                'id' => 115, 
                'nama' => 'Workshop Fotografi Produk', 
                'pengusul' => 'UKM Fotografi',
                'nama_mahasiswa' => 'Kartika Dewi',
                'nim' => '461701003',
                'prodi' => 'D4 Teknik Grafika dan Penerbitan',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => '2025-01-30',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.K.521211',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop Fotografi Produk'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            116 => [
                'id' => 116, 
                'nama' => 'Pelatihan Leadership Untuk Mahasiswa', 
                'pengusul' => 'BEM Fakultas',
                'nama_mahasiswa' => 'Lina Marlina',
                'nim' => '431701003',
                'prodi' => 'D3 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => '2025-02-01',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.L.521212',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Pelatihan Leadership Untuk Mahasiswa'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            117 => [
                'id' => 117, 
                'nama' => 'Workshop PLC Programming', 
                'pengusul' => 'Lab Otomasi',
                'nama_mahasiswa' => 'Rizal Fahmi',
                'nim' => '451701002',
                'prodi' => 'D4 Teknik Telekomunikasi',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2025-02-03',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.M.521213',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Workshop PLC Programming'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ],
            118 => [
                'id' => 118, 
                'nama' => 'Pelatihan CAD Mekanik', 
                'pengusul' => 'Hima Mesin',
                'nama_mahasiswa' => 'Wahyu Hidayat',
                'nim' => '411701002',
                'prodi' => 'D4 Teknik Mesin',
                'jurusan' => 'Teknik Mesin',
                'tanggal_pengajuan' => '2025-02-05',
                'status' => 'Disetujui',
                'kode_mak' => '5241.001.052.N.521214',
                'komentar' => [],
                'komentar_penolakan' => '',
                'kak' => ['nama_kegiatan' => 'Pelatihan CAD Mekanik'],
                'iku' => ['Kompetensi Lulusan'],
                'indikator' => [],
                'rab' => []
            ]
        ];
    }

    /**
     * Menampilkan HALAMAN LIST (Hanya yang Disetujui)
     */
    public function index($data_dari_router = []) { 
        
        $list_kegiatan_all = $this->getAllKegiatan();
        $list_kegiatan_dummy = [];
        
        foreach ($list_kegiatan_all as $id => $item) {
            if (strtolower($item['status']) === 'disetujui') {
                $list_kegiatan_dummy[] = $item;
            }
        }

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => $list_kegiatan_dummy 
        ]);

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    /**
     * Menampilkan HALAMAN DETAIL atau RINCIAN
     * Mode: detail (default) atau rincian (form input)
     */
    public function show($id, $data_dari_router = []) {
        
        $mode = $_GET['mode'] ?? 'detail';
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        $list_kegiatan_all = $this->getAllKegiatan();
        $kegiatan_dipilih = $list_kegiatan_all[$id] ?? null;
        
        if (!$kegiatan_dipilih) {
            echo "<h1>404 - Kegiatan dengan ID $id tidak ditemukan.</h1>";
            exit;
        }
        
        // Jika mode = rincian, tampilkan form input
        if ($mode === 'rincian') {
            $data = array_merge($data_dari_router, [
                'title' => 'Rincian Kegiatan - ' . htmlspecialchars($kegiatan_dipilih['nama']),
                'kegiatan_id' => $id,
                'nama' => $kegiatan_dipilih['nama'],
                'kegiatan_data' => [
                    'id' => $kegiatan_dipilih['id'],
                    'nama' => $kegiatan_dipilih['nama'],
                    'penanggung_jawab' => $kegiatan_dipilih['nama_mahasiswa'],
                    'nama_mahasiswa' => $kegiatan_dipilih['nama_mahasiswa'],
                    'nim' => $kegiatan_dipilih['nim'],
                    'pengusul' => $kegiatan_dipilih['pengusul'],
                    'prodi' => $kegiatan_dipilih['prodi'],
                    'jurusan' => $kegiatan_dipilih['jurusan']
                ],
                'back_url' => $back_url
            ]);

            $this->view('pages/admin/detail_kegiatan', $data, 'app');
            return;
        }
        
        // Mode default: detail lengkap
        $status = $kegiatan_dipilih['status'];

        $surat_pengantar_url = '';
        if (!empty($kegiatan_dipilih['kak']['surat_pengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatan_dipilih['kak']['surat_pengantar'];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            'kegiatan_data' => $kegiatan_dipilih['kak'],
            'iku_data' => $kegiatan_dipilih['iku'],
            'indikator_data' => $kegiatan_dipilih['indikator'],
            'rab_data' => $kegiatan_dipilih['rab'],
            'kode_mak' => $kegiatan_dipilih['kode_mak'],
            'komentar_revisi' => $kegiatan_dipilih['komentar'],
            'komentar_penolakan' => $kegiatan_dipilih['komentar_penolakan'] ?? '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kegiatan', $data, 'app');
    }

    /**
     * Handle Submit Rincian Kegiatan
     */
    public function submitRincian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $kegiatan_id = $_POST['kegiatan_id'] ?? null;
        $penanggung_jawab = $_POST['penanggung_jawab'] ?? '';
        $pelaksana = $_POST['pelaksana'] ?? [];
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
        
        $upload_success = false;
        $file_name = '';
        
        if (isset($_FILES['surat_pengantar']) && $_FILES['surat_pengantar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['surat_pengantar']['tmp_name'];
            $file_original_name = $_FILES['surat_pengantar']['name'];
            $file_ext = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
            
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            if (in_array($file_ext, $allowed_extensions)) {
                $file_name = 'surat_' . $kegiatan_id . '_' . time() . '.' . $file_ext;
                $upload_dir = __DIR__ . '/../../../public/uploads/surat/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $upload_success = true;
                }
            }
        }

        if ($upload_success) {
            $_SESSION['success_message'] = 'Rincian kegiatan berhasil disimpan!';
            header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id);
            exit;
        } else {
            $_SESSION['error_message'] = 'Gagal mengupload surat pengantar!';
            header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id . '?mode=rincian');
            exit;
        }
    }

    /**
     * Download File Surat (untuk testing)
     */
    public function downloadSurat($filename) {
        $file_path = __DIR__ . '/../../../public/uploads/surat/' . $filename;
        
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            echo "%PDF-1.4\n";
            echo "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n";
            echo "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n";
            echo "3 0 obj<</Type/Page/Parent 2 0 R/Resources<</Font<</F1<</Type/Font/Subtype/Type1/BaseFont/Arial>>>>>>/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj\n";
            echo "4 0 obj<</Length 55>>stream\nBT /F1 24 Tf 100 700 Td (Dummy PDF - " . htmlspecialchars($filename) . ") Tj ET\nendstream\nendobj\n";
            echo "xref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000115 00000 n\n0000000287 00000 n\n";
            echo "trailer<</Size 5/Root 1 0 R>>\nstartxref\n393\n%%EOF";
            exit;
        }
    }
}