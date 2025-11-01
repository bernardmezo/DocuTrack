<?php
// src/middleware/PpkMiddleware.php

class PpkMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'ppk'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Cek apakah role user ada di session DAN apakah role-nya 'ppk'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ppk') {
            http_response_code(403); // Forbidden
            echo "Akses Ditolak. Anda bukan PPK.";
            // Anda bisa memuat view error 403 di sini
            exit; 
        }
    }
}