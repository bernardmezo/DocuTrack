<?php
// File: src/controllers/Bendahara/PencairandanaController.php

require_once '../src/core/Controller.php';

class BendaharaPencairandanaController extends Controller {
    
    // DATA DUMMY DIPERBARUI DENGAN STRUKTUR PNJ (JURUSAN & PRODI)
    private $list_kegiatan_all = [
        101 => [
            'id' => 101,
            'nama' => 'Seminar Nasional Artificial Intelligence 2025',
            'nama_mahasiswa' => 'Andi Pratama',
            'nim' => '481701001',
            'jurusan' => 'Teknik Informatika dan Komputer', // Jurusan Induk (Untuk Filter)
            'prodi' => 'Teknik Informatika', // Prodi Spesifik (Untuk Tampilan)
            'email' => 'andi.pratama@student.polindra.ac.id',
            'no_telp' => '08123456789',
            'status' => 'Menunggu',
            'pengusul' => 'Himpunan Mahasiswa TI',
            'tanggal_pengajuan' => '2025-01-15',
            'tanggal_kegiatan' => '2025-02-20',
            'lokasi' => 'Auditorium Kampus',
            'anggaran_disetujui' => 15000000,
            'anggaran_diminta' => 15000000,
            'deskripsi' => 'Seminar nasional tentang perkembangan AI dan implementasinya di industri.',
            'kode_mak' => '5241.001.052.A.521211',
            'catatan_verifikator' => 'Dokumen lengkap dan sesuai persyaratan.',
            'catatan_wadir' => 'Disetujui dengan catatan: pastikan narasumber sesuai jadwal.',
            'catatan_ppk' => 'Anggaran telah sesuai dengan RAB.',
            'catatan_bendahara' => null,
            'jumlah_dicairkan' => null,
            'tanggal_pencairan' => null,
            'metode_pencairan' => null,
            'kak' => [
                'nama_pengusul' => 'Andi Pratama',
                'nim_pengusul' => '481701001',
                'nama_penanggung_jawab' => 'Dr. Budi Hartanto, M.Kom',
                'nip_penanggung_jawab' => '197805152008011002',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence 2025',
                'gambaran_umum' => 'Seminar nasional yang membahas perkembangan terkini dalam bidang Artificial Intelligence.',
                'penerima_manfaat' => 'Mahasiswa Teknik Informatika, Sistem Informasi, dan umum.',
                'metode_pelaksanaan' => 'Hybrid (offline dan online).',
                'tahapan_kegiatan' => "Persiapan, Pelaksanaan, Evaluasi",
                'surat_pengantar' => 'Surat_Pengantar_Seminar_AI_2025.pdf',
                'tanggal_mulai' => '2025-02-20',
                'tanggal_selesai' => '2025-02-21'
            ],
            'iku' => ['Mendapat Pekerjaan', 'Prestasi Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Januari', 'nama' => 'Peserta Terdaftar', 'target' => 80]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Konsumsi Rapat', 'rincian'=>'Snack Box', 'vol1'=>20, 'sat1'=>'Orang', 'vol2'=>4, 'sat2'=>'Kali', 'harga'=>15000]
                ]
            ],
            'file_kak' => 'kak_seminar_ai_2025.pdf',
            'file_rab' => 'rab_seminar_ai_2025.pdf'
        ],
        105 => [
            'id' => 105,
            'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
            'nama_mahasiswa' => 'Rina Wati',
            'nim' => '421701005',
            'jurusan' => 'Akuntansi', // Jurusan Induk
            'prodi' => 'Akuntansi Keuangan', // Prodi Spesifik
            'email' => 'rina.wati@student.polindra.ac.id',
            'no_telp' => '08456789012',
            'status' => 'Menunggu',
            'pengusul' => 'Hima Akuntansi',
            'tanggal_pengajuan' => '2025-01-25',
            'tanggal_kegiatan' => '2025-03-05',
            'lokasi' => 'Aula Gedung C',
            'anggaran_disetujui' => 6500000,
            'anggaran_diminta' => 6500000,
            'deskripsi' => 'Pelatihan akuntansi dasar untuk pelaku UMKM di sekitar kampus.',
            'kode_mak' => '5241.001.052.D.521214',
            'catatan_verifikator' => 'Dokumen sudah lengkap.',
            'catatan_wadir' => 'Disetujui.',
            'catatan_ppk' => 'Anggaran sesuai.',
            'catatan_bendahara' => null,
            'jumlah_dicairkan' => null,
            'tanggal_pencairan' => null,
            'metode_pencairan' => null,
            'kak' => [
                'nama_pengusul' => 'Rina Wati',
                'nim_pengusul' => '421701005',
                'nama_penanggung_jawab' => 'Dra. Sri Mulyani, M.Ak',
                'nip_penanggung_jawab' => '197603102005012001',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
                'gambaran_umum' => 'Pelatihan akuntansi untuk UMKM.',
                'penerima_manfaat' => 'Pelaku UMKM.',
                'metode_pelaksanaan' => 'Ceramah dan Praktik.',
                'tahapan_kegiatan' => "Persiapan, Pelatihan, Pendampingan",
                'surat_pengantar' => 'Surat_Pelatihan_Akuntansi_UMKM.pdf',
                'tanggal_mulai' => '2025-03-05',
                'tanggal_selesai' => '2025-03-06'
            ],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [
                ['bulan' => 'Maret', 'nama' => 'Peserta UMKM', 'target' => 40]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>60, 'uraian'=>'Konsumsi Peserta', 'rincian'=>'Snack', 'vol1'=>50, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>40000]
                ]
            ],
            'file_kak' => 'kak_pelatihan_akuntansi.pdf',
            'file_rab' => 'rab_pelatihan_akuntansi.pdf'
        ],
        // ... Data lain disederhanakan untuk brevity, struktur sama ...
        // ... (Data 101 dan 105 tetap sama seperti sebelumnya) ...

        // DATA 102 (STATUS: DANA DIBERIKAN) - DATA DILENGKAPI
        102 => [
            'id' => 102,
            'nama' => 'Workshop UI/UX Design Fundamental',
            'nama_mahasiswa' => 'Siti Aminah',
            'nim' => '461701002',
            'jurusan' => 'Teknik Grafika dan Penerbitan',
            'prodi' => 'Desain Grafis',
            'email' => 'siti.aminah@student.polindra.ac.id',
            'no_telp' => '08234567890',
            'status' => 'Dana Diberikan',
            'pengusul' => 'UKM Multimedia',
            'tanggal_pengajuan' => '2025-01-18',
            'tanggal_kegiatan' => '2025-02-25',
            'lokasi' => 'Lab Komputer',
            'anggaran_disetujui' => 8500000,
            'anggaran_diminta' => 8500000,
            'deskripsi' => 'Workshop intensif tentang dasar-dasar UI/UX design untuk pemula.',
            'kode_mak' => '5241.001.052.B.521212',
            'catatan_verifikator' => 'Dokumen sesuai.',
            'catatan_wadir' => 'Disetujui.',
            'catatan_ppk' => 'RAB sudah sesuai.',
            'catatan_bendahara' => 'Dana telah dicairkan sesuai anggaran yang disetujui.',
            'jumlah_dicairkan' => 8500000,
            'tanggal_pencairan' => '2025-01-20 10:30:00',
            'metode_pencairan' => 'dana_penuh',
            // ISI DATA KAK AGAR TIDAK ERROR
            'kak' => [
                'nama_pengusul' => 'Siti Aminah',
                'nim_pengusul' => '461701002',
                'nama_penanggung_jawab' => 'Rudi Hermawan, M.Ds',
                'nip_penanggung_jawab' => '198501012010011001',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'nama_kegiatan' => 'Workshop UI/UX Design Fundamental',
                'gambaran_umum' => 'Workshop intensif pengenalan tools Figma dan prinsip desain user interface.',
                'penerima_manfaat' => 'Mahasiswa Desain Grafis dan Multimedia.',
                'metode_pelaksanaan' => 'Praktikum Lab Komputer.',
                'tahapan_kegiatan' => '1. Pengenalan Tools\n2. Studi Kasus\n3. Prototyping',
                'surat_pengantar' => 'Surat_Workshop_UIUX.pdf',
                'tanggal_mulai' => '2025-02-25',
                'tanggal_selesai' => '2025-02-26'
            ],
            'iku' => ['Prestasi Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Peserta Workshop', 'target' => 50]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>12, 'uraian'=>'Konsumsi Peserta', 'rincian'=>'Snack Box', 'vol1'=>60, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Kali', 'harga'=>20000]
                ],
                'Belanja Jasa' => [
                    ['id'=>15, 'uraian'=>'Honor Instruktur', 'rincian'=>'Instruktur UI/UX', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>1500000]
                ]
            ],
            'file_kak' => 'kak_workshop_uiux.pdf',
            'file_rab' => 'rab_workshop_uiux.pdf'
        ]
    ];

    /**
     * Halaman List Pencairan Dana
     * Hanya menampilkan usulan dengan status MENUNGGU
     */
    public function index($data_dari_router = []) {
        $stats = [
            'total' => 12, 
            'danaDiberikan' => 8, 
            'ditolak' => 2, 
            'menunggu' => 12
        ];

        // Data dummy untuk tabel (List View)
        // Disini kita memetakan data Jurusan (untuk filter) dan Prodi (untuk display)
        $list_kak_dummy = [
            [
                'id' => 101,
                'nama' => 'Seminar Nasional Artificial Intelligence 2025',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'jurusan' => 'Teknik Informatika dan Komputer', // FILTER key
                'prodi' => 'Teknik Informatika', // DISPLAY text
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-15',
                'pengusul' => 'Himpunan Mahasiswa TI'
            ],
            [
                'id' => 105,
                'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
                'nama_mahasiswa' => 'Rina Wati',
                'nim' => '421701005',
                'jurusan' => 'Akuntansi',
                'prodi' => 'Akuntansi Keuangan',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-25',
                'pengusul' => 'Hima Akuntansi'
            ],
            [
                'id' => 107,
                'nama' => 'Pelatihan Digital Marketing untuk UMKM',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '431701007',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'Administrasi Bisnis',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-28',
                'pengusul' => 'Hima Administrasi Niaga'
            ],
            [
                'id' => 108,
                'nama' => 'Workshop Mobile App Development',
                'nama_mahasiswa' => 'Desi Ratnasari',
                'nim' => '481701008',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Multimedia Digital', // Prodi berbeda dalam satu Jurusan TIK
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-29',
                'pengusul' => 'UKM Teknologi'
            ],
            [
                'id' => 109,
                'nama' => 'Lomba Desain Poster Nasional',
                'nama_mahasiswa' => 'Rizky Pratama',
                'nim' => '461701009',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'Desain Grafis',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-30',
                'pengusul' => 'Hima TGP'
            ],
            [
                'id' => 110,
                'nama' => 'Seminar Cyber Security Awareness',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '441701010',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Multimedia Jaringan', // Prodi berbeda lagi di TIK
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-01',
                'pengusul' => 'Hima TIK'
            ],
            [
                'id' => 111,
                'nama' => 'Pelatihan Public Speaking',
                'nama_mahasiswa' => 'Citra Dewi',
                'nim' => '431701011',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'MICE',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-02',
                'pengusul' => 'Hima MICE'
            ],
            [
                'id' => 112,
                'nama' => 'Kompetisi Coding Challenge',
                'nama_mahasiswa' => 'Eko Purnomo',
                'nim' => '481701012',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Informatika',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-03',
                'pengusul' => 'Himpunan Mahasiswa TI'
            ],
            [
                'id' => 113,
                'nama' => 'Workshop Fotografi Produk',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '461701013',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'Penerbitan',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-04',
                'pengusul' => 'UKM Fotografi'
            ],
            [
                'id' => 114,
                'nama' => 'Seminar Manajemen Keuangan Pribadi',
                'nama_mahasiswa' => 'Gita Sari',
                'nim' => '421701014',
                'jurusan' => 'Akuntansi',
                'prodi' => 'Keuangan dan Perbankan',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-05',
                'pengusul' => 'Hima Akuntansi'
            ],
            [
                'id' => 115,
                'nama' => 'Pelatihan Data Analytics dengan Python',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '441701015',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'Teknik Informatika',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-06',
                'pengusul' => 'Hima TIK'
            ],
            [
                'id' => 116,
                'nama' => 'Bakti Sosial ke Desa Binaan',
                'nama_mahasiswa' => 'Indah Permata',
                'nim' => '431701016',
                'jurusan' => 'Teknik Sipil',
                'prodi' => 'Konstruksi Gedung',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-02-07',
                'pengusul' => 'BEM Universitas'
            ]
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Pencairan Dana - Bendahara',
            'stats' => $stats,
            'list_kak' => $list_kak_dummy
        ]);

        $this->view('pages/bendahara/pencairan-dana', $data, 'bendahara'); 
    }

    /**
     * Halaman Detail untuk Review Pencairan Dana
     */
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'pencairan-dana';
        $base_url = "/docutrack/public/bendahara";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pencairan-dana';

        $kegiatan_dipilih = $this->list_kegiatan_all[$id] ?? null;
        
        if (!$kegiatan_dipilih) {
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        $surat_pengantar_url = '';
        if (!empty($kegiatan_dipilih['kak']['surat_pengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatan_dipilih['kak']['surat_pengantar'];
        }

        // Hitung total anggaran dari RAB
        $total_anggaran = 0;
        foreach ($kegiatan_dipilih['rab'] as $kategori => $items) {
            foreach ($items as $item) {
                $subtotal = $item['vol1'] * $item['vol2'] * $item['harga'];
                $total_anggaran += $subtotal;
            }
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Pencairan Dana - ' . htmlspecialchars($kegiatan_dipilih['nama']),
            'status' => $kegiatan_dipilih['status'],
            'kegiatan_data' => $kegiatan_dipilih['kak'],
            'iku_data' => $kegiatan_dipilih['iku'],
            'indikator_data' => $kegiatan_dipilih['indikator'],
            'rab_data' => $kegiatan_dipilih['rab'],
            'total_anggaran' => $total_anggaran,
            'kode_mak' => $kegiatan_dipilih['kode_mak'],
            'nama_mahasiswa' => $kegiatan_dipilih['nama_mahasiswa'],
            'nim' => $kegiatan_dipilih['nim'],
            // Menampilkan data prodi & jurusan
            'jurusan' => $kegiatan_dipilih['jurusan'],
            'prodi' => $kegiatan_dipilih['prodi'], // Tambahkan ini
            'email' => $kegiatan_dipilih['email'],
            'no_telp' => $kegiatan_dipilih['no_telp'],
            'pengusul' => $kegiatan_dipilih['pengusul'],
            'tanggal_pengajuan' => $kegiatan_dipilih['tanggal_pengajuan'],
            'lokasi' => $kegiatan_dipilih['lokasi'],
            'anggaran_disetujui' => $kegiatan_dipilih['anggaran_disetujui'],
            'anggaran_diminta' => $kegiatan_dipilih['anggaran_diminta'],
            'deskripsi' => $kegiatan_dipilih['deskripsi'],
            'catatan_verifikator' => $kegiatan_dipilih['catatan_verifikator'],
            'catatan_wadir' => $kegiatan_dipilih['catatan_wadir'],
            'catatan_ppk' => $kegiatan_dipilih['catatan_ppk'],
            'catatan_bendahara' => $kegiatan_dipilih['catatan_bendahara'],
            'jumlah_dicairkan' => $kegiatan_dipilih['jumlah_dicairkan'],
            'tanggal_pencairan' => $kegiatan_dipilih['tanggal_pencairan'],
            'metode_pencairan' => $kegiatan_dipilih['metode_pencairan'],
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url,
            'file_kak' => $kegiatan_dipilih['file_kak'] ?? '',
            'file_rab' => $kegiatan_dipilih['file_rab'] ?? ''
        ]);

        $this->view('pages/bendahara/pencairan-dana-detail', $data, 'bendahara');
    }

    /**
     * Proses Pencairan/Penolakan Dana
     */
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        $kak_id = $_POST['kak_id'] ?? null;
        $action = $_POST['action'] ?? null;
        
        if (!$kak_id || !$action) {
            $_SESSION['flash_error'] = 'Data tidak lengkap!';
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        try {
            if ($action === 'cairkan') {
                $jumlah_dicairkan = $_POST['jumlah_dicairkan'] ?? 0;
                $metode_pencairan = $_POST['metode_pencairan'] ?? 'uang_muka';
                $catatan = trim($_POST['catatan'] ?? '');
                
                // TODO: Simpan ke database
                
                $_SESSION['flash_message'] = 'Dana berhasil dicairkan sebesar Rp ' . number_format($jumlah_dicairkan, 0, ',', '.');
                $_SESSION['flash_type'] = 'success';
                
            } elseif ($action === 'tolak') {
                $catatan = trim($_POST['catatan'] ?? '');
                
                if (empty($catatan)) {
                    $_SESSION['flash_error'] = 'Catatan penolakan wajib diisi!';
                    header('Location: /docutrack/public/bendahara/pencairan-dana/show/' . $kak_id);
                    exit;
                }
                
                // TODO: Simpan ke database
                
                $_SESSION['flash_type'] = 'warning';
                
            } else {
                throw new Exception('Action tidak valid');
            }

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        header('Location: /docutrack/public/bendahara/pencairan-dana');
        exit;
    }
}
?>