<?php
// src/middleware/BendaharaMiddleware.php

class BendaharaMiddleware {
    
    /**
     * Memeriksa apakah pengguna memiliki role 'bendahara'.
     * HARUS dijalankan SETELAH AuthMiddleware::check().
     */
    public static function check() {
        // Cek apakah role user ada di session DAN apakah role-nya 'bendahara'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'bendahara') {
            http_response_code(403); // Forbidden
            echo "Akses Ditolak. Anda bukan Bendahara.";
            // Anda bisa memuat view error 403 di sini
            exit; 
        }
    }
}