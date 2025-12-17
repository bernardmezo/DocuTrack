<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\KegiatanService;
use App\Exceptions\ValidationException;
use Exception;

class AdminController extends Controller
{
    private $kegiatanService;

    public function __construct()
    {
        parent::__construct();
        $this->kegiatanService = new KegiatanService($this->db);
    }

    public function submitRincian(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }

        $kegiatanIdFromPost = $_POST['id_kegiatan'] ?? $_POST['kegiatan_id'] ?? null;
        $errorRedirectUrl = '/docutrack/public/admin/pengajuan-kegiatan/show/' . ($kegiatanIdFromPost ? (int)$kegiatanIdFromPost : '') . '?mode=rincian';

        try {
            $this->kegiatanService->processRincianKegiatan($_POST, $_FILES['surat_pengantar'] ?? null);

            $this->redirectWithMessage('/docutrack/public/admin/pengajuan-kegiatan', 'success', 'Rincian kegiatan berhasil disimpan dan dikirim ke PPK.');
        } catch (ValidationException $e) {
            error_log("AdminController::submitRincian - ValidationException: " . $e->getMessage());
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Validasi gagal: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log("AdminController::submitRincian - Exception: " . $e->getMessage());
            error_log("AdminController::submitRincian - Stack trace: " . $e->getTraceAsString());
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
