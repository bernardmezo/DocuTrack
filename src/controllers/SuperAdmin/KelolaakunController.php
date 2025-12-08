<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Services\SuperAdminService;
use App\Services\ValidationService;
use App\Exceptions\ValidationException;
use Exception;

class KelolaakunController extends Controller
{
    private $superAdminService;
    private $validationService;

    public function __construct()
    {
        parent::__construct();
        $this->superAdminService = new SuperAdminService($this->db);
        $this->validationService = new ValidationService();
    }

    public function index($data_dari_router = [])
    {
        $data = array_merge($data_dari_router, [
            'title' => 'Kelola Akun Pengguna',
            'list_users' => $this->superAdminService->getAllUsers(),
            'list_roles' => $this->superAdminService->getAllRoles(),
            'list_jurusan' => $this->superAdminService->getListJurusan()
        ]);
        $this->view('pages/superadmin/kelola-akun', $data, 'superadmin');
    }

    public function show($id)
    {
        $user = $this->superAdminService->getUserById((int)$id);

        if (!$user) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail User - ' . htmlspecialchars($user['nama']),
            'user' => $user,
            'list_roles' => $this->superAdminService->getAllRoles(),
            'list_jurusan' => $this->superAdminService->getListJurusan()
        ];

        $this->view('pages/superadmin/kelola-akun-detail', $data, 'superadmin');
    }

    /**
     * Process user update. Renamed from edit().
     */
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
                'password' => 'nosanitize', // Optional, but validate if present
                'namaJurusan' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->superAdminService->updateUser((int)$id, $validatedData)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil diperbarui!');
            } else {
                throw new Exception('Gagal memperbarui user di database.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun/show/' . $id, 'error', 'Data tidak valid.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun/show/' . $id, 'error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($this->superAdminService->deleteUser((int)$id)) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil dihapus!');
        } else {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'error', 'Gagal menghapus user.');
        }
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Akun Baru',
            'list_roles' => $this->superAdminService->getAllRoles(),
            'list_jurusan' => $this->superAdminService->getListJurusan()
        ];
        $this->view('pages/superadmin/kelola-akun-create', $data, 'superadmin');
    }

    /**
     * Store the new user data from the form.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/superadmin/kelola-akun');
        }

        try {
            $rules = [
                'nama' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|nosanitize',
                'roleId' => 'required|numeric',
                'namaJurusan' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            if ($this->superAdminService->createUser($validatedData)) {
                $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun', 'success', 'User berhasil dibuat!');
            } else {
                throw new Exception('Gagal membuat user di database.');
            }
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun/create', 'error', 'Data tidak valid.');
        } catch (Exception $e) {
            $this->redirectWithMessage('/docutrack/public/superadmin/kelola-akun/create', 'error', $e->getMessage());
        }
    }
}
