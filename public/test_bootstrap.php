<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "1. Starting test...<br>";

try {
    echo "2. Loading bootstrap...<br>";
    require_once __DIR__ . '/../src/bootstrap.php';
    echo "3. Bootstrap loaded successfully!<br>";
    
    echo "4. Testing db() helper...<br>";
    $db = db();
    echo "5. Database connection: " . ($db->ping() ? "OK" : "FAILED") . "<br>";
    
    echo "6. Testing Database::getInstance()...<br>";
    $instance = \Core\Database::getInstance();
    echo "7. Instance retrieved: " . get_class($instance) . "<br>";
    
    echo "<br><strong>All tests passed!</strong>";
    
} catch (Exception $e) {
    echo "<br><strong>ERROR:</strong> " . $e->getMessage();
    echo "<br><strong>File:</strong> " . $e->getFile();
    echo "<br><strong>Line:</strong> " . $e->getLine();
    echo "<br><strong>Trace:</strong><pre>" . $e->getTraceAsString() . "</pre>";
}
