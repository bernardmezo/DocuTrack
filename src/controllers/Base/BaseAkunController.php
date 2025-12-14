<?php

declare(strict_types=1);

namespace App\Controllers\Base;

use App\Core\Controller;
use App\Services\AuthService;
// Removed: use App\Services\FileUploadService;
// Removed: use App\Services\ValidationService;
use App\Exceptions\ValidationException;
use Exception;

abstract class BaseAkunController extends Controller
{
    private $authService;
    // fileUploadService and validationService are now inherited from base Controller

    // Abstract methods to be implemented by child controllers
    abstract protected function getAkunViewPath(): string;
    abstract protected function getAkunRedirectUrl(): string;
    abstract protected function getAkunLayout(): string;

    public function __construct($db)
    {
        parent::__construct($db);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/docutrack/public/');
        }
        $this->authService = new AuthService($this->db);
        // $this->fileUploadService and $this->validationService are already set in parent::__construct()
    }

    public function index($options = [])
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId === null) {
            error_log("BaseAkunController: user_id missing from session. Redirecting to login.");
            session_destroy(); // Ensure clean state
            $this->redirect('/docutrack/public/');
        }

        $dbUser = $this->authService->getUserProfile($userId);

        if (!$dbUser) {
            error_log("BaseAkunController: User profile not found for ID {$userId}. Redirecting to login.");
            session_destroy();
            $this->redirect('/docutrack/public/');
        }

        $viewUser = [
            'username' => $dbUser['nama'] ?? 'User',
            'email' => $dbUser['email'] ?? '',
            'role' => $dbUser['namaRole'] ?? 'N/A',
            'created_at' => $dbUser['createdAt'] ?? date('Y-m-d'),
            'profile_image' => $_SESSION['user_data']['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($dbUser['nama'] ?? 'U') . '&background=0D8ABC&color=fff&size=150',
            'header_bg' => $_SESSION['user_data']['header_bg'] ?? 'linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%)',
        ];

        $data = array_merge($options, [
            'title' => 'Pengaturan Akun',
            'user' => $viewUser
        ]);

        $this->view($this->getAkunViewPath(), $data, $this->getAkunLayout());
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->getAkunRedirectUrl());
        }

        $userId = $_SESSION['user_id'];

        try {
            $rules = [
                'username' => 'required',
                'email' => 'required|email',
                'password' => 'nosanitize', // Optional, but don't sanitize if present
                'password_confirm' => 'nosanitize'
            ];
            $validatedData = $this->validationService->validate($_POST, $rules);

            // Update user profile with validated data
            $updateData = [
                'nama' => $validatedData['username'],
                'email' => $validatedData['email']
            ];
            $this->authService->updateUserProfile($userId, $updateData);

            // Handle password change if a new password was provided
            if (!empty($validatedData['password'])) {
                if ($validatedData['password'] !== $validatedData['password_confirm']) {
                    throw new Exception("Konfirmasi password tidak cocok.");
                }
                $this->authService->changePassword($userId, $validatedData['password']);
            }

            // Handle file uploads
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $publicPath = $this->fileUploadService->uploadProfileImage($_FILES['profile_image']);
                $_SESSION['user_data']['profile_image'] = $publicPath;
            }

            if (isset($_FILES['header_bg']) && $_FILES['header_bg']['error'] === UPLOAD_ERR_OK) {
                $cssUrl = $this->fileUploadService->uploadHeaderBackground($_FILES['header_bg']);
                $_SESSION['user_data']['header_bg'] = $cssUrl;
            }

            // Refresh session data with updated info
            $updatedUser = $this->authService->getUserProfile($userId);
            $_SESSION['user_data']['username'] = $updatedUser['nama'];
            $_SESSION['user_data']['email'] = $updatedUser['email'];

            $this->redirectWithMessage($this->getAkunRedirectUrl(), 'success', 'Profil berhasil diperbarui!');
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirectWithMessage($this->getAkunRedirectUrl(), 'error', 'Validasi gagal, periksa kembali data Anda.');
        } catch (Exception $e) {
            $this->redirectWithMessage($this->getAkunRedirectUrl(), 'error', $e->getMessage());
        }
    }
}
