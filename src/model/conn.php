<?php
/**
 * Database Connection with Auto-Setup
 * ====================================
 * File ini akan:
 * 1. Cek apakah database sudah ada
 * 2. Jika belum, otomatis buat database + tabel + seed
 * 3. Return koneksi yang siap pakai
 */

$host = 'localhost';
$user = 'root';
$pass = '';  // Kosongkan untuk default XAMPP
$db   = 'db_docutrack2';

// Include auto-setup class
require_once __DIR__ . '/db_setup.php';

// Check if setup is needed
$setup = new DatabaseSetup($host, $user, $pass, $db);

if ($setup->needsSetup()) {
    // Run silent setup (no HTML output)
    $setup->run(true);
}

// Now connect normally
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Koneksi database gagal. Silakan cek konfigurasi atau jalankan setup manual.');
} else {
    mysqli_set_charset($conn, 'utf8mb4');
}

// $conn tersedia untuk file yang meng-include file ini