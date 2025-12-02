<?php
// File: src/controllers/Direktur/AkunController.php

class DirekturAkunController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_data'])) {
            // Jika belum ada user_data, redirect ke login
            header('Location: /docutrack/public/');
            exit;
        }
    }

    public function index($options = [])
    {
        $user = $_SESSION['user_data'];
        
        // Set active page dari options
        $active_page = $options['active_page'] ?? '/direktur/akun';
        
        include __DIR__ . '/../../views/layouts/direktur/header.php';
        include __DIR__ . '/../../views/pages/direktur/akun.php';
        include __DIR__ . '/../../views/layouts/direktur/footer.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Update Data Teks (Username & Email)
            $_SESSION['user_data']['username'] = $_POST['username'] ?? $_SESSION['user_data']['username'];
            $_SESSION['user_data']['email']    = $_POST['email'] ?? $_SESSION['user_data']['email'];
            
            // Update Password (jika diisi dan valid)
            if (!empty($_POST['password'])) {
                $newPassword = $_POST['password'];
                $confirmPassword = $_POST['password_confirm'] ?? '';
                
                // Validasi password
                if ($newPassword === $confirmPassword) {
                    if (strlen($newPassword) >= 8) {
                        // Dalam production, gunakan password_hash()
                        $_SESSION['user_data']['password'] = $newPassword;
                        // $_SESSION['user_data']['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    } else {
                        // Password terlalu pendek
                        header('Location: /docutrack/public/direktur/akun?error=password_short');
                        exit;
                    }
                } else {
                    // Password tidak cocok
                    header('Location: /docutrack/public/direktur/akun?error=password_mismatch');
                    exit;
                }
            }
            
            // Folder Upload
            $uploadDir = __DIR__ . '/../../../public/assets/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // --- 1. LOGIC UPLOAD FOTO PROFIL ---
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $fileName = time() . '_p_' . basename($_FILES['profile_image']['name']);
                $targetPath = $uploadDir . $fileName;
                $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                
                // Validasi ekstensi file
                if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Validasi ukuran file (max 2MB)
                    if ($_FILES['profile_image']['size'] <= 2097152) {
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                            // Hapus foto lama jika ada (opsional)
                            if (isset($_SESSION['user_data']['profile_image']) && 
                                strpos($_SESSION['user_data']['profile_image'], 'ui-avatars.com') === false) {
                                $oldFile = __DIR__ . '/../../../public' . parse_url($_SESSION['user_data']['profile_image'], PHP_URL_PATH);
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                            }
                            
                            $_SESSION['user_data']['profile_image'] = '/docutrack/public/assets/uploads/profiles/' . $fileName;
                        }
                    }
                }
            }
            
            // --- 2. LOGIC UPLOAD BACKGROUND HEADER ---
            if (isset($_FILES['header_bg']) && $_FILES['header_bg']['error'] === 0) {
                $bgName = time() . '_bg_' . basename($_FILES['header_bg']['name']);
                $bgPath = $uploadDir . $bgName;
                $bgType = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                
                if (in_array($bgType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if ($_FILES['header_bg']['size'] <= 2097152) { // Max 2MB
                        if (move_uploaded_file($_FILES['header_bg']['tmp_name'], $bgPath)) {
                            // Hapus background lama jika ada (opsional)
                            if (isset($_SESSION['user_data']['header_bg']) && 
                                strpos($_SESSION['user_data']['header_bg'], 'url(') !== false) {
                                preg_match("/url\('(.+?)'\)/", $_SESSION['user_data']['header_bg'], $matches);
                                if (isset($matches[1])) {
                                    $oldBgFile = __DIR__ . '/../../../public' . parse_url($matches[1], PHP_URL_PATH);
                                    if (file_exists($oldBgFile)) {
                                        unlink($oldBgFile);
                                    }
                                }
                            }
                            
                            // Simpan sebagai format URL() untuk CSS
                            $publicPath = '/docutrack/public/assets/uploads/profiles/' . $bgName;
                            $_SESSION['user_data']['header_bg'] = "url('$publicPath')"; 
                        }
                    }
                }
            }
            
            // Redirect dengan pesan sukses
            header('Location: /docutrack/public/direktur/akun?success=1');
            exit;
        }
        
        // Jika bukan POST, redirect ke halaman akun
        header('Location: /docutrack/public/direktur/akun');
        exit;
    }
}