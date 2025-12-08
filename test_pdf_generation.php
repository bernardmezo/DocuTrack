<?php
// Test PDF generation
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Register shutdown function
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

// Initialize session
session_start();
$_SESSION['user'] = [
    'userId' => 1,
    'userRole' => 'admin',
    'nama' => 'Test Admin'
];

// Load bootstrap
require_once __DIR__ . '/src/bootstrap.php';

use App\Controllers\Admin\PengajuanKegiatanController;

try {
    echo "Creating PengajuanKegiatanController...\n";
    $controller = new PengajuanKegiatanController();
    
    echo "Testing downloadPDF method with ID=1...\n";
    
    // Capture output
    ob_start();
    $controller->downloadPDF(1);
    $output = ob_get_clean();
    
    echo "PDF Generation completed!\n";
    echo "Output length: " . strlen($output) . " bytes\n";
    
    if (strlen($output) > 0) {
        echo "✓ PDF content generated successfully\n";
    }
    
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "Buffered output: " . substr($output, 0, 500) . "\n";
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
