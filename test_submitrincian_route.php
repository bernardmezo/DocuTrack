<?php
// Test submitrincian route
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "\nFATAL ERROR:\n";
        echo "Type: " . $error['type'] . "\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . "\n";
        echo "Line: " . $error['line'] . "\n";
    }
});

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'kegiatan_id' => 1,
    'penanggung_jawab' => 'Test User',
    'nim_nip_pj' => '123456',
    'tanggal_mulai' => '2025-01-01',
    'tanggal_selesai' => '2025-01-31'
];

// Initialize session
session_start();
$_SESSION['user'] = [
    'userId' => 1,
    'userRole' => 'admin',
    'nama' => 'Test Admin'
];

// Load bootstrap
require_once __DIR__ . '/src/bootstrap.php';

use App\Controllers\Admin\AdminController;

try {
    echo "Creating AdminController...\n";
    ob_start();
    $controller = new AdminController();
    
    echo "Calling submitRincian method...\n";
    $controller->submitRincian();
    $output = ob_get_clean();
    
    echo "Method output: " . $output . "\n";
    echo "Method executed successfully!\n";
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "Buffered output: " . $output . "\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
