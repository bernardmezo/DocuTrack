<?php
// src/middleware/AdminMiddleware.php

class AdminMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'admin'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Cek apakah role user ada di session DAN apakah role-nya 'admin'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Jika tidak punya akses, tampilkan pesan error
            http_response_code(403); // Kode status Forbidden
            echo "Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.";
            // Anda bisa juga membuat halaman error 403 yang lebih bagus
            // require '../src/views/pages/errors/403.php'; 
            exit; // Hentikan eksekusi script selanjutnya
        }
        
        // Jika role sesuai, biarkan script lanjut
    }
}