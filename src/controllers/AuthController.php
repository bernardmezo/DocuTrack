<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
// Removed: use App\Services\ValidationService;
use App\Exceptions\ValidationException;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/logger_helper.php';
}

class AuthController extends Controller
{
    private $authService;
    // validationService is now inherited from base Controller

    public function __construct($db)
    {
        parent::__construct($db);
        $this->authService = new AuthService($this->db);
        // $this->validationService is already set in parent::__construct()
    }

    public function index()
    {
        $this->redirect('/docutrack/public/');
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docutrack/public/');
        }

        try {
            // Validate CAPTCHA first
            $this->validateCaptcha($_POST['captcha_code'] ?? '');
            
            $rules = [
                'login_email'    => 'required|email',
                // 'nosanitize' is used for password to prevent htmlspecialchars
                // from altering the raw password before it's processed by the AuthService.
                'login_password' => 'required|min:8|nosanitize',
                'login_role'     => 'nosanitize', // Role is also not html content
                'captcha_code'   => 'required|nosanitize',
            ];
            $data = $this->validationService->validate($_POST, $rules);

            // From this point on, ONLY use the $data array, not $_POST.
            $email = $data['login_email'];
            $password = $data['login_password'];
            $role_input = $data['login_role'] ?? '';

            error_log("[AuthDebug] Login attempt for: " . $email);

            $result = $this->authService->login($email, $password);

            if (!$result['success']) {
                error_log("[AuthDebug] Login failed for {$email}: " . $result['message']);
                if (function_exists('logLogin')) {
                    logLogin(0, $email, false, $result['message']);
                }
                // Use a ValidationException with a generic field for login-specific errors
                throw new ValidationException("Email atau password salah.", ['login' => [$result['message']]]);
            }

            $user = $result['user'];
            error_log("[AuthDebug] User found: ID={$user['userId']}, Role={$user['namaRole']}");

            $normalized_role = strtolower(str_replace([' ', '_'], '-', $user['namaRole']));
            error_log("[AuthDebug] Normalized Role: {$normalized_role}");

            if (!empty($role_input) && $role_input !== $normalized_role) {
                error_log("[AuthDebug] Role mismatch. Input: {$role_input}, Actual: {$normalized_role}");
                throw new ValidationException("Akun ini tidak terdaftar sebagai " . ucfirst($role_input), ['login_role' => ["Peran yang dipilih tidak sesuai."]]);
            }

            session_regenerate_id(true);
            unset($_SESSION['login_error']);
            unset($_SESSION['flash_errors']);
            unset($_SESSION['old_input']);

            $_SESSION['user_id']       = $user['userId'];
            $_SESSION['user_name']     = $user['nama'];
            $_SESSION['user_role']     = $normalized_role;
            $_SESSION['user_jurusan']  = $user['namaJurusan'] ?? null;

            $_SESSION['user_data'] = [
                'id'            => $user['userId'],
                'username'      => $user['nama'],
                'email'         => $email,
                'role'          => $normalized_role,
                'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=0D8ABC&color=fff&size=150',
                'header_bg'     => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%)',
                'created_at'    => date('Y-m-d')
            ];

            if (function_exists('logLogin')) {
                logLogin($user['userId'], $email, true);
            }

            error_log("[AuthDebug] Login successful. Redirecting to role dashboard.");
            $this->redirectBasedOnRole($normalized_role, $user);
        } catch (ValidationException $e) {
            // The global exception handler will now catch this and redirect.
            // Storing errors in session for the view to display.
            $_SESSION['flash_errors'] = $e->getErrors();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/docutrack/public/');
        }
    }

    private function redirectBasedOnRole($normalized_role, $user)
    {
        $dashboardMap = [
            'verifikator' => '/verifikator/dashboard',
            'wadir' => '/wadir/dashboard',
            'ppk' => '/ppk/dashboard',
            'bendahara' => '/bendahara/dashboard',
            'admin' => '/admin/dashboard',
            'super-admin' => '/superadmin/dashboard',
            'superadmin' => '/superadmin/dashboard',
            'direktur' => '/direktur/dashboard'
        ];

        if (isset($dashboardMap[$normalized_role])) {
            $this->redirect('/docutrack/public' . $dashboardMap[$normalized_role]);
        } else {
            error_log("DocuTrack Login Warning: Unknown role '{$normalized_role}' for user {$user['email']}");
            $_SESSION['login_error'] = 'Role pengguna tidak dikenali: ' . htmlspecialchars($user['namaRole']);
            $this->redirect('/docutrack/public/');
        }
    }

    private function validateCaptcha($inputCode)
    {
        if (!isset($_SESSION['captcha_code'])) {
            throw new ValidationException("CAPTCHA tidak valid. Silakan refresh halaman.", ['captcha_code' => ["CAPTCHA tidak ditemukan."]]);
        }
        
        // Check if CAPTCHA is expired (5 minutes)
        if (isset($_SESSION['captcha_time']) && (time() - $_SESSION['captcha_time']) > 300) {
            unset($_SESSION['captcha_code']);
            unset($_SESSION['captcha_time']);
            throw new ValidationException("CAPTCHA telah kadaluarsa. Silakan refresh.", ['captcha_code' => ["CAPTCHA kadaluarsa."]]);
        }
        
        if (strtoupper($inputCode) !== strtoupper($_SESSION['captcha_code'])) {
            throw new ValidationException("Kode CAPTCHA salah.", ['captcha_code' => ["Kode CAPTCHA tidak sesuai."]]);
        }
        
        // Clear CAPTCHA after successful validation
        unset($_SESSION['captcha_code']);
        unset($_SESSION['captcha_time']);
    }

    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        if ($userId > 0 && function_exists('logLogout')) {
            logLogout($userId);
        }

        session_destroy();
        $this->redirect('/docutrack/public/');
    }
}
