<?php
namespace App\Middleware;

class SuperAdminMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super-admin') {
            http_response_code(403);
            echo "Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman Super Admin.";
            exit;
        }
    }
}