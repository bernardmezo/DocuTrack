<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Models\SuperAdminModel;
use App\Services\ValidationService;
use App\Exceptions\ValidationException;
use Exception;

class KelolaakunController extends Controller
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
        // Direct Model Calls
        $data = array_merge($data_dari_router, [
            'title' => 'Kelola Akun Pengguna',
            'list_users' => $this->model->getAllUsers(),
            'list_roles' => $this->model->getAllRoles(),
            'list_jurusan' => $this->model->getListJurusan()
        ]);
        
        // View path matches: src/views/pages/superadmin/kelola-akun.php
        $this->view('pages/superadmin/kelola-akun', $data, 'superadmin');
    }

    public function create()
    {
        // Redirect because we use Modals in the index view
        $this->redirect('/docutrack/public/superadmin/kelola-akun');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/kelola-akun');
        }

        try {
            $rules = [
                'nama' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8', // Raw password validation
                'roleId' => 'required|numeric',
                'namaJurusan' => 'nosanitize'
            ];
            
            $validatedData = $this->validationService->validate($_POST, $rules);

            // CRITICAL: Hash password here since we removed the Service layer
            $validatedData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if (isset($_POST['status'])) {
                $validatedData['status'] = $_POST['status'];
            }

            if ($this->model->createUser($validatedData)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil dibuat!');
            } else {
                throw new Exception('Gagal membuat user di database.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'Validasi gagal: ' . implode(', ', $e->getErrors()));
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = $this->model->getUserById((int)$id);
        if (!$user) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'User tidak ditemukan.');
        }
        // Redirect to index with a trigger to open modal (simulated by just going to index for now)
        $this->redirect('/docutrack/public/superadmin/kelola-akun');
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/kelola-akun');
        }

        try {
            $rules = [
                'nama' => 'required',
                'email' => 'required|email',
                'roleId' => 'required|numeric',
                'namaJurusan' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            // Handle Password Update logic
            if (!empty($_POST['password'])) {
                $validatedData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                // Ensure we don't overwrite with empty string if not provided
                unset($validatedData['password']); 
            }

            if (isset($_POST['status'])) {
                $validatedData['status'] = $_POST['status'];
            }

            if ($this->model->updateUser((int)$id, $validatedData)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil diperbarui!');
            } else {
                throw new Exception('Gagal memperbarui user.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'Validasi gagal.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->deleteUser((int)$id)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil dihapus!');
            } else {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'Gagal menghapus user.');
            }
        } else {
            $this->redirect('/docutrack/public/superadmin/kelola-akun');
        }
    }
}
