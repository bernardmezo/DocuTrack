<?php
namespace App\Middleware;

class WadirMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'wadir') {
            http_response_code(403);
            echo "Akses Ditolak. Anda bukan Wakil Direktur.";
            exit; 
        }
    }
}
