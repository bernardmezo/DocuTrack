<?php
// File: src/controllers/Wadir/MonitoringController.php
require_once '../src/core/Controller.php';

class WadirMonitoringController extends Controller {
    
    // --- Data Dummy dengan Struktur PNJ ---
    private $all_proposals = [
        // TEKNIK INFORMATIKA DAN KOMPUTER
        ['id' => 1, 'nama' => 'Seminar Nasional: Inovasi Teknologi', 'nama_lengkap' => 'Putra Wijaya', 'nim' => '2021001001', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 3, 'nama' => 'Kuliah Umum: AI for Future', 'nama_lengkap' => 'Bernadya Putri', 'nim' => '2022001023', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 'status' => 'In Process', 'tahap_sekarang' => 'ACC PPK'],
        ['id' => 5, 'nama' => 'Dies Natalis TIK', 'nama_lengkap' => 'Anton Pratama', 'nim' => '2020001045', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Jaringan', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],
        ['id' => 8, 'nama' => 'Hackathon 2025', 'nama_lengkap' => 'Deni Kurniawan', 'nim' => '2022001056', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknologi Industri Cetak Kemasan', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],

        // SISTEM INFORMASI (Bagian dari TIK atau Jurusan Sendiri tergantung struktur, disini saya gabung ke TIK sesuai PNJ atau pisah jika beda)
        // Asumsi PNJ: Masuk Jurusan TIK
        ['id' => 2, 'nama' => 'Seminar BEM: Education', 'nama_lengkap' => 'Yopan Saputra', 'nim' => '2021002015', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Informatika', 'status' => 'Ditolak', 'tahap_sekarang' => 'ACC WD'],
        ['id' => 6, 'nama' => 'Seminar Expektik', 'nama_lengkap' => 'Bambang Setiawan', 'nim' => '2021002032', 'jurusan' => 'Teknik Informatika dan Komputer', 'prodi' => 'Teknik Multimedia Digital', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],

        // TEKNIK ELEKTRO
        ['id' => 4, 'nama' => 'Seminar Himatik', 'nama_lengkap' => 'Fidel Augusto', 'nim' => '2022003008', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Otomasi Listrik Industri', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],
        ['id' => 9, 'nama' => 'Workshop IoT', 'nama_lengkap' => 'Eka Permana', 'nim' => '2021003027', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Teknik Telekomunikasi', 'status' => 'In Process', 'tahap_sekarang' => 'Dana Cair'],
        ['id' => 12, 'nama' => 'Kompetisi Robotik', 'nama_lengkap' => 'Hendra Kusuma', 'nim' => '2021003041', 'jurusan' => 'Teknik Elektro', 'prodi' => 'Broadband Multimedia', 'status' => 'Approved', 'tahap_sekarang' => 'LPJ'],

        // TEKNIK GRAFIKA DAN PENERBITAN
        ['id' => 7, 'nama' => 'Lomba Desain', 'nama_lengkap' => 'Citra Dewi Lestari', 'nim' => '2023004011', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Desain Grafis', 'status' => 'In Process', 'tahap_sekarang' => 'Verifikasi'],
        ['id' => 11, 'nama' => 'Pelatihan UI/UX', 'nama_lengkap' => 'Galih Pratomo', 'nim' => '2023004025', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'prodi' => 'Penerbitan', 'status' => 'Menunggu', 'tahap_sekarang' => 'Pengajuan'],

        // ADMINISTRASI NIAGA
        ['id' => 10, 'nama' => 'Seminar Kewirausahaan', 'nama_lengkap' => 'Fani Rahayu', 'nim' => '2022005019', 'jurusan' => 'Administrasi Niaga', 'prodi' => 'Administrasi Bisnis', 'status' => 'In Process', 'tahap_sekarang' => 'ACC WD'],
    ];

    // Daftar Jurusan PNJ untuk Filter
    private $list_jurusan = [
        'Teknik Informatika dan Komputer',
        'Teknik Elektro',
        'Teknik Mesin',
        'Teknik Sipil',
        'Teknik Grafika dan Penerbitan',
        'Akuntansi',
        'Administrasi Niaga'
    ];
    
    public function index($data_dari_router = []) { 
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_jurusan' => $this->list_jurusan
        ]);
        $this->view('pages/wadir/monitoring', $data, 'wadir');
    }

    /**
     * Method GET DATA (API)
     * Filter menggunakan 'jurusan' (Induk)
     * Data yang dikirim tetap memuat 'prodi'
     */
    public function getData() {
        $page = (int)($_GET['page'] ?? 1);
        $status_filter = strtolower($_GET['status'] ?? 'semua');
        $jurusan_filter = $_GET['jurusan'] ?? 'semua'; // Menerima filter jurusan induk
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5;

        $filtered_data = array_filter($this->all_proposals, function($item) use ($status_filter, $jurusan_filter, $search_text) {
            // Filter Status
            $status_match = ($status_filter === 'semua') || (strtolower($item['status']) === $status_filter);
            
            // Filter Jurusan (Mencocokkan Jurusan Induk)
            $jurusan_match = ($jurusan_filter === 'semua') || ($item['jurusan'] === $jurusan_filter);
            
            // Filter Search (Nama Kegiatan, Pengusul, NIM)
            $search_match = empty($search_text) || 
                            str_contains(strtolower($item['nama']), $search_text) || 
                            str_contains(strtolower($item['nama_lengkap']), $search_text) || 
                            str_contains(strtolower($item['nim']), $search_text);
            
            return $status_match && $jurusan_match && $search_match;
        });

        $total_items = count($filtered_data);
        $total_pages = max(1, ceil($total_items / $per_page));
        $offset = ($page - 1) * $per_page;
        $paginated_items = array_slice(array_values($filtered_data), $offset, $per_page);

        $response = [
            'proposals' => $paginated_items,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $total_pages,
                'totalItems' => $total_items,
                'perPage' => $per_page,
                'showingFrom' => $total_items > 0 ? $offset + 1 : 0,
                'showingTo' => $total_items > 0 ? $offset + count($paginated_items) : 0
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>