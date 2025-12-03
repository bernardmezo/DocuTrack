<?php
// File: src/controllers/Admin/AdminPengajuanLpjController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';

class AdminPengajuanLpjController extends Controller {
    
    private $model;

    public function __construct() {
        $this->model = new adminModel($this->db);
    }

    /**
     * Menampilkan HALAMAN LIST PENGAJUAN LPJ
     */
    public function index($data_dari_router = []) { 
        
        // Handle action dari GET parameter
        $action = $_GET['action'] ?? 'list';
        $id = $_GET['id'] ?? null;
        
        // Route ke method yang sesuai
        if ($action === 'verifikasi' && $id) {
            return $this->verifikasi($id);
        } elseif ($action === 'tolak' && $id) {
            return $this->tolak($id);
        }
        
        // Default: Tampilkan list dengan type safety
        $list_lpj = $this->safeModelCall($this->model, 'getDashboardLPJ', [], []);
        
        // Support feedback messages (already set in session by action methods)
        // No need to unset here since View will handle it
        
        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan LPJ',
            'list_lpj' => $list_lpj ?? []
        ]);

        $this->view('pages/admin/pengajuan_lpj_list', $data, 'app');
    }
    
    /**
     * Verifikasi/Approve LPJ
     */
    public function verifikasi($lpjId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['confirm'])) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Konfirmasi verifikasi diperlukan'
            );
        }
        
        require_once '../src/model/bendaharaModel.php';
        $bendaharaModel = new bendaharaModel($this->db);
        
        $result = $this->safeModelCall($bendaharaModel, 'approveLPJ', [$lpjId], false);
        
        if ($result) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'success',
                'LPJ berhasil diverifikasi dan disetujui'
            );
        } else {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Gagal memverifikasi LPJ'
            );
        }
    }
    
    /**
     * Tolak LPJ dengan komentar
     */
    public function tolak($lpjId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Method not allowed'
            );
        }
        
        $komentar = trim($_POST['komentar'] ?? '');
        
        if (empty($komentar)) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj?action=detail&id=' . $lpjId,
                'error',
                'Komentar penolakan wajib diisi'
            );
        }
        
        // TODO: Implementasi logic penolakan LPJ di Model
        // $result = $this->model->tolakLPJ($lpjId, $komentar);
        
        $this->redirectWithMessage(
            '/docutrack/public/admin/pengajuan-lpj',
            'success',
            'LPJ ditolak dan dikembalikan untuk revisi'
        );
    }

    public function show($id, $data_dari_router = []) {
        
        $ref = $_GET['ref'] ?? 'lpj'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

        // Ambil detail LPJ dari database
        $lpj_detail = $this->model->getDetailLPJ($id);
        
        if (!$lpj_detail) {
            header("Location: $back_url");
            exit;
        }
        
        $status = $lpj_detail['status'];

        $kegiatan_data = [
            'nama_kegiatan' => $lpj_detail['nama_kegiatan'],
            'pengusul' => $lpj_detail['pengusul']
        ];
        
        // Ambil RAB items dari database (jika ada kakId)
        $rab_items_merged = [];
        if (!empty($lpj_detail['kakId'])) {
            $rab_items_merged = $this->model->getRABForLPJ($lpj_detail['kakId']);
        }
        
        // Jika tidak ada RAB, tampilkan array kosong
        if (empty($rab_items_merged)) {
            $rab_items_merged = [];
        }
        
        // Komentar revisi (jika status revisi)
        $komentar_revisi = [];
        if (strtolower($status) === 'revisi') {
            $komentar_revisi = [
                'pesan_umum' => 'Mohon perbaiki item yang diberi komentar dan upload ulang bukti yang sesuai.'
            ];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data,
            'rab_items' => $rab_items_merged,
            'komentar_revisi' => $komentar_revisi,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_lpj', $data, 'app');
    }
    
    public function uploadBukti() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File tidak ditemukan atau error upload.']);
            return;
        }

        // Gunakan helper validation yang sudah dibuat
        require_once '../src/helpers/security_helper.php';
        
        $validation = validateFileUpload($_FILES['file'], [
            'allowed_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png']
        ]);
        
        if (!$validation['valid']) {
            echo json_encode(['success' => false, 'message' => $validation['error']]);
            return;
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Tentukan direktori upload
        $targetDir = __DIR__ . '/../../../public/uploads/lpj/';
        
        // ✅ CEK DAN BUAT DIREKTORI JIKA BELUM ADA
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                echo json_encode(['success' => false, 'message' => 'Gagal membuat direktori upload.']);
                return;
            }
        }

        // Generate nama file unik
        $filename = 'bukti_' . time() . '_' . uniqid() . '.' . $ext;
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Bukti berhasil diupload',
                'filename' => $filename
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file ke server.']);
        }
    }
    
    public function submitLpj() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        // Load Helper Model for Transaction
        require_once __DIR__ . '/../../model/lpj/lpjModel.php';

        $kegiatanId = $_POST['kegiatan_id'] ?? null;
        $itemsJson = $_POST['items'] ?? '[]';

        if (!$kegiatanId) {
            echo json_encode(['success' => false, 'message' => 'ID Kegiatan tidak valid.']);
            return;
        }

        $items = json_decode($itemsJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Format data item tidak valid.']);
            return;
        }

        // 1. Cek apakah LPJ sudah ada untuk kegiatan ini
        $existingLpj = getLpjWithItemsByKegiatanId($kegiatanId);
        $lpjId = null;

        // Mulai Transaction via Model logic
        // Karena Model lpjModel.php yang baru menggunakan procedural style dengan global $conn,
        // kita harus berhati-hati. Idealnya kita wrap di try-catch sini.

        try {
            if ($existingLpj) {
                $lpjId = $existingLpj['lpj_id'];
                // Hapus item lama (Reset)
                deleteLpjItemsByLpjId($lpjId);
            } else {
                // Insert Baru
                $lpjId = insertLpj($kegiatanId);
                if (!$lpjId) throw new Exception("Gagal membuat draft LPJ.");
            }

            // Insert Item Baru
            if (!empty($items)) {
                // Mapping data JSON ke format Database
                $dbItems = [];
                foreach ($items as $item) {
                    $dbItems[] = [
                        'jenis_belanja' => $item['kategori'] ?? 'Lainnya',
                        'uraian' => $item['uraian'] ?? '',
                        'rincian' => $item['rincian'] ?? '',
                        'satuan' => $item['satuan'] ?? '',
                        'total_harga' => floatval($item['harga_satuan'] ?? 0),
                        'sub_total' => floatval($item['total'] ?? 0), // Frontend sends 'total' as subtotal
                        'file_bukti_nota' => $item['file_bukti'] ?? null
                    ];
                }
                
                if (!insertLpjItems($lpjId, $dbItems)) {
                    throw new Exception("Gagal menyimpan item LPJ.");
                }
            }

            // Update Grand Total & Status
            updateLpjGrandTotal($lpjId);
            updateLpjStatus($lpjId, 'Submitted');

            echo json_encode([
                'success' => true, 
                'message' => 'LPJ berhasil diajukan ke Bendahara'
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function submitRevisi() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Revisi LPJ berhasil disubmit'
        ]);
    }
}