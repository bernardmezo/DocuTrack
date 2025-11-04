<?php
// File: src/controllers/AuthController.php

require_once '../src/core/Controller.php';
// require_once '../src/models/User.php'; // (Nanti Anda akan pakai ini)
include __DIR__ . '/../model/conn.php'; // Koneksi database

class AuthController extends Controller {

    private $conn;

    public function __construct() {
        include __DIR__ . '/../model/conn.php';
        $this->conn = $conn;
    }
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

    // --- LOGIN MULTI-ROLE ---

    public function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /docutrack/public/');
        exit;
    }

    session_start();
    $user_input = $_POST['captcha_input'] ?? '';
    $captcha_code = $_SESSION['captcha_code'] ?? '';

    if (empty($user_input) || strtolower($user_input) !== strtolower($captcha_code)) {
        $_SESSION['register_error'] = 'Kode CAPTCHA salah, silakan coba lagi.';
        header('Location: /docutrack/public/');
        exit;
    }



    // Ambil input dari form login
    $email = trim($_POST['login_email'] ?? '');
    $password = trim($_POST['login_password'] ?? '');

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password harus diisi.';
        header('Location: /docutrack/public/');
        exit;
    }

    


    // Cek apakah email terdaftar
    $query = "SELECT id, nama_lengkap, email, password, role_id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Cek password dengan password_verify()
        if ($password == $user['password']) {
            
            // Bersihkan error sebelumnya
            unset($_SESSION['login_error']);

            // Set session login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama_lengkap'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role_id'] = $user['role_id'];

            // Mapping role_id ke nama role
            $role_map = [
                1 => 'super-admin',
                2 => 'verifikator',
                3 => 'wadir',
                4 => 'ppk',
                5 => 'bendahara',
                6 => 'user'
            ];

            $role_name = $role_map[$user['role_id']] ?? 'user';
            $_SESSION['user_role'] = $role_name;

            // Redirect sesuai role
            switch ($role_name) {
                // case 'super-admin':
                //     header('Location: /docutrack/public/admin/dashboard');
                //     break;
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
                case 'user':
                    header('Location: /docutrack/public/admin/dashboard');
                    break;
                default:
                    header('Location: /docutrack/public/');
                    break;
            }

            exit; // Penting: hentikan eksekusi setelah redirect

        } else {
            // Password salah
            $_SESSION['login_error'] = 'Password salah.';
        }
    } else {
        // Email tidak ditemukan
        $_SESSION['login_error'] = 'Email tidak ditemukan.';
    }

    // Jika gagal login, kembali ke halaman utama
    header('Location: /docutrack/public/');
    exit;
}


        // -- REGITRASI USER BARU --

        public function handleRegister() {
        global $conn; // gunakan koneksi dari conn.php

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/');
            exit;
        }

        // Ambil data dari form
        $nama_lengkap = $_POST['register_nama_lengkap'] ?? null;
        $email = $_POST['register_email'] ?? null;
        $password = $_POST['register_password'] ?? null;
        $role_text = strtolower(trim($_POST['register_role'] ?? '')); // ubah jadi huruf kecil agar konsisten

        // Validasi input kosong
        if (empty($nama_lengkap) || empty($email) || empty($password) || empty($role_text)) {
            $_SESSION['login_error'] = 'Data registrasi tidak boleh kosong.';
            header('Location: /docutrack/public/1');
            exit;
        }

        // Mapping role ke ID
        $role_map = [
            'super-admin' => 1,
            'verifikator' => 2,
            'wadir' => 3,
            'ppk' => 4,
            'bendahara' => 5,
            'user' => 6
        ];

        // Default ke user jika role tidak dikenali
        $role_id = $role_map[$role_text] ?? 6;

        // Hash password
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Cek apakah email sudah ada
        $query_check = "SELECT email FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $query_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);

        if (mysqli_stmt_execute($stmt_check)) {
            mysqli_stmt_store_result($stmt_check);
            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $_SESSION['register_error'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
                header('Location: /docutrack/public/2');
                exit;
            }
        }

        // Tambah user ke database
        $query = "INSERT INTO users (nama_lengkap, email, password, role_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nama_lengkap, $email, $password_hashed, $role_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['register_success'] = 'Registrasi berhasil! Silakan login.';
            header('Location: /docutrack/public/login');
        } else {
            $_SESSION['register_error'] = 'Gagal registrasi. Silakan coba lagi.';
            header('Location: /docutrack/public/3');
        }
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