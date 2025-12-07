<?php
/**
 * DocuTrack Application Entry Point
 *
 * This file initializes the application, sets up global error handling,
 * and dispatches requests to the router.
 */

// 1. Bootstrap the application
// This file handles configuration, autoloading, session, and database connection.
require_once __DIR__ . '/../src/bootstrap.php';

use App\Core\Router;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;

// 2. Set up Global Exception Handling
require_once __DIR__ . '/../src/helpers/error_logger_helper.php';

/**
 * Handles all uncaught exceptions in the application.
 * Logs fatal errors and displays user-friendly error pages.
 *
 * @param Throwable $exception The exception that was thrown.
 */
function globalExceptionHandler(Throwable $exception) {
    // Clear any previously buffered output
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Log the error for all exception types except for 404s
    if (!($exception instanceof NotFoundException)) {
        log_error(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $request_uri
        );
    }

    // Always show detailed error in development for debugging purposes
    if (getenv('APP_ENV') === 'development') {
        http_response_code(500); // Default to 500 for dev display
        echo "<h1>Exception Occurred</h1>";
        echo "<p><strong>Type:</strong> " . get_class($exception) . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " on line " . $exception->getLine() . "</p>";
        echo "<hr><h3>Stack Trace:</h3><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        exit;
    }

    // Handle specific exceptions for production environment
    switch (get_class($exception)) {
        case ValidationException::class:
            http_response_code(422); // Unprocessable Entity
            // In a real app, you'd redirect back with errors in session
            if (isset($_SERVER['HTTP_REFERER'])) {
                $_SESSION['flash_errors'] = $exception->getErrors();
                $_SESSION['old_input'] = $_POST;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                include __DIR__ . '/../src/views/pages/errors/500.php';
            }
            break;

        case NotFoundException::class:
            http_response_code(404);
            include __DIR__ . '/../src/views/pages/errors/404.php';
            break;

        case ForbiddenException::class:
            http_response_code(403);
            include __DIR__ . '/../src/views/pages/errors/403.php';
            break;

        default:
            http_response_code(500);
            include __DIR__ . '/../src/views/pages/errors/500.php';
            break;
    }
    exit;
}

set_exception_handler('globalExceptionHandler');

/**
 * Gets the request path from the URL, stripping the base directory and query string.
 *
 * @return string The clean request path (e.g., '/admin/dashboard').
 */
function get_request_path(): string 
{
    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    $base_path = dirname($script_name);
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    }
    
    $request_path = $request_uri;
    
    if ($base_path && strpos($request_uri, $base_path) === 0) {
        $request_path = substr($request_uri, strlen($base_path));
    }
    
    $request_path = parse_url($request_path, PHP_URL_PATH);
    
    if (empty($request_path) || $request_path === '/index.php') {
        return '/';
    }
    
    return $request_path;
}

// 3. Get the database connection from the bootstrap helper.
$db = db(); 

// 4. Instantiate and dispatch the router.
// Any exception thrown from here will be caught by our globalExceptionHandler.
$router = new Router($db);
$router->dispatch(get_request_path(), $_SERVER['REQUEST_METHOD']);