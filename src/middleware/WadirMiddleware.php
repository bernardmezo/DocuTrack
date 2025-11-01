<?php
// src/middleware/WadirMiddleware.php

class WadirMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'wadir'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Cek apakah role user ada di session DAN apakah role-nya 'wadir'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'wadir') {
            http_response_code(403); // Forbidden
            echo "Akses Ditolak. Anda bukan Wakil Direktur.";
            // Anda bisa memuat view error 403 di sini
            exit; 
        }
    }
}