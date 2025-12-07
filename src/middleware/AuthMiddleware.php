<?php
namespace App\Middleware;

class AuthMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /docutrack/public/');
            exit;
        }
    }
}
