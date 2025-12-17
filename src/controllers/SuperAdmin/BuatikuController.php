<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Models\SuperAdminModel;
use App\Services\ValidationService;
use App\Exceptions\ValidationException;
use Exception;

class BuatikuController extends Controller
{
    private $model;

    public function __construct($db = null)
    {
        parent::__construct($db);
        // Direct Model Instantiation
        $this->model = new SuperAdminModel($this->db);
        

    }

    public function index($data_dari_router = [])
    {
        // Pagination logic (Manual implementation since Model returns all)
        $page = (int)($_GET['page'] ?? 1);
        $search_text = strtolower($_GET['search'] ?? '');
        $per_page = 10;

        // Direct Model Call
        $all_iku = $this->model->getAllIKU();

        // Filter in memory
        $filtered_data = array_filter($all_iku, function ($item) use ($search_text) {
            if (empty($search_text)) {
                return true;
            }
            return str_contains(strtolower($item['nama']), $search_text) || 
                   str_contains(strtolower($item['deskripsi'] ?? ''), $search_text);
        });

        $filtered_data = array_values($filtered_data);
        $total_items = count($filtered_data);
        $total_pages = ceil($total_items / $per_page);

        if ($page < 1) $page = 1;
        if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

        $offset = ($page - 1) * $per_page;
        $display_data = array_slice($filtered_data, $offset, $per_page);

        $data = array_merge($data_dari_router, [
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
        ]);

        // View path matches: src/views/pages/superadmin/buat-iku.php
        $this->view('pages/superadmin/buat-iku', $data, 'superadmin');
    }

    public function create()
    {
        $this->redirect('/docutrack/public/superadmin/buat-iku');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/buat-iku');
        }

        try {
            $rules = [
                'nama' => 'required',
                'deskripsi' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            // Direct Model Call
            if ($this->model->createIKU($validatedData['nama'], $validatedData['deskripsi'] ?? '')) {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil ditambahkan!');
            } else {
                throw new Exception('Gagal menambahkan IKU.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', 'Validasi gagal.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->redirect('/docutrack/public/superadmin/buat-iku');
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/buat-iku');
        }

        try {
            $rules = [
                'nama' => 'required',
                'deskripsi' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->model->updateIKU((int)$id, $validatedData['nama'], $validatedData['deskripsi'] ?? '')) {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil diupdate!');
            } else {
                throw new Exception('Gagal mengupdate IKU.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', 'Validasi gagal.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->deleteIKU((int)$id)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'success', 'IKU berhasil dihapus!');
            } else {
                $this->redirectWithMessage('/docutrack/public/superadmin/buat-iku', 'error', 'Gagal menghapus IKU.');
            }
        } else {
            $this->redirect('/docutrack/public/superadmin/buat-iku');
        }
    }
}