<?php

class AdminAkunController
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
        
        include __DIR__ . '/../../views/layouts/app/header.php';
        include __DIR__ . '/../../views/pages/admin/akun.php';
        include __DIR__ . '/../../views/layouts/app/footer.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Update Data Teks
            $_SESSION['user_data']['username'] = $_POST['username'] ?? $_SESSION['user_data']['username'];
            $_SESSION['user_data']['email']    = $_POST['email'] ?? $_SESSION['user_data']['email'];
            
            if (!empty($_POST['password'])) {
                $_SESSION['user_data']['password'] = $_POST['password'];
            }

            // Folder Upload
            $uploadDir = __DIR__ . '/../../../../public/assets/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // --- 1. LOGIC UPLOAD FOTO PROFIL ---
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

            // --- 2. LOGIC UPLOAD BACKGROUND HEADER (BARU) ---
            if (isset($_FILES['header_bg']) && $_FILES['header_bg']['error'] === 0) {
                $bgName = time() . '_bg_' . basename($_FILES['header_bg']['name']);
                $bgPath = $uploadDir . $bgName;
                $bgType = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                
                if (in_array($bgType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if (move_uploaded_file($_FILES['header_bg']['tmp_name'], $bgPath)) {
                        // Simpan sebagai format URL() untuk CSS
                        $publicPath = '/docutrack/public/assets/uploads/profiles/' . $bgName;
                        $_SESSION['user_data']['header_bg'] = "url('$publicPath')"; 
                    }
                }
            }

            header('Location: /docutrack/public/admin/akun?success=1');
            exit;
        }

        header('Location: /docutrack/public/admin/akun');
        exit;
    }
}