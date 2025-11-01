<?php
// src/middleware/AuthMiddleware.php

class AuthMiddleware {
    
    /**
     * Memeriksa apakah pengguna sudah login.
     * Jika belum, arahkan ke halaman login.
     */
    public static function check() {
        // Cek apakah session user_id sudah ada
        if (!isset($_SESSION['user_id'])) {
            // Jika belum login, simpan URL yang dituju (opsional)
            // $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            // Arahkan ke halaman login
            header('Location: /docutrack/public/login');
            exit; // Hentikan eksekusi script selanjutnya
        }
        
        // Jika sudah login, biarkan script lanjut
    }
}