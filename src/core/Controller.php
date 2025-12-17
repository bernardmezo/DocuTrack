<?php

namespace App\Core;

use Exception;
use App\Services\ValidationService;
use App\Services\FileUploadService;

class Controller
{
    protected $db;
    protected $validationService;
    protected $fileUploadService;

    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } elseif (function_exists('db')) {
            $this->db = db();
        } elseif (isset($GLOBALS['conn'])) {
            $this->db = $GLOBALS['conn'];
        }

        // Instantiate stateless services once in the base controller
        $this->validationService = new ValidationService();
        $this->fileUploadService = new FileUploadService();

        // HTTP SECURITY HEADERS
        if (!headers_sent()) {
            header("X-Frame-Options: DENY");
            header("X-Content-Type-Options: nosniff");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https://ui-avatars.com https://via.placeholder.com;");
        }

        // GLOBAL SECURITY SHIELD
        // Automatically scan all incoming requests based on active policy
        if (class_exists('App\\Services\\AiSecurityService')) {
            $security = new \App\Services\AiSecurityService($this->db);
            $security->handleSecurity();
        }
    }

    protected function safeModelCall($model, string $method, array $params = [], $defaultReturn = [])
    {
        try {
            if (!method_exists($model, $method)) {
                error_log("Method {$method} not found in " . get_class($model));
                return $defaultReturn;
            }

            $result = call_user_func_array([$model, $method], $params);

            if (is_array($defaultReturn) && $result === null) {
                return [];
            }

            return $result ?? $defaultReturn;
        } catch (Exception $e) {
            error_log("Error in " . get_class($model) . "::{$method} - " . $e->getMessage());
            return $defaultReturn;
        }
    }

    protected function setFlashMessage(string $type, string $message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_' . $type] = $message;
    }

    protected function redirectWithMessage(string $url, string $type, string $message)
    {
        $this->setFlashMessage($type, $message);
        header("Location: {$url}");
        exit;
    }

    protected function view(string $view, array $data = [], $layout = 'app')
    {
        return $this->viewLegacy($view, $data, $layout);
    }

    protected function redirect($url, $statusCode = 302)
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    public function viewLegacy($view, $data = [], $layout = 'app')
    {
        extract($data, EXTR_SKIP);

        $header_file = DOCUTRACK_ROOT . '/src/views/layouts/' . $layout . '/header.php';
        $footer_file = DOCUTRACK_ROOT . '/src/views/layouts/' . $layout . '/footer.php';
        $view_file = DOCUTRACK_ROOT . '/src/views/' . $view . '.php';

        // Add detailed checks for file existence
        if (!file_exists($header_file)) {
            error_log("Error: Header file not found: " . $header_file);
            die("Error: Header layout file not found.");
        }
        if (!file_exists($view_file)) {
            error_log("Error: View file not found: " . $view_file);
            die("Error: View file not found.");
        }
        if (!file_exists($footer_file)) {
            error_log("Error: Footer file not found: " . $footer_file);
            die("Error: Footer layout file not found.");
        }

        require_once $header_file;
        require_once $view_file;
        require_once $footer_file;
    }
}
