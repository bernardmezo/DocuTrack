<?php
// src/middleware/SuperAdminMiddleware.php

class SuperAdminMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'super_admin'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cek apakah role user ada di session DAN apakah role-nya 'super_admin'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
            // Jika tidak punya akses, tampilkan pesan error
            http_response_code(403); // Kode status Forbidden
            echo "Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman Super Admin.";
            // Anda bisa juga membuat halaman error 403 yang lebih bagus
            // require '../src/views/pages/errors/403.php'; 
            exit; // Hentikan eksekusi script selanjutnya
        }
        
        // Jika role sesuai, biarkan script lanjut
    }
}
