<?php
// File: src/controllers/Admin/AdminPengajuanLpjController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';

class AdminPengajuanLpjController extends Controller {
    
    private $model;

    public function __construct() {
        parent::__construct();
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

    // ============================================
    // FILE 2: AdminPengajuanLpjController.php - Enhanced show() with Debug
    // ============================================

    // Di AdminPengajuanLpjController.php, ganti method show dengan:

    public function show($id, $data_dari_router = []) {
        error_log("=== AdminPengajuanLpjController::show START ===");
        error_log("LPJ ID: " . $id);
        
        try {
            $ref = $_GET['ref'] ?? 'lpj'; 
            $base_url = "/docutrack/public/admin";
            $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

            // Ambil detail LPJ dari database
            $lpj_detail = $this->model->getDetailLPJ($id);
            
            if (!$lpj_detail) {
                error_log("ERROR: LPJ detail tidak ditemukan untuk ID: " . $id);
                $_SESSION['flash_error'] = 'Data LPJ tidak ditemukan';
                header("Location: $back_url");
                exit;
            }
            
            $status = strtolower(trim($lpj_detail['status'] ?? 'draft'));
            error_log("Status: " . $status);

            $kegiatan_data = [
                'kegiatanId' => $lpj_detail['kegiatanId'] ?? 0,
                'id' => $lpj_detail['kegiatanId'] ?? 0,
                'nama_kegiatan' => $lpj_detail['nama_kegiatan'] ?? 'N/A',
                'pengusul' => $lpj_detail['pengusul'] ?? 'N/A',
                'nim' => $lpj_detail['nim'] ?? '',
                'prodi' => $lpj_detail['prodi'] ?? '',
                'jurusan' => $lpj_detail['jurusan'] ?? ''
            ];
            
            // Ambil RAB items
            $rab_items_merged = [];
            
            if (!empty($lpj_detail['kakId']) && !empty($lpj_detail['lpjId'])) {
                error_log("Fetching RAB for lpjId: {$lpj_detail['lpjId']}, kakId: {$lpj_detail['kakId']}");
                $rab_items_merged = $this->model->getRABForLPJ($lpj_detail['lpjId'], $lpj_detail['kakId']);
                
                if (empty($rab_items_merged)) {
                    error_log("WARNING: No items returned from getRABForLPJ");
                }
            } else {
                error_log("ERROR: Missing lpjId or kakId!");
                error_log("lpjId: " . ($lpj_detail['lpjId'] ?? 'NULL'));
                error_log("kakId: " . ($lpj_detail['kakId'] ?? 'NULL'));
            }
            
            // Cek upload status
            $all_bukti_uploaded = true;
            $total_items = 0;
            $uploaded_items = 0;
            
            foreach ($rab_items_merged as $kategori => $items) {
                foreach ($items as $item) {
                    $total_items++;
                    if (!empty($item['bukti_file'])) {
                        $uploaded_items++;
                    } else {
                        $all_bukti_uploaded = false;
                    }
                }
            }
            
            error_log("Items: {$total_items}, Uploaded: {$uploaded_items}");

            $data = array_merge($data_dari_router, [
                'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data['nama_kegiatan']),
                'status' => $status,
                'kegiatan_data' => $kegiatan_data,
                'rab_items' => $rab_items_merged,
                'komentar_revisi' => [],
                'back_url' => $back_url,
                'lpj_id' => $lpj_detail['lpjId'] ?? $id,
                'all_bukti_uploaded' => $all_bukti_uploaded,
                'total_items' => $total_items,
                'uploaded_items' => $uploaded_items
            ]);

            error_log("=== AdminPengajuanLpjController::show END ===");
            
            $this->view('pages/admin/detail_lpj', $data, 'app');
            
        } catch (Exception $e) {
            error_log("EXCEPTION in show(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $_SESSION['flash_error'] = 'Terjadi kesalahan saat memuat data LPJ';
            header("Location: " . $base_url . "/pengajuan-lpj");
            exit;
        }
    }
        
        // ============================================
    // FILE 2: AdminPengajuanLpjController.php - Fix uploadBukti
    // ============================================

    /**
     * Upload bukti dan UPDATE tbl_lpj_item
     */
    public function uploadBukti() {
        error_log("=== uploadBukti START ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            // ✅ PERBAIKAN: Validasi file upload
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $errorMsg = 'File tidak ditemukan';
                if (isset($_FILES['file']['error'])) {
                    switch ($_FILES['file']['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $errorMsg = 'File terlalu besar';
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $errorMsg = 'File hanya terupload sebagian';
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $errorMsg = 'Tidak ada file yang diupload';
                            break;
                        default:
                            $errorMsg = 'Error upload file (code: ' . $_FILES['file']['error'] . ')';
                    }
                }
                throw new Exception($errorMsg);
            }

            // ✅ PERBAIKAN: Ambil item_id dari POST
            $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
            
            error_log("Item ID from POST: " . $itemId);
            
            if (!$itemId || $itemId <= 0) {
                throw new Exception('Item ID tidak ditemukan atau tidak valid');
            }

            $file = $_FILES['file'];
            
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Tipe file tidak diizinkan. Hanya JPG, JPEG, dan PNG.');
            }
            
            // Validasi ukuran (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
            }

            // ✅ PERBAIKAN: Path upload yang benar
            $uploadDir = __DIR__ . '/../../../public/uploads/lpj/';
            
            // Buat direktori jika belum ada
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception('Gagal membuat direktori upload');
                }
                error_log("Created directory: " . $uploadDir);
            }

