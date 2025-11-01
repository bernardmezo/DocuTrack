<?php
// File: src/controllers/AuthController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/User.php'; // (Nanti Anda akan pakai ini)

class AuthController extends Controller {

    /**
     * Menampilkan halaman login (saat ini di-handle popup).
     * Jika diakses langsung, redirect ke landing page.
     */
    public function index() {
        header('Location: /docutrack/public/'); 
        exit;
    }

    /**
     * Memproses data login dari form popup
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/'); 
            exit;
        }

        $email = $_POST['login_email'] ?? null;
        $password = $_POST['login_password'] ?? null;

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password harus diisi.';
            header('Location: /docutrack/public/');
            exit;
        }

        // --- SIMULASI DATABASE USER (Ganti dengan Model Anda nanti) ---
        
        // $userModel = new User();
        // $user = $userModel->findByEmail($email);
        
        // Data Dummy untuk Multi-Role
        $users_db = [
            'admin@example.com' => [
                'id' => 1,
                'password' => 'password123', // Di database asli, ini harus di-hash
                'nama' => 'Admin Docutrack',
                'role' => 'admin'
            ],
            'verifikator@example.com' => [
                'id' => 2,
                'password' => 'password123',
                'nama' => 'Putra Yopan (Verifikator)', // Sesuai screenshot
                'role' => 'verifikator'
            ],
            'wadir@example.com' => [
                'id' => 3,
                'password' => 'password123',
                'nama' => 'Wakil Direktur',
                'role' => 'wadir'
            ],
            'ppk@example.com' => [
                'id' => 4,
                'password' => 'password123',
                'nama' => 'Pejabat PPK',
                'role' => 'ppk'
            ]
        ];

        // --- Logika Login Multi-Role ---

        // 1. Cek apakah email ada di "database"
        if (isset($users_db[$email])) {
            $user = $users_db[$email];

            // 2. Cek password (Di aplikasi nyata, gunakan password_verify())
            if ($password === $user['password']) {
                
                // --- LOGIN BERHASIL ---
                unset($_SESSION['login_error']);

                // 3. Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['user_role'] = $user['role']; // <-- INI YANG PENTING

                // 4. Redirect berdasarkan Role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /docutrack/public/admin/dashboard');
                        break;
                    case 'verifikator':
                        header('Location: /docutrack/public/verifikator/dashboard');
                        break;
                    case 'wadir':
                        header('Location: /docutrack/public/wadir/dashboard');
                        break;
                    case 'ppk':
                        header('Location: /docutrack/public/ppk/dashboard');
                        break;
                    // ... (Tambahkan case untuk ppk, bendahara, dll.) ...
                    default:
                        // Jika role tidak dikenal, lempar ke landing page
                        header('Location: /docutrack/public/');
                        break;
                }
                exit; // Hentikan script setelah redirect

            }
        }

        // --- LOGIN GAGAL ---
        // Jika email tidak ditemukan atau password salah
        $_SESSION['login_error'] = 'Email atau password salah.';
        header('Location: /docutrack/public/'); // Kembali ke landing page
        exit;
    }

    /**
     * Menghandle logout
     */
    public function logout() {
        session_destroy();
        header('Location: /docutrack/public/'); 
        exit;
    }
}