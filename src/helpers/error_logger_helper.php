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
function log_error(string $level, int $code, string $message, string $file, int $line, string $requestUri = ''): void
{
    $logDirectory = DOCUTRACK_ROOT . '/logs';
    $logFile = $logDirectory . '/app.log';

    // Pastikan direktori log ada
    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0775, true);
    }

    // Collect additional context
    $context = [
        'session_id' => session_id(),
        'user_id' => $_SESSION['user_id'] ?? null,
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
        'request_get' => $_GET,
        'request_post' => $_POST,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
    ];

    // Sanitize sensitive data from request_post
    if (isset($context['request_post']['password'])) {
        $context['request_post']['password'] = '[REDACTED]';
    }
    if (isset($context['request_post']['old_password'])) {
        $context['request_post']['old_password'] = '[REDACTED]';
    }
    if (isset($context['request_post']['new_password'])) {
        $context['request_post']['new_password'] = '[REDACTED]';
    }
    if (isset($context['request_post']['confirm_password'])) {
        $context['request_post']['confirm_password'] = '[REDACTED]';
    }

    $logEntry = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'code' => $code,
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'request_uri' => $requestUri,
        'context' => $context,
        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) // Add stack trace
    ]) . PHP_EOL;

    // Rotasi log jika file terlalu besar (misalnya 1MB)
    $maxLogSize = 1 * 1024 * 1024; // 1 MB
    if (file_exists($logFile) && filesize($logFile) > $maxLogSize) {
        rename($logFile, $logFile . '.1');
    }

    // Tulis ke file log, FILE_APPEND untuk menambahkan ke konten yang ada
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
