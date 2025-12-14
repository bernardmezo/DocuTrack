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

// Debug Mode Configuration
define('DEBUG_MODE', getenv('APP_ENV') === 'development');

if (getenv('APP_ENV') === 'production') {
    set_error_handler(function ($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
}

// Autoloader (PSR-4)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = DOCUTRACK_ROOT . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

if (file_exists(DOCUTRACK_ROOT . '/vendor/autoload.php')) {
    require DOCUTRACK_ROOT . '/vendor/autoload.php';
}

// Load .env file variables
if (class_exists('Dotenv\Dotenv') && file_exists(DOCUTRACK_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(DOCUTRACK_ROOT);
    $dotenv->safeLoad();
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


try {
    $database = \App\Core\Database::getInstance($config['db']);
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
    return \App\Core\Database::getInstance()->getConnection();
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

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/debug_logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/debug_logger_helper.php';
}

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/url_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/url_helper.php';
}

return [
    'database' => $database,
    'connection' => $conn,
    'config' => $config,
];
