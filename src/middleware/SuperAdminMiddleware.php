<?php
// File: src/middleware/SuperAdminMiddleware.php

class SuperAdminMiddleware
{
    /**
     * Memeriksa apakah user yang sedang login memiliki role 'super_admin' atau 'super administrator'
     * Jika tidak, redirect ke halaman dashboard sesuai role mereka
     */
    public static function check()
    {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Cek apakah user sudah login (AuthMiddleware sudah handle ini, tapi double check)
        if (!isset($_SESSION['user_data'])) {
            header('Location: /docutrack/public/');
            exit;
        }

        // Ambil role user dari session
        $userRole = strtolower($_SESSION['user_data']['role'] ?? '');

        // Cek apakah role adalah super admin (support berbagai format)
        $allowedRoles = ['super_admin', 'super administrator', 'superadmin'];
        
        if (!in_array($userRole, $allowedRoles)) {
            // Jika bukan super admin, redirect ke dashboard sesuai role mereka
            self::redirectToDashboard($userRole);
        }

        // Jika sampai sini, berarti user adalah super admin, boleh lanjut
    }

    /**
     * Redirect user ke dashboard sesuai role mereka
     */
    private static function redirectToDashboard($role)
    {
        $redirectPath = '/docutrack/public/';

        switch ($role) {
            case 'admin':
                $redirectPath = '/docutrack/public/admin/dashboard';
                break;
            case 'verifikator':
                $redirectPath = '/docutrack/public/verifikator/dashboard';
                break;
            case 'wadir':
                $redirectPath = '/docutrack/public/wadir/dashboard';
                break;
            case 'ppk':
                $redirectPath = '/docutrack/public/ppk/dashboard';
                break;
            case 'bendahara':
                $redirectPath = '/docutrack/public/bendahara/dashboard';
                break;
            default:
                $redirectPath = '/docutrack/public/';
        }

        header("Location: $redirectPath");
        exit;
    }
}