<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'docutrack_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Catat detail error ke log server (jangan tampilkan detail ke user)
    error_log('Database connection failed: ' . mysqli_connect_error());
    // Hentikan eksekusi dengan pesan generik
    die('Koneksi database gagal. Silakan cek konfigurasi atau log server.');
} else {
    // Set karakter set ke UTF-8
    mysqli_set_charset($conn, 'utf8');
}

// $conn tersedia untuk file yang meng-include file ini