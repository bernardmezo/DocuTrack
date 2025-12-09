<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database; // Still needed for DokumenModel instantiation if it's not a service
use App\Models\DokumenModel;
use App\Exceptions\UploadException;
use Exception;
use App\Services\AdminService;
use App\Services\LpjService;
use App\Services\KegiatanService;

/**
 * UploadController - Handle file upload operations
 *
 * Controller untuk menangani upload file KAK, RAB, LPJ dengan
 * validation, progress tracking, dan access control.
 *
 * @category Controller
 * @package  DocuTrack\Controllers\Admin
 * @version  1.0.0
 */
class UploadController extends Controller
{
    // uploadService is now inherited from base Controller

    /**
     * @var DokumenModel
     */
    private $dokumenModel;

    /**
     * @var AdminService
     */
    private $adminService;

    /**
     * @var LpjService
     */
    private $lpjService;

    /**
     * @var KegiatanService
     */
    private $kegiatanService;

    /**
     * Constructor
     */
    public function __construct($db)
    {
        parent::__construct($db); // Pass the injected $db to the parent constructor

        // $this->uploadService is already set in parent::__construct()
        $this->dokumenModel = new DokumenModel($this->db); // Use $this->db
        $this->adminService = new AdminService($this->db);
        $this->lpjService = new LpjService($this->db);
        $this->kegiatanService = new KegiatanService($this->db);
    }
    /**
     * Upload KAK document
     *
     * POST /admin/upload/kak
     *
     * @return void
     */
    public function uploadKakDocument()
    {
        try {
            // Validate request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
            }

            // Check if file uploaded
            if (!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] === UPLOAD_ERR_NO_FILE) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Tidak ada file yang diupload'
                ], 400);
            }

            // Get reference ID (kegiatan_id)
            $kegiatanId = (int) ($_POST['kegiatan_id'] ?? 0);
            if ($kegiatanId === 0) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'ID kegiatan tidak valid'
                ], 400);
            }

            // --- START Authorization Check for Kegiatan ---
            $currentUserId = $_SESSION['user_id'] ?? 0;
            $currentUserRole = $_SESSION['user_role'] ?? '';

            $kegiatan = $this->kegiatanService->getKegiatanById($kegiatanId);

            if (!$kegiatan) {
                $this->jsonResponse(['success' => false, 'error' => 'Kegiatan tidak ditemukan.'], 404);
            }

            // Authorization: Uploader or Admin/SuperAdmin
            if ($kegiatan['pengusul_id'] != $currentUserId && !in_array($currentUserRole, ['admin', 'superadmin'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Anda tidak memiliki izin untuk mengupload dokumen ke kegiatan ini.'], 403);
            }
            // --- END Authorization Check for Kegiatan ---

            // Get description (optional)
            $description = $_POST['description'] ?? 'kak';

            // Upload file
            $fileInfo = $this->fileUploadService->uploadDocument($_FILES['dokumen'], 'kak', $description);

            // Save metadata to database
            $documentId = $this->dokumenModel->insert([
                'reference_type' => 'kak',
                'reference_id' => $kegiatanId,
                'original_name' => $fileInfo['original_name'],
                'filename' => $fileInfo['filename'],
                'file_path' => $fileInfo['relative_path'],
                'file_size' => $fileInfo['size'],
                'mime_type' => $fileInfo['mime_type'],
                'uploaded_by' => $_SESSION['user_id'] ?? 0
            ]);

            // Success response
            $this->jsonResponse([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'id' => $documentId,
                    'filename' => $fileInfo['filename'],
                    'original_name' => $fileInfo['original_name'],
                    'url' => '/admin/download/' . $documentId,
                    'size' => $fileInfo['size'],
                    'size_formatted' => $fileInfo['size_formatted'],
                    'mime_type' => $fileInfo['mime_type'],
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (UploadException $e) {
            // Upload validation error
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 422);
        } catch (Exception $e) {
            // Server error
            error_log('UploadController::uploadKakDocument Error: ' . $e->getMessage());

            $this->jsonResponse([
                'success' => false,
                'error' => 'Terjadi kesalahan saat upload file'
            ], 500);
        }
    }

    /**
     * Upload RAB attachment
     *
     * POST /admin/upload/rab
     *
     * @return void
     */
    public function uploadRabAttachment()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
            }

            if (!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] === UPLOAD_ERR_NO_FILE) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Tidak ada file yang diupload'
                ], 400);
            }

            // Get reference ID (rab_id)
            $rabId = (int) ($_POST['rab_id'] ?? 0);
            if ($rabId === 0) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'ID RAB tidak valid'
                ], 400);
            }

            // --- START Authorization Check for RAB ---
            $currentUserId = $_SESSION['user_id'] ?? 0;
            $currentUserRole = $_SESSION['user_role'] ?? '';

            // Fetch RAB details (assuming AdminService has a method to get RAB by ID with associated KAK/Kegiatan details)
            $rab = $this->adminService->getRABById($rabId); // Assuming AdminService can get RAB and its associated kegiatan info

            if (!$rab) {
                $this->jsonResponse(['success' => false, 'error' => 'RAB tidak ditemukan.'], 404);
            }

            // Authorization: Uploader (pengusul_id from associated kegiatan/KAK) or Admin/SuperAdmin
            // This assumes the RAB object (or its related KAK/Kegiatan) has an 'pengusul_id'
            if ($rab['pengusul_id'] != $currentUserId && !in_array($currentUserRole, ['admin', 'superadmin'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Anda tidak memiliki izin untuk mengupload dokumen ke RAB ini.'], 403);
            }
            // --- END Authorization Check for RAB ---

            $description = $_POST['description'] ?? 'rab';

            // Upload file

            $fileInfo = $this->fileUploadService->uploadDocument($_FILES['dokumen'], 'rab', $description);

            $documentId = $this->dokumenModel->insert([
                'reference_type' => 'rab',
                'reference_id' => $rabId,
                'original_name' => $fileInfo['original_name'],
                'filename' => $fileInfo['filename'],
                'file_path' => $fileInfo['relative_path'],
                'file_size' => $fileInfo['size'],
                'mime_type' => $fileInfo['mime_type'],
                'uploaded_by' => $_SESSION['user_id'] ?? 0
            ]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'File RAB berhasil diupload',
                'data' => [
                    'id' => $documentId,
                    'filename' => $fileInfo['filename'],
                    'original_name' => $fileInfo['original_name'],
                    'url' => '/admin/download/' . $documentId,
                    'size' => $fileInfo['size'],
                    'size_formatted' => $fileInfo['size_formatted'],
                    'mime_type' => $fileInfo['mime_type'],
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (UploadException $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 422);
        } catch (Exception $e) {
            error_log('UploadController::uploadRabAttachment Error: ' . $e->getMessage());

            $this->jsonResponse([
                'success' => false,
                'error' => 'Terjadi kesalahan saat upload file'
            ], 500);
        }
    }

    /**
     * Upload LPJ document
     *
     * POST /admin/upload/lpj
     *
     * @return void
     */
    public function uploadLpjDocument()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
            }

            if (!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] === UPLOAD_ERR_NO_FILE) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Tidak ada file yang diupload'
                ], 400);
            }

            // Get reference ID (lpj_id)
            $lpjId = (int) ($_POST['lpj_id'] ?? 0);
            if ($lpjId === 0) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'ID LPJ tidak valid'
                ], 400);
            }

            // --- START Authorization Check for LPJ ---
            $currentUserId = $_SESSION['user_id'] ?? 0;
            $currentUserRole = $_SESSION['user_role'] ?? '';

            // Fetch LPJ details (assuming LpjService has a method to get LPJ by ID with associated Kegiatan details)
            $lpj = $this->lpjService->getLpjById($lpjId); // Assuming LpjService can get LPJ and its associated kegiatan info

            if (!$lpj) {
                $this->jsonResponse(['success' => false, 'error' => 'LPJ tidak ditemukan.'], 404);
            }

            // Authorization: Uploader (pengusul_id from associated kegiatan) or Admin/SuperAdmin
            // This assumes the LPJ object (or its related Kegiatan) has an 'pengusul_id'
            if ($lpj['pengusul_id'] != $currentUserId && !in_array($currentUserRole, ['admin', 'superadmin'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Anda tidak memiliki izin untuk mengupload dokumen ke LPJ ini.'], 403);
            }
            // --- END Authorization Check for LPJ ---

            $description = $_POST['description'] ?? 'lpj';

            // Upload file

            $fileInfo = $this->fileUploadService->uploadDocument($_FILES['dokumen'], 'lpj', $description);

            $documentId = $this->dokumenModel->insert([
                'reference_type' => 'lpj',
                'reference_id' => $lpjId,
                'original_name' => $fileInfo['original_name'],
                'filename' => $fileInfo['filename'],
                'file_path' => $fileInfo['relative_path'],
                'file_size' => $fileInfo['size'],
                'mime_type' => $fileInfo['mime_type'],
                'uploaded_by' => $_SESSION['user_id'] ?? 0
            ]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'File LPJ berhasil diupload',
                'data' => [
                    'id' => $documentId,
                    'filename' => $fileInfo['filename'],
                    'original_name' => $fileInfo['original_name'],
                    'url' => '/admin/download/' . $documentId,
                    'size' => $fileInfo['size'],
                    'size_formatted' => $fileInfo['size_formatted'],
                    'mime_type' => $fileInfo['mime_type'],
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (UploadException $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 422);
        } catch (Exception $e) {
            error_log('UploadController::uploadLpjDocument Error: ' . $e->getMessage());

            $this->jsonResponse([
                'success' => false,
                'error' => 'Terjadi kesalahan saat upload file'
            ], 500);
        }
    }

    /**
     * Download file with access control
     *
     * GET /admin/download/{id}
     *
     * @param int $id Document ID
     * @return void
     */
    public function download($id)
    {
        try {
            // Get document from database
            $document = $this->dokumenModel->findById($id);

            if (!$document) {
                http_response_code(404);
                die('File tidak ditemukan');
            }

            // --- START Access Control Check ---
            $currentUserId = $_SESSION['user_id'] ?? 0;
            $currentUserRole = $_SESSION['user_role'] ?? ''; // Assuming user_role is set in session

            $isAuthorized = false;

            // 1. Allow if current user is the uploader
            if ($document['uploaded_by'] == $currentUserId) {
                $isAuthorized = true;
            }

            // 2. Allow if current user is an Admin or Super Admin
            if (in_array($currentUserRole, ['admin', 'superadmin'])) {
                $isAuthorized = true;
            }

            // TODO: (Advanced) Add more granular checks based on document type (e.g., is user involved in the referenced kegiatan/LPJ?)
            // Example: if ($document['reference_type'] === 'kak') {
            //              $kegiatan = $this->kegiatanService->getKegiatanById($document['reference_id']);
            //              if ($kegiatan && $kegiatan['pengusul_id'] == $currentUserId) { $isAuthorized = true; }
            //          }

            if (!$isAuthorized) {
                http_response_code(403);
                die('Anda tidak memiliki izin untuk mengunduh file ini.');
            }
            // --- END Access Control Check ---

            // Get file path
            $filePath = $this->fileUploadService->getUploadBasePath() . DIRECTORY_SEPARATOR . $document['file_path'];

            // Check if file exists
            if (!file_exists($filePath)) {
                http_response_code(404);
                echo 'File tidak ada di filesystem';
                exit;
            }

            // Get file info
            $fileInfo = $this->fileUploadService->getFileInfo($filePath);

            // Set headers for download
            header('Content-Type: ' . $fileInfo['mime_type']);
            header('Content-Disposition: attachment; filename="' . $document['original_name'] . '"');
            header('Content-Length: ' . $fileInfo['size']);
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            // Output file
            readfile($filePath);
            exit;
        } catch (Exception $e) {
            error_log('UploadController::download Error: ' . $e->getMessage());

            http_response_code(500);
            die('Terjadi kesalahan saat download file');
        }
    }

    /**
     * Delete file
     *
     * DELETE /admin/upload/{id}
     *
     * @param int $id Document ID
     * @return void
     */
    public function delete($id)
    {
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
            }

            // Get document from database
            $document = $this->dokumenModel->findById($id);

            if (!$document) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'File tidak ditemukan'
                ], 404);
            }

            // TODO: Add access control check
            // Check if user has permission to delete this file
            // For now, only allow uploader or admin
            $userId = $_SESSION['user_id'] ?? 0;
            $userRole = $_SESSION['user_role'] ?? '';

            if ($document['uploaded_by'] != $userId && !in_array($userRole, ['admin', 'super-admin'])) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Anda tidak memiliki akses untuk menghapus file ini'
                ], 403);
            }

            // Delete file from filesystem
            $filePath = $document['file_path'];
            $this->fileUploadService->delete($filePath);

            // Delete record from database
            $this->dokumenModel->delete($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
        } catch (UploadException $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            error_log('UploadController::delete Error: ' . $e->getMessage());

            $this->jsonResponse([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menghapus file'
            ], 500);
        }
    }

    /**
     * Get files by reference
     *
     * GET /admin/upload/list?type=kak&ref_id=123
     *
     * @return void
     */
    public function getFiles()
    {
        try {
            $type = $_GET['type'] ?? '';
            $refId = (int) ($_GET['ref_id'] ?? 0);

            if (!in_array($type, ['kak', 'rab', 'lpj'])) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Tipe dokumen tidak valid'
                ], 400);
            }

            if ($refId === 0) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Reference ID tidak valid'
                ], 400);
            }

            // Get files from database
            $files = $this->dokumenModel->findByReference($type, $refId);

            // Format response
            $formattedFiles = array_map(function ($file) {
                return [
                    'id' => $file['id'],
                    'filename' => $file['filename'],
                    'original_name' => $file['original_name'],
                    'size' => $file['file_size'],
                    'size_formatted' => $this->formatFileSize($file['file_size']),
                    'mime_type' => $file['mime_type'],
                    'uploaded_by' => $file['uploader_name'] ?? 'Unknown',
                    'uploaded_at' => $file['uploaded_at'],
                    'url' => '/admin/download/' . $file['id']
                ];
            }, $files);

            $this->jsonResponse([
                'success' => true,
                'data' => $formattedFiles,
                'total' => count($formattedFiles)
            ]);
        } catch (Exception $e) {
            error_log('UploadController::getFiles Error: ' . $e->getMessage());

            $this->jsonResponse([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil daftar file'
            ], 500);
        }
    }

    /**
     * Format file size to human readable
     *
     * @param int $bytes
     * @return string
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $exp = floor(log($bytes) / log(1024));

        return sprintf('%.2f %s', $bytes / pow(1024, $exp), $units[$exp]);
    }
}
