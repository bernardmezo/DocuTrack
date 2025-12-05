<?php
// File: src/controllers/Admin/AdminPengajuanKegiatanController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';
require_once '../src/helpers/logger_helper.php';
require_once __DIR__ . '/AdminController.php'; // Load AdminController for submitRincian

class AdminPengajuanKegiatanController extends Controller {
    
    /**
     * Menampilkan halaman daftar pengajuan kegiatan dengan filtering role-based.
     */
    public function index($data_dari_router = []) { 
    $model = new adminModel($this->db);

    // Ambil semua kegiatan (tanpa filter jurusan) agar halaman menampilkan SEMUA KAK
    // yang memenuhi kriteria posisi = 1 dan status = 3.
    $all_kegiatan = $model->getDashboardKAK();

    // Filter: Hanya tampilkan kegiatan yang Posisi = Admin (1) DAN Status = Disetujui (3)
    // Perhatian: beberapa fungsi/model mungkin memberi nama field berbeda (posisiId vs posisi, statusUtamaId vs statusId/status).
    $list_kegiatan_disetujui = array_filter($all_kegiatan, function($item) {
        // ambil nilai posisi dari beberapa kemungkinan key
        $posisi = null;
        if (isset($item['posisiId'])) {
            $posisi = $item['posisiId'];
        } elseif (isset($item['posisi'])) {
            $posisi = $item['posisi'];
        } elseif (isset($item['posisi_id'])) {
            $posisi = $item['posisi_id'];
        } else {
            $posisi = 0;
        }

        // ambil nilai status dari beberapa kemungkinan key
        $statusId = null;
        if (isset($item['statusUtamaId'])) {
            $statusId = $item['statusUtamaId'];
        } elseif (isset($item['statusId'])) {
            $statusId = $item['statusId'];
        } elseif (isset($item['statusUtama'])) {
            $statusId = $item['statusUtama'];
        } elseif (isset($item['status'])) {
            // jika status berupa teks, coba parse numeric bila mungkin
            $statusCandidate = $item['status'];
            $statusId = is_numeric($statusCandidate) ? (int)$statusCandidate : 0;
        } else {
            $statusId = 0;
        }

        $posisi = (int) $posisi;
        $statusId = (int) $statusId;

        // Hanya posisi = 1 (Admin) dan status = 3 (Disetujui)
        return ($posisi === 1 && $statusId === 3);
    });

    // Re-index array agar urutan kunci rapi (0, 1, 2...) untuk View
    $list_kegiatan_disetujui = array_values($list_kegiatan_disetujui);

    $data = array_merge($data_dari_router, [
        'title' => 'List Pengajuan Kegiatan',
        'list_kegiatan' => $list_kegiatan_disetujui,
        'debug_info' => [
            'total_raw' => count($all_kegiatan),
            'total_filtered' => count($list_kegiatan_disetujui)
        ]
    ]);

    $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
}


    /**
     * Menampilkan halaman detail atau rincian kegiatan.
     */
    public function show($id, $data_dari_router = []) {
        $mode = $_GET['mode'] ?? 'detail';
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        $model = new adminModel($this->db);
        
        $kegiatanDB = $model->getDetailKegiatan($id);

        if (!$kegiatanDB) {
            echo "<h1>404 - Kegiatan dengan ID $id tidak ditemukan.</h1>";
            exit;
        }
        
        if ($mode === 'rincian') {
            $data = array_merge($data_dari_router, [
                'title' => 'Rincian Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
                'kegiatan_id' => $id,
                'namaKeg' => $kegiatanDB['namaKegiatan'],
                'back_url' => $back_url
            ]);

            $this->view('pages/admin/detail_kegiatan', $data, 'app');
            return;
        }
        
        $kakId = $kegiatanDB['kakId'];
        
        $iku_string = $kegiatanDB['iku'] ?? '';
        $iku_array = !empty($iku_string) ? explode(',', $iku_string) : [];
        
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan = $model->getTahapanByKAK($kakId);
        $rab = $model->getRABByKAK($kakId);

        $surat_pengantar_url = '';
        if (!empty($kegiatanDB['suratPengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatanDB['suratPengantar'];
        }

        $kak_formatted = [
            'nama_pengusul' => $kegiatanDB['pemilikKegiatan'],
            'nim' => $kegiatanDB['nimPelaksana'],
            'jurusan' => $kegiatanDB['jurusanPenyelenggara'],
            'prodi' => $kegiatanDB['prodiPenyelenggara'],
            'nama_kegiatan' => $kegiatanDB['namaKegiatan'],
            'gambaran_umum' => $kegiatanDB['gambaranUmum'],
            'penerima_manfaat' => $kegiatanDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $kegiatanDB['metodePelaksanaan'],
            'tanggal_mulai' => $kegiatanDB['tanggalMulai'] ?? '-',
            'tanggal_selesai' => $kegiatanDB['tanggalSelesai'] ?? '-',
            'surat_pengantar' => $kegiatanDB['suratPengantar'] ?? ''
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
            'status' => ucfirst($kegiatanDB['status_text'] ?? 'Menunggu'),
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            'kegiatan_data' => $kak_formatted,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'tahapan_data' => $tahapan,
            'rab_data' => $rab,
            'kode_mak' => $kegiatanDB['buktiMAK'] ?? '-',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kak', $data, 'app');
    }

    /**
     * Menangani submit rincian kegiatan dengan controller baru berbasis MVC.
     */
    public function submitRincian(): void
    {
        $controller = new \Controllers\Admin\AdminController($this->db);
        $controller->submitRincian();
    }

    /**
     * Mendownload surat pengantar dengan validasi keamanan.
     */
    public function downloadSurat($filename) {
        $safe_filename = basename($filename);
        $upload_dir = realpath(__DIR__ . '/../../../public/uploads/surat/');
        
        if ($upload_dir === false) {
            http_response_code(500);
            echo "Direktori upload tidak ditemukan.";
            return;
        }
        
        $file_path = realpath($upload_dir . DIRECTORY_SEPARATOR . $safe_filename);
        
        if ($file_path === false || !file_exists($file_path)) {
            http_response_code(404);
            echo "File tidak ditemukan.";
            return;
        }
        
        if (strpos($file_path, $upload_dir) !== 0) {
            http_response_code(403);
            error_log("[SECURITY] Path traversal attempt blocked: {$filename} from IP: {$_SERVER['REMOTE_ADDR']}");
            echo "Akses ditolak.";
            return;
        }
        
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_extensions)) {
            http_response_code(403);
            echo "Tipe file tidak diizinkan.";
            return;
        }

        $mime_types = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png'
        ];

        header('Content-Type: ' . ($mime_types[$extension] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $safe_filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        readfile($file_path);
        exit;
    }
}