<?php

namespace App\Core;

use App\Exceptions\NotFoundException;
use App\Exceptions\MethodNotAllowedException;

class Router
{
    protected $staticRoutes = [];
    protected $dynamicRoutes = [];
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
        $allRoutes = require __DIR__ . '/../routes.php';
        
        // Optimize: Pre-sort routes into Static (O(1)) and Dynamic (Regex)
        foreach ($allRoutes as $pattern => $config) {
            if (strpos($pattern, '{') === false) {
                $this->staticRoutes[$pattern] = $config;
            } else {
                $this->dynamicRoutes[$pattern] = $config;
            }
        }
    }

    public function dispatch($requestPath, $requestMethod)
    {
        $cleanPath = $this->normalizePath($requestPath);
        $pathMatched = false;

        // 1. Check Static Routes (O(1))
        if (isset($this->staticRoutes[$cleanPath])) {
            $pathMatched = true;
            $routeConfig = $this->staticRoutes[$cleanPath];
            
            if ($this->checkMethod($routeConfig, $requestMethod)) {
                $this->executeRoute($routeConfig, [], $cleanPath);
                return;
            } else {
                 throw new MethodNotAllowedException("Method $requestMethod not allowed for $cleanPath");
            }
        }

        // 2. Check Dynamic Routes (Regex)
        foreach ($this->dynamicRoutes as $routePattern => $routeConfig) {
            $params = $this->matchRoute($routePattern, $cleanPath);

            if ($params !== false) {
                $pathMatched = true;
                if ($this->checkMethod($routeConfig, $requestMethod)) {
                    $this->executeRoute($routeConfig, $params, $routePattern);
                    return;
                } else {
                     // If we match a dynamic route but method fails, we assume this is the intended resource
                     // and throw 405.
                     throw new MethodNotAllowedException("Method $requestMethod not allowed for $cleanPath");
                }
            }
        }

        // If no route matches at all
        error_log("Router: 404 Not Found for $cleanPath");
        throw new NotFoundException('Page not found.');
    }

    protected function checkMethod($routeConfig, $requestMethod)
    {
        // If 'methods' is defined, enforce it. If not, allow all (or default to GET/POST implied).
        // Usually, if not defined, we allow any.
        if (isset($routeConfig['methods']) && !in_array($requestMethod, $routeConfig['methods'])) {
            return false;
        }
        return true;
    }

    /**
     * Normalizes the request path:
     * 1. Strips Query Strings.
     * 2. Detects and strips sub-folder Base Path (Windows/Linux compatible).
     * 3. Ensures leading slash and no trailing slash.
     */
    protected function normalizePath($path)
    {
        // 1. Strip Query String
        $path = parse_url($path, PHP_URL_PATH);

        // 2. Auto-detect and strip Base Path (e.g. /docutrack/public)
        // This handles cases where the app is in a sub-folder and the caller passed the full URI.
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $basePath = dirname($scriptName);
            
            // Normalize backslashes to forward slashes (Windows fix)
            $basePath = str_replace('\\', '/', $basePath);
            $path = str_replace('\\', '/', $path);

            // Ensure Base Path is not root '/' or empty dot '.'
            if ($basePath !== '/' && $basePath !== '.') {
                $basePath = rtrim($basePath, '/');

                // If path starts with base path, strip it (Case-insensitive check for safety)
                if (stripos($path, $basePath) === 0) {
                    $path = substr($path, strlen($basePath));
                }
            }
        }

        // 3. Ensure consistent format (Starts with /, no trailing / unless it is root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        if (empty($path) || substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        return $path;
    }

    protected function executeRoute($routeConfig, $params, $matchedPattern)
    {
        // Middleware Execution
        if (isset($routeConfig['middleware'])) {
            foreach ($routeConfig['middleware'] as $middlewareClass) {
                // Support short names (e.g., 'AuthMiddleware') or FQCN
                $middlewareFQN = strpos($middlewareClass, '\\') === false 
                    ? "App\\Middleware\\{$middlewareClass}" 
                    : $middlewareClass;

                if (!class_exists($middlewareFQN)) {
                     error_log("WARNING Router: Middleware class '{$middlewareFQN}' not found.");
                     continue;
                }

                // 1. Try Instantiated 'handle()' (Modern Pattern)
                if (method_exists($middlewareFQN, 'handle')) {
                    $instance = new $middlewareFQN();
                    $instance->handle();
                } 
                // 2. Try Static 'check()' (Legacy Pattern)
                elseif (method_exists($middlewareFQN, 'check')) {
                    $middlewareFQN::check();
                } else {
                    error_log("WARNING Router: No 'handle' or 'check' method found in middleware '{$middlewareClass}'.");
                }
            }
        }

        $controllerName = "App\\Controllers\\{$routeConfig['controller']}";
        $methodName = $routeConfig['method'];

        if (!class_exists($controllerName)) {
             error_log("ERROR Router: Controller class '{$controllerName}' not found.");
             throw new NotFoundException("Controller $controllerName not found");
        }

        $controller = new $controllerName($this->db);

        if (!method_exists($controller, $methodName)) {
             error_log("ERROR Router: Method '{$methodName}' not found in Controller '{$controllerName}'.");
             throw new NotFoundException("Method $methodName not found in $controllerName");
        }

        call_user_func_array([$controller, $methodName], array_values($params));
    }

    protected function matchRoute($routePattern, $requestPath)
    {
        // Extract param names
        if (!preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $routePattern, $paramMatches)) {
            return false;
        }
        $paramNames = $paramMatches[1];

        // Build Regex
        $regex = preg_quote($routePattern, '#');

        // Robust handling for Optional Parameters: /{param?}
        // Matches literal slash followed by the parameter pattern.
        // Replaces with (?:/([^/]*))? -> Optional non-capturing group containing slash and capture group.
        $regex = preg_replace('#/\\\{([a-zA-Z0-9_]+)\\\\?\\\\}#', '(?:/([^/]*))?', $regex);

        // Robust handling for Required Parameters: {param}
        // Matches {param}.
        // Replaces with ([^/]+) -> Required capture group (anything but slash).
        $regex = preg_replace('#\\\{([a-zA-Z0-9_]+)\\}#', '([^/]+)', $regex);

        $regex = '#^' . $regex . '$#';

        // Match
        if (preg_match($regex, $requestPath, $matches)) {
            array_shift($matches); // Remove full match

            $params = [];
            foreach ($paramNames as $index => $name) {
                // Determine value (use null if optional and missing)
                $val = (isset($matches[$index]) && $matches[$index] !== '') ? $matches[$index] : null;
                // Decode URI params (e.g. %20 -> space)
                $params[$name] = $val !== null ? urldecode($val) : null;
            }
            return $params;
        }

        return false;
    }
}