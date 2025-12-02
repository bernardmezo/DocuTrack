<?php
// File: src/controllers/Admin/AdminPengajuanKegiatanController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php'; // Load Model
require_once '../src/helpers/logger_helper.php'; // Load Logger Helper

class AdminPengajuanKegiatanController extends Controller {
    
    // HAPUS function getAllKegiatan() { ... } KARENA SUDAH TIDAK DIPAKAI

    /**
     * Menampilkan HALAMAN LIST dengan Role-Based Filtering
     * 
     * LOGIC:
     * - Super Admin: Lihat SEMUA kegiatan
     * - Admin Jurusan: Hanya kegiatan dari jurusan sendiri
     * 
     * Ref: ANALYSIS_REPORT.md - Poin 3.D (Logic Mismatch: Filter Admin)
     */
    public function index($data_dari_router = []) { 
        // 1. Panggil Model
        $model = new adminModel();
        
        // 2. Ambil data user dari session untuk filtering
        $userRole = $_SESSION['user_role'] ?? '';
        $userJurusan = $_SESSION['user_jurusan'] ?? null; // Nama jurusan user
        
        // 3. Ambil data dari database dengan filter berbasis role
        if ($userRole === 'super-admin' || $userRole === 'superadmin') {
            // Super Admin: Lihat SEMUA data
            $all_kegiatan = $model->getDashboardKAK();
        } else {
            // Admin Jurusan: Hanya data jurusan sendiri
            // Panggil method baru yang menerima filter jurusan
            $all_kegiatan = $model->getDashboardKAKByJurusan($userJurusan);
        }
        
        // 4. Filter hanya yang statusnya "Menunggu" di posisi Admin (posisi 2)
        $list_kegiatan_disetujui = array_filter($all_kegiatan, function($item) {
            return isset($item['posisi']) && strtolower($item['posisi']) === '2' && isset($item['status']) && strtolower($item['status']) === 'menunggu';
        });

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => $list_kegiatan_disetujui 
        ]);

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    /**
     * Menampilkan HALAMAN DETAIL atau RINCIAN
     */
    public function show($id, $data_dari_router = []) {
        $mode = $_GET['mode'] ?? 'detail';
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        // 1. Panggil Model
        $model = new adminModel();
        
        // 2. Ambil Detail Kegiatan dari DB
        $kegiatanDB = $model->getDetailKegiatan($id);

        if (!$kegiatanDB) {
            echo "<h1>404 - Kegiatan dengan ID $id tidak ditemukan.</h1>";
            exit;
        }
        
        // --- MODE RINCIAN (FORM INPUT) ---
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
        
        // --- MODE DEFAULT (DETAIL LENGKAP) ---
        
        // Ambil data relasi (RAB, IKU, dll) menggunakan method yang sudah ada di Model
        $kakId = $kegiatanDB['kakId'];
        
        $iku_string = $kegiatanDB['iku'] ?? ''; // Di DB biasanya string dipisah koma
        $iku_array = !empty($iku_string) ? explode(',', $iku_string) : [];
        
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan = $model->getTahapanByKAK($kakId); // Return array string
        $rab = $model->getRABByKAK($kakId);

        // Cek URL Surat Pengantar (Jika ada di tbl_kegiatan)
        $surat_pengantar_url = '';
        if (!empty($kegiatanDB['suratPengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatanDB['suratPengantar'];
        }

        // Mapping Data KAK agar sesuai View detail_kegiatan.php
        $kak_formatted = [
            'nama_pengusul' => $kegiatanDB['pemilikKegiatan'],
            'nim' => $kegiatanDB['nimPelaksana'],
            'jurusan' => $kegiatanDB['jurusanPenyelenggara'],
            'prodi' => $kegiatanDB['prodiPenyelenggara'],
            'nama_kegiatan' => $kegiatanDB['namaKegiatan'],
            'gambaran_umum' => $kegiatanDB['gambaranUmum'],
            'penerima_manfaat' => $kegiatanDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $kegiatanDB['metodePelaksanaan'],
            'tanggal_mulai' => $kegiatanDB['tanggalMulai'] ?? '-', // Perlu kolom ini di DB jika ada
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
            'tahapan_data' => $tahapan, // Tambahan jika view membutuhkannya
            'rab_data' => $rab,
            'kode_mak' => $kegiatanDB['buktiMAK'] ?? '-',
            'komentar_revisi' => [], // Ambil dari model jika fitur revisi sudah aktif
            'komentar_penolakan' => '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kegiatan', $data, 'app');
    }

    /**
     * Handle Submit Rincian Kegiatan (Upload Surat & Update DB)
     */
    public function submitRincian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo "Method not allowed"; return;
        }

        // 1. Ambil Data dari Form (Input Text)
        $kegiatan_id = $_POST['kegiatan_id'] ?? null;
        
        $data_update = [
            'namaPj'        => $_POST['penanggung_jawab'] ?? '',
            'nimNipPj'         => $_POST['nim_nip_pj'] ?? '',
            'tgl_mulai'   => $_POST['tanggal_mulai'] ?? null,
            'tgl_selesai' => $_POST['tanggal_selesai'] ?? null
        ];

        // 2. Logika Upload File
        $file_name = null; // Default null jika tidak ada file baru
        
        if (isset($_FILES['surat_pengantar']) && $_FILES['surat_pengantar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['surat_pengantar']['tmp_name'];
            $file_original_name = $_FILES['surat_pengantar']['name'];
            $file_ext = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
            
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            if (in_array($file_ext, $allowed_extensions)) {
                $file_name = 'surat_' . $kegiatan_id . '_' . time() . '.' . $file_ext;
                $upload_dir = __DIR__ . '/../../../public/uploads/surat';
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                
                if (!move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    // Gagal upload
                    header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id . '?mode=rincian&error=upload_failed');
                    exit;
                }
            }
        }

        // 3. Panggil Model untuk Update Semua Data
        $model = new adminModel();
        
        // Kirim data array + nama file (bisa null jika user tidak upload file baru)
        if ($model->updateRincianKegiatan($kegiatan_id, $data_update, $file_name)) {
            // Sukses
            header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id);
            exit;
        } else {
            // Gagal DB
            echo "Gagal update database.";
        }
    }

    // ... (Function downloadSurat tetap sama) ...
    /**
     * Download Surat Pengantar dengan validasi keamanan
     * SECURITY FIX: Implementasi path traversal protection
     * Ref: ANALYSIS_REPORT.md - Poin 3.A & DATABASE_AUDIT.md - Pilar 2
     */
    public function downloadSurat($filename) {
        // SECURITY: Gunakan basename() untuk menghapus path traversal attempts
        // Contoh serangan: ../../etc/passwd akan menjadi "passwd"
        $safe_filename = basename($filename);
        
        // Definisikan direktori upload yang diizinkan
        $upload_dir = realpath(__DIR__ . '/../../../public/uploads/surat');
        
        if ($upload_dir === false) {
            http_response_code(500);
            echo "Direktori upload tidak ditemukan.";
            return;
        }
        
        // Bangun full path dan resolve ke absolute path
        $file_path = realpath($upload_dir . DIRECTORY_SEPARATOR . $safe_filename);
        
        // SECURITY CHECK 1: File harus exist
        if ($file_path === false || !file_exists($file_path)) {
            http_response_code(404);
            echo "File tidak ditemukan.";
            return;
        }
        
        // SECURITY CHECK 2: File HARUS berada di dalam direktori upload
        // Ini menangkap upaya path traversal yang berhasil melewati basename()
        if (strpos($file_path, $upload_dir) !== 0) {
            http_response_code(403);
            error_log("[SECURITY] Path traversal attempt blocked: {$filename} from IP: {$_SERVER['REMOTE_ADDR']}");
            echo "Akses ditolak.";
            return;
        }
        
        // SECURITY CHECK 3: Validasi ekstensi file (whitelist)
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_extensions)) {
            http_response_code(403);
            echo "Tipe file tidak diizinkan.";
            return;
        }
        
        // Set Content-Type berdasarkan ekstensi
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