<?php

/**
 * Debug Logger Helper
 * 
 * Memisahkan DEBUG logging ke file terpisah untuk menghindari polusi di php_error.log
 */

if (!function_exists('debug_log')) {
    /**
     * Log debug message ke file debug.log
     * 
     * @param string $message Pesan debug
     * @param string $context Konteks (Router, Controller, Model, dll)
     * @return void
     */
    function debug_log(string $message, string $context = 'DEBUG'): void
    {
        // Hanya log jika dalam mode development
        $isDebugMode = defined('DEBUG_MODE') && DEBUG_MODE === true;
        
        if (!$isDebugMode) {
            return;
        }
        
        $logDir = __DIR__ . '/../../logs';
        $logFile = $logDir . '/debug.log';
        
        // Pastikan direktori logs ada
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('[d-M-Y H:i:s T]');
        $formattedMessage = "{$timestamp} {$context}: {$message}" . PHP_EOL;
        
        file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('router_debug')) {
    /**
     * Shortcut untuk Router debug logging
     */
    function router_debug(string $message): void
    {
        debug_log($message, 'DEBUG Router');
    }
}

if (!function_exists('controller_debug')) {
    /**
     * Shortcut untuk Controller debug logging
     */
    function controller_debug(string $message): void
    {
        debug_log($message, 'DEBUG Controller');
    }
}
