<?php
// File: src/controllers/AuthController.php

require_once '../src/core/Controller.php';
// 1. Panggil Model Login
require_once '../src/model/LoginModel.php';

class AuthController extends Controller {

    public function __construct() {
        // Constructor kosong
    }

    public function index() {
        header('Location: /docutrack/public/'); 
        exit;
    }

    // =====================================================
    // ===============  LOGIN REAL DATABASE  ===============
    // =====================================================

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/');
            exit;
        }
        
        // 1. Ambil input dari form
        $email      = trim($_POST['login_email'] ?? '');
        $password   = trim($_POST['login_password'] ?? '');
        // Role yang dipilih user di dropdown (opsional, kita validasi nanti)
        $role_input = strtolower(trim($_POST['login_role'] ?? '')); // bakal ga dipake pas middleware di apus

        // 2. Validasi Input Kosong
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password harus diisi.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 3. Panggil Model & Cari User
        $loginModel = new LoginModel();
        $user = $loginModel->getUserByEmail($email);

        // 4. Cek Apakah User Ditemukan?
        if (!$user) {
            $_SESSION['login_error'] = 'Email tidak terdaftar.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 5. Verifikasi Password
        // PENTING: Di database asli, password HARUS di-hash pakai password_hash()
        // Kita gunakan password_verify() untuk mencocokkan input dengan hash di DB.
        
        $password_valid = false;

        // Cek A: Jika database menggunakan Hash (Recommended)
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
        } 
        // Cek B: (FALLBACK) Jika database masih pakai text polos (HANYA UNTUK DEVELOPMENT)
        // Hapus bagian 'elseif' ini jika nanti password di DB sudah di-hash semua.
        elseif ($password === $user['password']) {
            $password_valid = true;
        }

        if (!$password_valid) {
            $_SESSION['login_error'] = 'Password salah.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 6. Validasi Role (PENTING!)
        // Pastikan role yang dipilih di form SESUAI dengan role asli user di database.
        // Nama kolom dari Model tadi adalah 'namaRole'
        $db_role = strtolower($user['namaRole']); // misal: 'verifikator'

        // Jika user memilih role di dropdown, kita cek kecocokannya.
        // Jika role_input kosong (misal user lupa pilih), kita bisa otomatis pakai role dari DB.
        if (!empty($role_input) && $role_input !== $db_role) {
            $_SESSION['login_error'] = "Akun ini tidak terdaftar sebagai " . ucfirst($role_input);
            header('Location: /docutrack/public/');
            exit;
        }

        // ============================
        //  LOGIN BERHASIL
        // ============================

        unset($_SESSION['login_error']);

        // Simpan data ke Session (Sesuaikan dengan key dari LoginModel)
        $_SESSION['user_id']       = $user['userId'];      // Dari query: u.userId
        $_SESSION['user_name']     = $user['nama'];        // Dari query: u.nama
        $_SESSION['user_role']     = $db_role;             // Dari query: r.nama_role
        $_SESSION['user_jurusan']  = $user['nama_jurusan'];// Dari query: j.nama_jurusan (Opsional)

        // ============================
        //  REDIRECT BERDASARKAN ROLE ASLI
        // ============================

        switch ($db_role) {
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
                // Jika role tidak dikenali, lempar error atau ke halaman umum
                $_SESSION['login_error'] = 'Role pengguna tidak valid.';
                session_destroy(); // Hapus sesi agar aman
                header('Location: /docutrack/public/');
                break;
        }

        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: /docutrack/public/'); 
        exit;
    }
}