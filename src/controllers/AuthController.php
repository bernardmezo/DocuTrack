<?php
// File: src/controllers/AuthController.php

require_once '../src/core/Controller.php';

class AuthController extends Controller {

    public function __construct() {
        // Tidak butuh database sama sekali
    }

    /**
     * Menampilkan halaman login.
     * Jika diakses langsung, redirect ke landing page.
     */
    public function index() {
        header('Location: /docutrack/public/'); 
        exit;
    }


    // =====================================================
    // ===============  LOGIN MULTI-ROLE  ==================
    // =====================================================

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/');
            exit;
        }

        // Ambil input dari form login
        $email      = trim($_POST['login_email'] ?? '');
        $password   = trim($_POST['login_password'] ?? '');
        $role_text  = strtolower(trim($_POST['login_role'] ?? ''));

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password harus diisi.';
            header('Location: /docutrack/public/');
            exit;
        }

        // ============================
        //  DATABASE DUMMY TANPA DB
        // ============================

        $users_db = [
            'admin@example.com' => [
                'id' => 1,
                'password' => 'password123',
                'nama' => 'Admin Docutrack',
                'role' => 'admin'
            ],
            'verifikator@example.com' => [
                'id' => 2,
                'password' => 'password123',
                'nama' => 'Putra Yopan (Verifikator)',
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
            ],
            'bendahara@example.com' => [
                'id' => 5,
                'password' => 'password123',
                'nama' => 'Bendahara',
                'role' => 'bendahara'
            ],
            'superadmin@example.com' => [
                'id' => 6,
                'password' => 'password123',
                'nama' => 'Super Admin',
                'role' => 'super-admin'
            ]
        ];


        // ============================
        //  LOGIKA LOGIN DUMMY
        // ============================

        if (!isset($users_db[$email])) {
            $_SESSION['login_error'] = 'Email tidak ditemukan.';
            header('Location: /docutrack/public/');
            exit;
        }

        $user = $users_db[$email];

        if ($password !== $user['password']) {
            $_SESSION['login_error'] = 'Password salah.';
            header('Location: /docutrack/public/');
            exit;
        }

        // ============================
        //  LOGIN BERHASIL
        // ============================

        unset($_SESSION['login_error']);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];

        // ============================
        //  REDIRECT BERDASARKAN ROLE
        // ============================

        switch ($role_text) {
            case 'verifikator':
                header('Location: /docutrack/public/verifikator/dashboard');
                break;

            case 'wadir':
                header('Location: /docutrack/public/wadir/dashboard');
                break;

            case 'ppk':
                header('Location: /docutrack/public/ppk/dashboard');
                break;

            case 'bendahara':
                header('Location: /docutrack/public/bendahara/dashboard');
                break;

            case 'admin':
                header('Location: /docutrack/public/admin/dashboard');
                break;
            
            case 'super-admin':
                header('Location: /docutrack/public/super_admin/dashboard');
                break;

            default:
                header('Location: /docutrack/public/');
                break;
        }

        exit;
    }


    // =====================================================
    // ==================== LOGOUT ==========================
    // =====================================================

    public function logout() {
        session_destroy();
        header('Location: /docutrack/public/'); 
        exit;
    }
}
