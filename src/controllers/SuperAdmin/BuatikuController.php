<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Services\SuperAdminService; // Use Service instead of Model
use App\Services\ValidationService; // Use Validation Service
use App\Exceptions\ValidationException;
use Exception;

class BuatikuController extends Controller
{
    private $superAdminService; // Changed from $model to $superAdminService
    private $validationService;

    public function __construct()
    {
        parent::__construct();
        $this->superAdminService = new SuperAdminService($this->db);
        $this->validationService = new ValidationService();
    }

    public function index()
    {
        $page = (int)($_GET['page'] ?? 1);
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 5;

        // Fetch data via Service
        $all_iku = $this->superAdminService->getAllIKU();

        $filtered_data = array_filter($all_iku, function ($item) use ($search_text) {
            if (empty($search_text)) {
                return true;
            }
            return str_contains(strtolower($item['nama']), $search_text);
        });

        $filtered_data = array_values($filtered_data);
        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $per_page;
        $display_data = array_slice($filtered_data, $offset, $per_page);

        $data = [
            'title' => 'Kelola IKU',
            'list_iku' => $display_data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ($total_pages > 0) ? $total_pages : 1,
                'total_items' => $total_items,
                'showing_from' => ($total_items > 0) ? $offset + 1 : 0,
                'showing_to' => ($total_items > 0) ? min($offset + count($display_data), $total_items) : 0,
                'per_page' => $per_page
            ],
            'filters' => [
                'search' => $_GET['search'] ?? ''
            ]
        ];

        $this->view('pages/super_admin/buat-iku', $data, 'super_admin');
    }

    /**
     * Show form to create new IKU
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah IKU Baru'
        ];
        $this->view('pages/super_admin/iku-create', $data, 'super_admin');
    }

    /**
     * Store new IKU (POST request)
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/buat-iku/create');
        }

        try {
            $rules = [
                'nama' => 'required',
                'deskripsi' => 'nosanitize' // Description is optional, or can be empty
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->superAdminService->createIKU($validatedData['nama'], $validatedData['deskripsi'])) {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil ditambahkan!');
            } else {
                throw new Exception('Gagal menambahkan IKU.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku/create', 'error', 'Validasi gagal, periksa kembali input Anda.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku/create', 'error', $e->getMessage());
        }
    }

    /**
     * Show form to edit existing IKU
     */
    public function edit($id)
    {
        $iku = $this->superAdminService->getIKUById((int)$id);
        if (!$iku) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', 'IKU tidak ditemukan.');
        }
        $data = [
            'title' => 'Edit IKU',
            'iku' => $iku
        ];
        $this->view('pages/super_admin/iku-edit', $data, 'super_admin');
    }

    /**
     * Update existing IKU (POST request)
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/buat-iku/edit/' . $id);
        }

        try {
            $rules = [
                'nama' => 'required',
                'deskripsi' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->superAdminService->updateIKU((int)$id, $validatedData['nama'], $validatedData['deskripsi'])) {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil diupdate!');
            } else {
                throw new Exception('Gagal mengupdate IKU.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku/edit/' . $id, 'error', 'Validasi gagal, periksa kembali input Anda.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku/edit/' . $id, 'error', $e->getMessage());
        }
    }

    /**
     * Delete IKU
     */
    public function delete($id)
    {
        if ($this->superAdminService->deleteIKU((int)$id)) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil dihapus!');
        } else {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', 'Gagal menghapus IKU.');
        }
    }
}
