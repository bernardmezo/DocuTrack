<?php
// File: src/controllers/AuthController.php

// PENTING: Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/core/Controller.php';
// 1. Panggil Model Login
require_once '../src/model/LoginModel.php';
require_once '../src/helpers/logger_helper.php'; // ✅ LOAD LOGGER untuk audit trail

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
        $role_input = strtolower(trim($_POST['login_role'] ?? '')); 

        // 2. Validasi Input Kosong
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password harus diisi.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 3. Panggil Model & Cari User
        $loginModel = new LoginModel($this->db);
        $user = $loginModel->getUserByEmail($email);

        // 4. Cek Apakah User Ditemukan?
        if (!$user) {
            $_SESSION['login_error'] = 'Email tidak terdaftar.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 5. Verifikasi Password
        // SECURITY FIX: Hanya gunakan password_verify() - TIDAK ADA FALLBACK PLAIN TEXT
        // Ref: DATABASE_AUDIT.md - Pilar 2: Security Hardening
        
        if (!password_verify($password, $user['password'])) {
            // ✅ AUDIT LOG: Catat login gagal
            logLogin(0, $email, false, 'Password salah');
            
            $_SESSION['login_error'] = 'Password salah.';
            header('Location: /docutrack/public/');
            exit;
        }

        // 6. Normalize & Validasi Role
        // Normalize role dari database: 'Super Admin' -> 'super-admin', 'Verifikator' -> 'verifikator'
        $normalized_role = strtolower($user['namaRole']);
        $normalized_role = str_replace([' ', '_'], '-', $normalized_role);
        $normalized_role = preg_replace('/--+/', '-', $normalized_role);

        // Jika user memilih role di dropdown, kita cek kecocokannya (juga normalize input).
        if (!empty($role_input)) {
            $normalized_input = strtolower($role_input);
            $normalized_input = str_replace([' ', '_'], '-', $normalized_input);
            
            if ($normalized_input !== $normalized_role) {
                $_SESSION['login_error'] = "Akun ini tidak terdaftar sebagai " . ucfirst($role_input);
                header('Location: /docutrack/public/');
                exit;
            }
        }

        // ============================
        //  LOGIN BERHASIL
        // ============================

        // SECURITY FIX: Regenerate session ID untuk mencegah Session Fixation Attack
        // Ref: DATABASE_AUDIT.md - Pilar 2: Security Hardening
        session_regenerate_id(true);
        
        unset($_SESSION['login_error']);

        // --- MERGED SESSION LOGIC ---
        // 1. Simpan data ke Session (Logic dari branch integrate-db untuk database)
        $_SESSION['user_id']       = $user['userId'];      // Dari query: u.userId
        $_SESSION['user_name']     = $user['nama'];        // Dari query: u.nama
        $_SESSION['user_role']     = $normalized_role;     // Normalized: 'verifikator', 'super-admin', dll
        $_SESSION['user_jurusan']  = $user['namaJurusan'] ?? null; // Dari query: j.namaJurusan

        // 2. Simpan data ke Session (Logic dari branch HEAD untuk frontend/design)
        // Menggabungkan data database ke dalam format yang diharapkan oleh view/design lama
        $_SESSION['user_data'] = [
            'id'            => $user['userId'],
            'username'      => $user['nama'],
            'email'         => $email,
            'role'          => $normalized_role,
            // Generate gambar profil seperti di branch HEAD agar desain tidak rusak
            'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=0D8ABC&color=fff&size=150',
            'header_bg'     => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%)',
            'created_at'    => date('Y-m-d')
        ];

        // ✅ AUDIT LOG: Catat login berhasil
        logLogin($user['userId'], $email, true);

        // ============================
        //  REDIRECT BERDASARKAN ROLE
        // ============================

        switch ($normalized_role) {
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
            case 'superadmin': // Fallback jika di DB ditulis tanpa spasi/dash
                header('Location: /docutrack/public/super_admin/dashboard');
                break;

            default:
                // Jika role tidak dikenali, log untuk debugging dan redirect ke landing
                // JANGAN session_destroy() karena akan menyebabkan loop
                error_log("DocuTrack Login Warning: Unknown role '{$normalized_role}' for user {$user['email']}");
                $_SESSION['login_error'] = 'Role pengguna tidak dikenali: ' . htmlspecialchars($user['namaRole']);
                header('Location: /docutrack/public/');
                break;
        }

        exit;
    }

    public function logout() {
        // ✅ AUDIT LOG: Catat logout sebelum session dihapus
        $userId = $_SESSION['user_id'] ?? 0;
        if ($userId > 0) {
            logLogout($userId);
        }
        
        session_destroy();
        header('Location: /docutrack/public/'); 
        exit;
    }
}
