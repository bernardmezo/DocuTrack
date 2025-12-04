<?php
// src/middleware/VerifikatorMiddleware.php

class VerifikatorMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'verifikator'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cek apakah role user ada di session DAN apakah role-nya 'verifikator'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'verifikator') {
            http_response_code(403); // Forbidden
            echo "Akses Ditolak. Anda bukan Verifikator.";
            // Anda bisa memuat view error 403 di sini
            exit; 
        }
    }
}