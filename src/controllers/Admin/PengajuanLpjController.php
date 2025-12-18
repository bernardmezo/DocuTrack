<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\AdminService;
use App\Services\LpjService;
use App\Exceptions\ValidationException;
use Exception;

class PengajuanLpjController extends Controller
{
    private $adminService;
    private $lpjService;

    public function __construct()
    {
        parent::__construct();
        $this->adminService = new AdminService($this->db);
        $this->lpjService = new LpjService($this->db);
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
            error_log("❌ LPJ tidak ditemukan atau KAK belum dibuat: lpjId=$id");
            $this->redirectWithMessage($back_url, 'error', 'Data LPJ tidak ditemukan atau KAK belum dibuat untuk kegiatan ini.');
            return;
        }

        if (empty($lpj_detail['kakId'])) {
            error_log("❌ KAK tidak ditemukan untuk kegiatan: kegiatanId={$lpj_detail['kegiatanId']}");
            $this->redirectWithMessage($back_url, 'error', 'KAK belum dibuat untuk kegiatan ini. Silakan buat KAK terlebih dahulu.');
            return;
        }

        $status = strtolower($lpj_detail['status'] ?? 'draft');
        
        $kegiatan_data = [
            'kegiatanId' => $lpj_detail['kegiatanId'],
            'nama_kegiatan' => $lpj_detail['nama_kegiatan'],
            'pengusul' => $lpj_detail['pengusul']
        ];

        error_log("📋 Fetching RAB: kakId={$lpj_detail['kakId']}, lpjId=$id");
        
        $rab_items_merged = [];
        if (!empty($lpj_detail['kakId'])) {
            $rab_items_merged = $this->safeModelCall($this->adminService, 'getRABForLPJ', [$lpj_detail['kakId']]);
        }

        if (empty($rab_items_merged)) {
            error_log("⚠️ Tidak ada data RAB untuk kakId={$lpj_detail['kakId']}");
        } else {
            error_log("✅ RAB berhasil diambil: " . count($rab_items_merged) . " kategori");
        }

        $total_items = 0;
        $uploaded_items = 0;
        
        foreach ($rab_items_merged as $kategori => $items) {
            foreach ($items as $item) {
                $total_items++;
                if (!empty($item['bukti_file'])) {
                    $uploaded_items++;
                }
            }
        }
        
        $all_bukti_uploaded = ($total_items > 0 && $uploaded_items === $total_items);

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data,
            'rab_items' => $rab_items_merged,
            'total_items' => $total_items,
            'uploaded_items' => $uploaded_items,
            'all_bukti_uploaded' => $all_bukti_uploaded,
            'komentar_revisi' => [],
            'back_url' => $back_url,
            'lpj_id' => $id,
            'kak_id' => $lpj_detail['kakId'],
        ]);

        $this->view('pages/admin/detail_lpj_new', $data, 'admin');
    }

    public function submitLpj()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Metode tidak diizinkan', 405);
            }

            $rules = ['kegiatan_id' => 'required|numeric'];
            $validatedData = $this->validationService->validate($_POST, $rules);

            $itemsJson = $_POST['items'] ?? '[]';
            $items = json_decode($itemsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ValidationException('Format data item tidak valid.', ['items' => ['JSON tidak valid.']]);
            }

            $result = $this->lpjService->submitLpj((int)$validatedData['kegiatan_id'], $items);

            echo json_encode($result);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $e->getErrors()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function uploadBukti()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method tidak diizinkan', 405);
            }

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File tidak valid atau gagal diupload');
            }

            $lpjId = $_POST['lpj_id'] ?? null;
            $rabItemId = $_POST['rab_item_id'] ?? null;
            
            error_log("📤 Upload Request: lpjId=$lpjId, rabItemId=$rabItemId, filename={$_FILES['file']['name']}");
            
            if (empty($lpjId) || !is_numeric($lpjId)) {
                throw new Exception('LPJ ID tidak valid: ' . var_export($lpjId, true));
            }
            
            if (empty($rabItemId) || !is_numeric($rabItemId)) {
                throw new Exception('RAB Item ID tidak valid: ' . var_export($rabItemId, true));
            }

            $result = $this->lpjService->uploadLpjBukti(
                (int)$lpjId, 
                (int)$rabItemId, 
                $_FILES['file']
            );

            error_log("Upload Success: " . json_encode($result));

            if (ob_get_length()) ob_clean();
            echo json_encode($result);
            exit;

        } catch (Exception $e) {
            error_log("Upload Error: " . $e->getMessage());
            
            if (ob_get_length()) ob_clean();
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function saveDraft()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Metode tidak diizinkan', 405);
            }

            $kegiatanId = $_POST['kegiatan_id'] ?? null;
            $lpjId = $_POST['lpj_id'] ?? null;
            $itemsJson = $_POST['items'] ?? '[]';
            $items = json_decode($itemsJson, true);

            if (!$kegiatanId || !is_numeric($kegiatanId)) {
                throw new Exception('Kegiatan ID tidak valid');
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Format data item tidak valid.');
            }

            $this->db->begin_transaction();

            try {
                if (!empty($items)) {
                    $lpjModel = new \App\Models\Lpj\LpjModel($this->db);
                    
                    if ($lpjId && is_numeric($lpjId)) {
                        $lpjModel->updateLpjItemsRealisasi((int)$lpjId, $items);
                        $lpjModel->updateLpjGrandTotal((int)$lpjId);
                    }
                }

                $this->db->commit();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Draft berhasil disimpan'
                ]);
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal menyimpan draft: ' . $e->getMessage()
            ]);
        }
    }
}
