<?php
namespace App\Middleware;

class BendaharaMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'bendahara') {
            http_response_code(403);
            echo "Akses Ditolak. Anda bukan Bendahara.";
            exit; 
        }
    }
}
