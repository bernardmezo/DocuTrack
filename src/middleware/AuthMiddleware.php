<?php
// src/middleware/AuthMiddleware.php

class AuthMiddleware {
    
    /**
     * Memeriksa apakah pengguna sudah login.
     * Jika belum, arahkan ke halaman landing (dengan popup login).
     */
    public static function check() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cek apakah session user_id sudah ada
        if (!isset($_SESSION['user_id'])) {
            // Jika belum login, simpan URL yang dituju (opsional)
            // $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            // Arahkan ke halaman landing (bukan /login karena itu akan loop)
            header('Location: /docutrack/public/');
            exit; // Hentikan eksekusi script selanjutnya
        }
        
        // Jika sudah login, biarkan script lanjut
    }
}