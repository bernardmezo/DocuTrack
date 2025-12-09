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
        extract($data);

        $header_file = DOCUTRACK_ROOT . '/src/views/layouts/' . $layout . '/header.php';
        $footer_file = DOCUTRACK_ROOT . '/src/views/layouts/' . $layout . '/footer.php';
        $view_file = DOCUTRACK_ROOT . '/src/views/' . $view . '.php';

        if (file_exists($header_file) && file_exists($footer_file) && file_exists($view_file)) {
            require_once $header_file;
            require_once $view_file;
            require_once $footer_file;
        } else {
            $missing = [];
            if (!file_exists($header_file)) {
                $missing[] = "Header ({$layout})";
            }
            if (!file_exists($footer_file)) {
                $missing[] = "Footer ({$layout})";
            }
            if (!file_exists($view_file)) {
                $missing[] = "View ({$view})";
            }
            die("Error: File not found: " . implode(', ', $missing));
        }
    }
}
