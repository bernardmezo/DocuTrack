<?php
// src/middleware/PpkMiddleware.php

class PPKMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'ppk'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cek apakah role user ada di session DAN apakah role-nya 'ppk'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ppk') {
            http_response_code(403); // Forbidden
            echo "Akses Ditolak. Anda bukan PPK.";
            exit; 
        }
    }
}