            // Generate nama file unik
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'bukti_lpj_' . $itemId . '_' . time() . '_' . uniqid() . '.' . $ext;
            $targetFile = $uploadDir . $filename;

            error_log("Target file path: " . $targetFile);

            // Upload file
            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                throw new Exception('Gagal menyimpan file ke server');
            }
            
            error_log("File uploaded successfully: " . $filename);
            
            // ✅ UPDATE database - tbl_lpj_item
            $updateQuery = "UPDATE tbl_lpj_item SET fileBukti = ? WHERE lpjItemId = ?";
            $stmt = mysqli_prepare($this->db, $updateQuery);
            
            if (!$stmt) {
                // Rollback: hapus file yang sudah diupload
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
                throw new Exception('Database error: ' . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($stmt, "si", $filename, $itemId);
            
            if (!mysqli_stmt_execute($stmt)) {
                // Rollback: hapus file yang sudah diupload
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
                mysqli_stmt_close($stmt);
                throw new Exception('Gagal update database: ' . mysqli_stmt_error($stmt));
            }
            
            $affectedRows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            
            error_log("Database updated - affected rows: " . $affectedRows);
            
            if ($affectedRows === 0) {
                error_log("WARNING: No rows affected - item ID might not exist: " . $itemId);
            }
            
            error_log("=== uploadBukti END (SUCCESS) ===");
            
            echo json_encode([
                'success' => true, 
                'message' => 'Bukti berhasil diupload',
                'filename' => $filename,
                'item_id' => $itemId
            ]);
            
        } catch (Exception $e) {
            error_log("ERROR in uploadBukti: " . $e->getMessage());
            error_log("=== uploadBukti END (FAILED) ===");
            
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
        
        // Di AdminPengajuanLpjController.php, ganti method submitLpj dengan:

    public function submitLpj() {
        error_log("=== submitLpj START ===");
        error_log("POST data: " . print_r($_POST, true));
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        try {
            // Ambil data dari POST
            $kegiatanId = isset($_POST['kegiatan_id']) ? intval($_POST['kegiatan_id']) : 0;
            $itemsJson = $_POST['items'] ?? '[]';

            error_log("kegiatanId: " . $kegiatanId);
            error_log("itemsJson: " . $itemsJson);

            if (!$kegiatanId || $kegiatanId <= 0) {
                throw new Exception('ID Kegiatan tidak valid');
            }

            $items = json_decode($itemsJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Format data item tidak valid: ' . json_last_error_msg());
            }

            if (empty($items) || !is_array($items)) {
                throw new Exception('Data item LPJ tidak ditemukan');
            }

            error_log("Total items to process: " . count($items));

            // ✅ Cek apakah LPJ sudah ada untuk kegiatan ini
            $checkQuery = "SELECT l.lpjId, l.submittedAt 
                        FROM tbl_lpj l 
                        WHERE l.kegiatanId = ? 
                        LIMIT 1";
            
            $stmt = mysqli_prepare($this->db, $checkQuery);
            mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $lpjData = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$lpjData) {
                throw new Exception('Data LPJ tidak ditemukan untuk kegiatan ini');
            }

            $lpjId = $lpjData['lpjId'];
            error_log("LPJ ID: " . $lpjId);

            // Mulai Transaction
            mysqli_begin_transaction($this->db);

            try {
                // ✅ UPDATE setiap item LPJ dengan realisasi (fileBukti sudah ada dari upload sebelumnya)
                $updateQuery = "UPDATE tbl_lpj_item 
                            SET subTotal = ? 
                            WHERE lpjItemId = ? AND lpjId = ?";
                
                $stmtUpdate = mysqli_prepare($this->db, $updateQuery);
                
                if (!$stmtUpdate) {
                    throw new Exception('Failed to prepare update statement: ' . mysqli_error($this->db));
                }

                $updatedCount = 0;
                
                foreach ($items as $item) {
                    $lpjItemId = isset($item['lpj_item_id']) ? intval($item['lpj_item_id']) : 0;
                    $realisasi = isset($item['realisasi']) ? floatval($item['realisasi']) : 0;
                    
                    if ($lpjItemId <= 0) {
                        error_log("WARNING: Invalid lpj_item_id: " . print_r($item, true));
                        continue;
                    }

                    error_log("Updating item {$lpjItemId}: realisasi = {$realisasi}");

                    mysqli_stmt_bind_param($stmtUpdate, "dii", $realisasi, $lpjItemId, $lpjId);
                    
                    if (!mysqli_stmt_execute($stmtUpdate)) {
                        throw new Exception('Failed to update item ' . $lpjItemId . ': ' . mysqli_stmt_error($stmtUpdate));
                    }
                    
                    $affectedRows = mysqli_stmt_affected_rows($stmtUpdate);
                    error_log("Item {$lpjItemId} - affected rows: {$affectedRows}");
                    
                    if ($affectedRows > 0) {
                        $updatedCount++;
                    }
                }
                
                mysqli_stmt_close($stmtUpdate);
                
                error_log("Total items updated: {$updatedCount}");

                // ✅ Hitung Grand Total dari subTotal
                $grandTotalQuery = "SELECT SUM(subTotal) as grandTotal 
                                FROM tbl_lpj_item 
                                WHERE lpjId = ?";
                
                $stmtTotal = mysqli_prepare($this->db, $grandTotalQuery);
                mysqli_stmt_bind_param($stmtTotal, "i", $lpjId);
                mysqli_stmt_execute($stmtTotal);
                $resultTotal = mysqli_stmt_get_result($stmtTotal);
                $totalData = mysqli_fetch_assoc($resultTotal);
                mysqli_stmt_close($stmtTotal);
                
                $grandTotal = $totalData['grandTotal'] ?? 0;
                error_log("Calculated Grand Total: " . $grandTotal);

                // ✅ UPDATE tbl_lpj: set submittedAt dan grandTotal
                $updateLpjQuery = "UPDATE tbl_lpj 
                                SET submittedAt = NOW(), 
                                    grandTotal = ? 
                                WHERE lpjId = ?";
                
                $stmtLpj = mysqli_prepare($this->db, $updateLpjQuery);
                mysqli_stmt_bind_param($stmtLpj, "di", $grandTotal, $lpjId);
                
                if (!mysqli_stmt_execute($stmtLpj)) {
                    throw new Exception('Failed to update tbl_lpj: ' . mysqli_stmt_error($stmtLpj));
                }
                
                mysqli_stmt_close($stmtLpj);
                
                error_log("tbl_lpj updated with submittedAt and grandTotal");

                // Commit transaction
                mysqli_commit($this->db);
                
                error_log("=== submitLpj END (SUCCESS) ===");

                echo json_encode([
                    'success' => true, 
                    'message' => 'LPJ berhasil diajukan ke Bendahara',
                    'lpj_id' => $lpjId,
                    'grand_total' => $grandTotal,
                    'items_updated' => $updatedCount
                ]);

            } catch (Exception $e) {
                mysqli_rollback($this->db);
                throw $e;
            }

        } catch (Exception $e) {
            error_log("ERROR in submitLpj: " . $e->getMessage());
            error_log("=== submitLpj END (FAILED) ===");
            
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
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