<?php
// File: src/controllers/Bendahara/AkunController.php

require_once '../src/core/Controller.php';
require_once '../src/model/bendaharaModel.php';

class BendaharaAkunController extends Controller
{
    private $model;
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_data'])) {
            header('Location: /docutrack/public/');
            exit;
        }
        
        $this->model = new bendaharaModel($this->db);
    }

    public function index($options = [])
    {
        // ✅ AMBIL DATA USER DARI DATABASE
        $userId = $_SESSION['user_id'] ?? $_SESSION['user_data']['userId'] ?? null;
        
        if ($userId) {
            $user = $this->model->getUserById($userId);
            // Merge dengan session data untuk profile_image dan header_bg
            $user['profile_image'] = $_SESSION['user_data']['profile_image'] ?? '/docutrack/public/assets/images/default-avatar.png';
            $user['header_bg'] = $_SESSION['user_data']['header_bg'] ?? "url('/docutrack/public/assets/images/default-header.jpg')";
        } else {
            $user = $_SESSION['user_data'];
        }
        
        $data = array_merge($options, [
            'title' => 'Pengaturan Akun',
            'user' => $user
        ]);
        
        $this->view('pages/bendahara/akun', $data, 'bendahara');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/akun');
            exit;
        }
        
        $userId = $_SESSION['user_id'] ?? $_SESSION['user_data']['userId'] ?? null;
        
        if (!$userId) {
            $_SESSION['flash_error'] = 'User ID tidak ditemukan.';
            header('Location: /docutrack/public/bendahara/akun');
            exit;
        }

        // Folder Upload
        $uploadDir = __DIR__ . '/../../../../public/assets/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // ✅ DATA UNTUK UPDATE KE DATABASE
        $updateData = [];
        
        if (!empty($_POST['username'])) {
            $updateData['nama'] = trim($_POST['username']);
        }
        
        if (!empty($_POST['email'])) {
            $updateData['email'] = trim($_POST['email']);
        }
        
        if (!empty($_POST['password'])) {
            $updateData['password'] = $_POST['password'];
        }

        // ✅ SIMPAN KE DATABASE
        if (!empty($updateData)) {
            $success = $this->model->updateUserProfile($userId, $updateData);
            
            if ($success) {
                // Update session dengan data baru
                if (!empty($updateData['nama'])) {
                    $_SESSION['user_data']['nama'] = $updateData['nama'];
                    $_SESSION['user_data']['username'] = $updateData['nama'];
                }
                if (!empty($updateData['email'])) {
                    $_SESSION['user_data']['email'] = $updateData['email'];
                }
                if (!empty($updateData['password'])) {
                    $_SESSION['user_data']['password'] = $updateData['password'];
                }
            } else {
                $_SESSION['flash_error'] = 'Gagal menyimpan data ke database.';
                header('Location: /docutrack/public/bendahara/akun');
                exit;
            }
        }

        // --- UPLOAD FOTO PROFIL ---
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $fileName = time() . '_p_' . basename($_FILES['profile_image']['name']);
            $targetPath = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            
            if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    $_SESSION['user_data']['profile_image'] = '/docutrack/public/assets/uploads/profiles/' . $fileName;
                }
            }
        }

        // --- UPLOAD BACKGROUND HEADER ---
        if (isset($_FILES['header_bg']) && $_FILES['header_bg']['error'] === 0) {
            $bgName = time() . '_bg_' . basename($_FILES['header_bg']['name']);
            $bgPath = $uploadDir . $bgName;
            $bgType = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
            
            if (in_array($bgType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['header_bg']['tmp_name'], $bgPath)) {
                    $publicPath = '/docutrack/public/assets/uploads/profiles/' . $bgName;
                    $_SESSION['user_data']['header_bg'] = "url('$publicPath')"; 
                }
            }
        }

        $_SESSION['flash_message'] = 'Profil berhasil diperbarui!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /docutrack/public/bendahara/akun?success=1');
        exit;
    }
}