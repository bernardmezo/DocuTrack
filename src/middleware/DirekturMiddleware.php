<?php

namespace App\Middleware;

class DirekturMiddleware
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'direktur') {
            http_response_code(403);
            if (getenv('APP_ENV') === 'development') {
                echo "Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini. (Role: " . ($_SESSION['user_role'] ?? 'None') . ")";
            } else {
                echo "Akses Ditolak.";
            }
            exit;
        }
    }
    
    // Static alias for backward compatibility if needed
    public static function check()
    {
        (new self())->handle();
    }
}
