<?php

namespace App\Core;

use App\Exceptions\NotFoundException;

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
        
        error_log("Router: Loaded " . count($allRoutes) . " routes (Static: " . count($this->staticRoutes) . ", Dynamic: " . count($this->dynamicRoutes) . ")");
    }

    public function dispatch($requestPath, $requestMethod)
    {
        // ROBUST URL SANITIZATION & BASE PATH STRIPPING
        // We do this inside the Router to ensure it works regardless of the caller's cleanup logic.
        $cleanPath = $this->normalizePath($requestPath);

        // Attempt to match static routes first for efficiency (O(1) lookup)
        if (isset($this->staticRoutes[$cleanPath])) {
            $this->executeRoute($this->staticRoutes[$cleanPath], [], $requestMethod, $cleanPath);
            return;
        }

        // If no static route matches, iterate through dynamic routes
        foreach ($this->dynamicRoutes as $routePattern => $routeConfig) {
            $params = $this->matchRoute($routePattern, $cleanPath);

            if ($params !== false) {
                $this->executeRoute($routeConfig, $params, $requestMethod, $routePattern);
                return;
            }
        }

        // If no route matches after checking both static and dynamic routes
        error_log("ERROR Router: No route matched for path '{$cleanPath}' (Original: '{$requestPath}') and method '{$requestMethod}'.");
        throw new NotFoundException('Page not found.');
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

    protected function executeRoute($routeConfig, $params, $requestMethod, $matchedPattern)
    {
        // Strict Method Check
        if (isset($routeConfig['methods']) && !in_array($requestMethod, $routeConfig['methods'])) {
            error_log("ERROR Router: Method '{$requestMethod}' not allowed for route '{$matchedPattern}'. Expected: " . json_encode($routeConfig['methods']));
            // Returning here results in 404 behavior if no other route matches, which is acceptable for simple routers.
            // Ideally, this should throw a MethodNotAllowedException (405).
            return; 
        }

        if (isset($routeConfig['middleware'])) {
            foreach ($routeConfig['middleware'] as $middlewareClass) {
                $middlewareFQN = "App\\Middleware\\{$middlewareClass}";
                if (method_exists($middlewareFQN, 'check')) {
                    $middlewareFQN::check();
                } else {
                    error_log("WARNING Router: Middleware method 'check' not found for '{$middlewareClass}'.");
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