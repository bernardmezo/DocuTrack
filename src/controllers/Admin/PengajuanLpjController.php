<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\AdminService;
use App\Services\LpjService; // Diganti dari BendaharaService
// Removed: use App\Services\ValidationService;
use App\Exceptions\ValidationException;
use Exception;

class PengajuanLpjController extends Controller
{
    private $adminService;
    private $lpjService;
    // validationService is now inherited from base Controller

    public function __construct()
    {
        parent::__construct();
        $this->adminService = new AdminService($this->db);
        $this->lpjService = new LpjService($this->db); // Menggunakan LpjService
        // $this->validationService is already set in parent::__construct()
    }

    public function index($data_dari_router = [])
    {
        $list_lpj = $this->safeModelCall($this->adminService, 'getDashboardLPJ', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan LPJ',
            'list_lpj' => $list_lpj ?? []
        ]);

        $this->view('pages/admin/pengajuan_lpj_list', $data, 'admin');
    }

    public function show($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'lpj';
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

        $lpj_detail = $this->safeModelCall($this->adminService, 'getDetailLPJ', [$id], null);

        if (!$lpj_detail) {
            $this->redirectWithMessage($back_url, 'error', 'Data LPJ tidak ditemukan.');
        }

        $status = $lpj_detail['status'];
        $kegiatan_data = [
            'nama_kegiatan' => $lpj_detail['nama_kegiatan'],
            'pengusul' => $lpj_detail['pengusul']
        ];

        $rab_items_merged = [];
        if (!empty($lpj_detail['kakId'])) {
            // Pass lpjId untuk mendapatkan data bukti yang sudah diupload
            $rab_items_merged = $this->safeModelCall($this->adminService, 'getRABForLPJ', [$lpj_detail['kakId'], $id], []);
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data,
            'rab_items' => $rab_items_merged,
            'komentar_revisi' => [],
            'back_url' => $back_url,
            'lpj_id' => $id  // Tambahkan lpjId untuk referensi
        ]);

        $this->view('pages/admin/detail_lpj', $data, 'admin');
    }

    public function uploadBukti()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Metode tidak diizinkan', 405);
            }

            // Validasi input
            $rules = ['item_id' => 'required|numeric'];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new ValidationException('File tidak valid atau tidak ditemukan', ['file' => ['File bukti wajib diunggah.']]);
            }

            // Panggil service
            $result = $this->lpjService->uploadLpjBukti((int)$validatedData['item_id'], $_FILES['file']);

            echo json_encode($result);
        } catch (ValidationException $e) {
            http_response_code(422); // Unprocessable Entity
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $e->getErrors()]);
        } catch (Exception $e) {
            http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function submitLpj()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Metode tidak diizinkan', 405);
            }

            // Validasi input dasar
            $rules = ['kegiatan_id' => 'required|numeric'];
            $validatedData = $this->validationService->validate($_POST, $rules);

            $itemsJson = $_POST['items'] ?? '[]';
            $items = json_decode($itemsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ValidationException('Format data item tidak valid.', ['items' => ['JSON tidak valid.']]);
            }

            // Panggil service dengan data yang sudah divalidasi dan di-decode
            $result = $this->lpjService->submitLpj((int)$validatedData['kegiatan_id'], $items);

            echo json_encode($result);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $e->getErrors()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan internal: ' . $e->getMessage()]);
        }
    }

    public function verifikasi($lpjId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Metode tidak diizinkan.'
            );
        }

        try {
            if ($this->lpjService->verifikasiLpj((int)$lpjId)) {
                $this->redirectWithMessage(
                    '/docutrack/public/admin/pengajuan-lpj',
                    'success',
                    'LPJ berhasil diverifikasi dan disetujui!'
                );
            } else {
                throw new Exception('Gagal memverifikasi LPJ.');
            }
        } catch (Exception $e) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                $e->getMessage()
            );
        }
    }

    public function tolak($lpjId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Metode tidak diizinkan.'
            );
        }

        try {
            $rules = ['komentar' => 'required'];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->lpjService->tolakLpj((int)$lpjId, $validatedData['komentar'])) {
                $this->redirectWithMessage(
                    '/docutrack/public/admin/pengajuan-lpj',
                    'success',
                    'LPJ berhasil ditolak dan dikembalikan untuk revisi.'
                );
            } else {
                throw new Exception('Gagal menolak LPJ.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj/show/' . $lpjId,
                'error',
                'Komentar penolakan wajib diisi.'
            );
        } catch (Exception $e) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                $e->getMessage()
            );
        }
    }

    public function submitRevisi($lpjId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                'Metode tidak diizinkan.'
            );
        }

        try {
            $rawKomentar = $_POST['komentar'] ?? [];
            $komentarRevisi = [];

            // Basic check if any comment is provided
            $hasComment = false;
            foreach ($rawKomentar as $kategori => $comment) {
                $trimmedComment = trim($comment);
                if (!empty($trimmedComment)) {
                    $komentarRevisi[] = [
                        'targetKolom' => $kategori, // Asumsi kategori adalah targetKolom
                        'komentar' => $trimmedComment
                    ];
                    $hasComment = true;
                }
            }

            if (!$hasComment) {
                throw new ValidationException('Minimal isi satu catatan revisi.', ['komentar' => ['Minimal satu komentar revisi wajib diisi.']]);
            }

            if ($this->lpjService->submitRevisiLpj((int)$lpjId, $komentarRevisi)) {
                $this->redirectWithMessage(
                    '/docutrack/public/admin/pengajuan-lpj',
                    'success',
                    'Revisi LPJ berhasil dikirim.'
                );
            } else {
                throw new Exception('Gagal mengirim revisi LPJ.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj/show/' . $lpjId,
                'error',
                'Validasi gagal: ' . $e->getMessage()
            );
        } catch (Exception $e) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-lpj',
                'error',
                $e->getMessage()
            );
        }
    }
}
