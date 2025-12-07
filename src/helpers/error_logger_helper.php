<?php
/**
 * Error Logger Helper - DocuTrack
 * ================================
 * File ini berisi fungsi untuk mencatat error aplikasi ke dalam file log.
 * Ini terpisah dari audit logger untuk menjaga pemisahan kepentingan (separation of concerns).
 */

if (!defined('DOCUTRACK_ROOT')) {
    define('DOCUTRACK_ROOT', dirname(__DIR__, 2));
}

/**
 * Mencatat pesan error ke file log aplikasi.
 *
 * @param int    $code      Kode status HTTP (misalnya 500).
 * @param string $message   Pesan error.
 * @param string $file      File tempat error terjadi.
 * @param int    $line      Baris tempat error terjadi.
 * @param string $requestUri URI yang diminta saat error terjadi.
 * @return void
 */
function log_error(int $code, string $message, string $file, int $line, string $requestUri = ''): void
{
    $logDirectory = DOCUTRACK_ROOT . '/logs';
    $logFile = $logDirectory . '/app.log';

    // Pastikan direktori log ada
    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0775, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = sprintf(
        "[%s] [%s] %s in %s on line %d. Request URI: %s" . PHP_EOL,
        $timestamp,
        $code,
        $message,
        $file,
        $line,
        $requestUri
    );

    // Tulis ke file log, FILE_APPEND untuk menambahkan ke konten yang ada
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
