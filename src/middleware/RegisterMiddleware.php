<?php
// src/middleware/RegisterMiddleware.php

class RegisterMiddleware {
    /**
     * Memeriksa apakah pengguna sudah login.
     * Jika belum, arahkan ke halaman register.
     */
    public static function check() {
        // Cek apakah session user_id sudah ada
        if (!isset($_SESSION['user_id'])) {
            // Arahkan ke halaman register (popup)
            header('Location: /docutrack/public');
            exit; // Hentikan eksekusi script selanjutnya
        }
        // Jika sudah login, biarkan script lanjut
    }
}
