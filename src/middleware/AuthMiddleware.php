<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            error_log("AuthMiddleware: user_id not set in session. Redirecting to login.");
            // Use helper if available, otherwise raw header
            if (function_exists('redirect')) {
                redirect('/login'); 
            } else {
                // Fallback to relative path or environment variable
                $loginPath = getenv('APP_URL') ? getenv('APP_URL') . '/login' : '/docutrack/public/login';
                header("Location: $loginPath");
                exit;
            }
        }
    }

    public static function check()
    {
        (new self())->handle();
    }
}
