<?php

// Prevent direct access
if (!defined('DOCUTRACK_ROOT')) {
    define('DOCUTRACK_ROOT', dirname(__DIR__));
}

// Error Handling
ini_set('display_errors', getenv('APP_ENV') === 'development' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', DOCUTRACK_ROOT . '/logs/php_error.log');
error_reporting(E_ALL);

if (getenv('APP_ENV') === 'production') {
    set_error_handler(function($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\'         => DOCUTRACK_ROOT . '/src/core/',
        'Controllers\\'  => DOCUTRACK_ROOT . '/src/controllers/',
        'Models\\'       => DOCUTRACK_ROOT . '/src/model/',
        'Helpers\\'      => DOCUTRACK_ROOT . '/src/helpers/',
        'Middleware\\'   => DOCUTRACK_ROOT . '/src/middleware/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

if (file_exists(DOCUTRACK_ROOT . '/vendor/autoload.php')) {
    require DOCUTRACK_ROOT . '/vendor/autoload.php';
}

// Config
$config = [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'name' => getenv('DB_NAME') ?: 'db_docutrack2',
    ],
    'app' => [
        'name' => 'DocuTrack',
        'version' => '2.0.0',
        'base_url' => getenv('APP_URL') ?: 'http://localhost/docutrack/public',
        'timezone' => 'Asia/Jakarta',
    ],
];

date_default_timezone_set($config['app']['timezone']);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

// Database Init
require_once DOCUTRACK_ROOT . '/src/core/Database.php';

try {
    $database = \Core\Database::getInstance($config['db']);
    $conn = $database->getConnection();
} catch (Exception $e) {
    error_log('Bootstrap: ' . $e->getMessage());
    
    if (getenv('APP_ENV') === 'development') {
        die('Database Error: ' . $e->getMessage());
    } else {
        die('Application initialization failed.');
    }
}

// Helper Functions
function db(): mysqli
{
    return \Core\Database::getInstance()->getConnection();
}

function redirect(string $path, int $statusCode = 302): void
{
    header("Location: {$path}", true, $statusCode);
    exit;
}

function flash(string $key, $value = null)
{
    if ($value !== null) {
        $_SESSION["flash_{$key}"] = $value;
        return $value;
    }

    $flashValue = $_SESSION["flash_{$key}"] ?? null;
    unset($_SESSION["flash_{$key}"]);
    return $flashValue;
}

// Legacy Compatibility
if (file_exists(DOCUTRACK_ROOT . '/src/helpers/date_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/date_helper.php';
}

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/security_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/security_helper.php';
}

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/logger_helper.php';
}

return [
    'database' => $database,
    'connection' => $conn,
    'config' => $config,
];