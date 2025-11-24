<?php
// File: src/controllers/Wadir/RiwayatController.php
require_once '../src/core/Controller.php';

class WadirRiwayatController extends Controller {
    
    // Daftar Jurusan PNJ untuk Filter Dropdown
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
        
        // --- DATA DUMMY DENGAN STRUKTUR JURUSAN & PRODI PNJ ---
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
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-01-19'
            ],
            [
                'id' => 5, 'nama' => 'Seminar Cyber Security', 'pengusul' => 'Eko Prasetyo', 'nim' => '2201003',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-01-25'
            ],
            [
                'id' => 18, 'nama' => 'Seminar Cloud Computing', 'pengusul' => 'Kevin Anggara', 'nim' => '2201004',
                'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Teknologi Industri Cetak Kemasan', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-15'
            ],

            // JURUSAN: TEKNIK KOMPUTER (Biasanya bagian dari TIK di PNJ, tapi jika dipisah di sistem lama, sesuaikan)
            // Asumsi: Masuk ke rumpun TIK atau Elektro tergantung struktur, disini saya ikut struktur PNJ umum (TIK)
            [
                'id' => 4, 'nama' => 'Pelatihan Web Development', 'pengusul' => 'Dewi Lestari', 'nim' => '2203001',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-01-23'
            ],
            [
                'id' => 8, 'nama' => 'Pelatihan Database Administration', 'pengusul' => 'Gita Savitri', 'nim' => '2203002',
                'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-01'
            ],

            // JURUSAN: TEKNIK ELEKTRO
            [
                'id' => 7, 'nama' => 'Workshop IoT & Embedded System', 'pengusul' => 'Fajar Nugroho', 'nim' => '2202001',
                'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-01-28'
            ],

            // JURUSAN: AKUNTANSI
            [
                'id' => 10, 'nama' => 'Seminar Financial Technology', 'pengusul' => 'Hendra Wijaya', 'nim' => '2204001',
                'jurusan' => 'Akuntansi', 'prodi' => 'Keuangan dan Perbankan', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-05'
            ],

            // JURUSAN: TEKNIK MESIN
            [
                'id' => 12, 'nama' => 'Workshop CAD/CAM', 'pengusul' => 'Indra Kusuma', 'nim' => '2205001',
                'jurusan' => 'Teknik Mesin', 'prodi' => 'Teknik Mesin', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-08'
            ],

            // JURUSAN: TEKNIK GRAFIKA DAN PENERBITAN
            [
                'id' => 15, 'nama' => 'Pelatihan UI/UX Design', 'pengusul' => 'Julia Permata', 'nim' => '2206001',
                'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 
                'status' => 'Disetujui', 'tgl_verifikasi' => '2024-02-12'
            ],
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Persetujuan Wadir',
            'list_riwayat' => $list_riwayat_dummy,
            'jurusan_list' => $this->jurusan_list
        ]);

        $this->view('pages/wadir/riwayat_verifikasi', $data, 'wadir');
    }
}
?>