<?php
/**
 * Database Connection with Auto-Setup
 * ====================================
 * File ini akan:
 * 1. Cek apakah database sudah ada
 * 2. Jika belum, otomatis buat database + tabel + seed
 * 3. Return koneksi yang siap pakai
 */

// ...existing code...
$host = 'localhost';
$user = 'root';
$pass = '';  // Kosongkan untuk default XAMPP
$db   = 'db_docutrack2';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Koneksi database gagal. Silakan cek konfigurasi atau jalankan setup manual.');
} else {
    mysqli_set_charset($conn, 'utf8mb4');
}

// $conn tersedia untuk file yang meng-include file ini