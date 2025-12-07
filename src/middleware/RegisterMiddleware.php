<?php
namespace App\Middleware;

class RegisterMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /docutrack/public');
            exit;
        }
    }
}