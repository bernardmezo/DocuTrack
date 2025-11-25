<?php
// File: src/controllers/Bendahara/DashboardController.php
require_once '../src/core/Controller.php';
require_once '../src/controllers/Bendahara/PengajuanlpjController.php';

class BendaharaDashboardController extends Controller {
    
    public function index($data_dari_router = []) {
        
        // 1. Data Stats (Ringkasan)
        $stats = ['total' => 12, 'danaDiberikan' => 8, 'ditolak' => 2, 'menunggu' => 2];
        
        // Hitung tanggal hari ini untuk referensi
        $today = new DateTime();
        
        // 3. Data List KAK dengan Jurusan dan Prodi PNJ
        $list_kak_dummy = [
            [
                'id' => 101,
                'nama' => 'Seminar Nasional Artificial Intelligence 2025',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '481701001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'D4 Teknik Informatika',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-15',
                'pengusul' => 'Himpunan Mahasiswa TI'
            ],
            [
                'id' => 102,
                'nama' => 'Workshop UI/UX Design Fundamental',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '461701002',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'D4 Teknik Grafika',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-18',
                'pengusul' => 'UKM Multimedia'
            ],
            [
                'id' => 104,
                'nama' => 'Turnamen E-Sport Kampus',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '431701004',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'D4 Administrasi Bisnis',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-22',
                'pengusul' => 'Komunitas Gaming'
            ],
            [
                'id' => 105,
                'nama' => 'Pelatihan Dasar Akuntansi Bagi UMKM',
                'nama_mahasiswa' => 'Rina Wati',
                'nim' => '421701005',
                'jurusan' => 'Akuntansi',
                'prodi' => 'D4 Akuntansi Manajerial',
                'status' => 'Menunggu',
                'tanggal_pengajuan' => '2025-01-25',
                'pengusul' => 'Hima Akuntansi'
            ],
            [
                'id' => 106,
                'nama' => 'Pameran Seni Rupa Digital',
                'nama_mahasiswa' => 'Doni Irawan',
                'nim' => '461701006',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'D3 Desain Grafis',
                'tanggal_pengajuan' => '2025-01-12',
                'status' => 'Dana Diberikan',
                'pengusul' => 'UKM Seni'
            ],
            [
                'id' => 107,
                'nama' => 'Workshop Internet of Things',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '481701007',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-10',
                'pengusul' => 'Hima TMJ'
            ],
            [
                'id' => 108,
                'nama' => 'Pelatihan Mesin CNC',
                'nama_mahasiswa' => 'Rudi Hermawan',
                'nim' => '451701008',
                'jurusan' => 'Teknik Mesin',
                'prodi' => 'D4 Teknik Perancangan Manufaktur',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-08',
                'pengusul' => 'Lab Manufaktur'
            ],
            [
                'id' => 109,
                'nama' => 'Kompetisi Robotika Nasional',
                'nama_mahasiswa' => 'Dewi Sartika',
                'nim' => '471701009',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'D4 Teknik Elektronika',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-05',
                'pengusul' => 'Robotics Club'
            ],
            [
                'id' => 110,
                'nama' => 'Seminar Teknologi 5G',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '471701010',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'D4 Teknik Telekomunikasi',
                'status' => 'Dana Diberikan',
                'tanggal_pengajuan' => '2025-01-03',
                'pengusul' => 'Hima Telekomunikasi'
            ],
        ];
        
        // 4. Ambil Data LPJ dari PengajuanlpjController
        $lpjController = new BendaharaPengajuanlpjController();
        $list_lpj = $lpjController->getLPJData();
        
        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj
        ]);
        
        $this->view('pages/bendahara/dashboard', $data, 'bendahara'); 
    }
}