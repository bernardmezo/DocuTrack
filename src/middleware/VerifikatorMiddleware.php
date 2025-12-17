<?php

namespace App\Middleware;

class VerifikatorMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'verifikator') {
            http_response_code(403);
            echo "Akses Ditolak. Anda bukan Verifikator.";
            exit;
        }
    }
}
