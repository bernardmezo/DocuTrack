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
        
        // 1. Optimize: Pre-sort routes into Static (O(1)) and Dynamic (Regex)
        foreach ($allRoutes as $pattern => $config) {
            if (strpos($pattern, '{') === false) {
                $this->staticRoutes[$pattern] = $config;
            } else {
                $this->dynamicRoutes[$pattern] = $config;
            }
        }
        
        router_debug("Loaded " . count($allRoutes) . " routes (Static: " . count($this->staticRoutes) . ", Dynamic: " . count($this->dynamicRoutes) . ")");
    }

    public function dispatch($requestPath, $requestMethod)
    {
        // 1. URL SANITIZATION:
        // get_request_path() already provides a clean path, but we'll ensure robustness.
        // It should already have used parse_url(PHP_URL_PATH) and urldecoded the path.
        $cleanPath = $requestPath; 
        
        // Ensure leading slash and remove trailing slash (if not root)
        if ($cleanPath !== '/' && substr($cleanPath, -1) === '/') {
            $cleanPath = rtrim($cleanPath, '/');
        }
        if (substr($cleanPath, 0, 1) !== '/') {
            $cleanPath = '/' . $cleanPath;
        }

        router_debug("Dispatching sanitized request for Path: '{$cleanPath}', Method: '{$requestMethod}'");

        // Attempt to match static routes first for efficiency (O(1) lookup)
        if (isset($this->staticRoutes[$cleanPath])) {
            router_debug("Static route hit for: '{$cleanPath}'");
            $this->executeRoute($this->staticRoutes[$cleanPath], [], $requestMethod, $cleanPath);
            return;
        }

        // If no static route matches, iterate through dynamic routes
        foreach ($this->dynamicRoutes as $routePattern => $routeConfig) {
            $params = $this->matchRoute($routePattern, $cleanPath);

            if ($params !== false) {
                router_debug("Dynamic route matched: '{$routePattern}'");
                $this->executeRoute($routeConfig, $params, $requestMethod, $routePattern);
                return;
            }
        }

        // If no route matches after checking both static and dynamic routes
        error_log("ERROR Router: No route matched for path '{$cleanPath}' (Original: '{$requestPath}') and method '{$requestMethod}'.");
        throw new NotFoundException('Page not found.');
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
        // 1. Extract param names
        if (!preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $routePattern, $paramMatches)) {
            return false;
        }
        $paramNames = $paramMatches[1];

        // 2. Build Regex
        $regex = preg_quote($routePattern, '#');

        // Robust handling for Optional Parameters: /{param?}
        // Matches literal slash followed by the parameter pattern.
        // Replaces with (?:/([^/]*))? -> Optional non-capturing group containing slash and capture group.
        $regex = preg_replace('#/\\\{([a-zA-Z0-9_]+)\\\\\?\\\\}\\}#', '(?:/([^/]*))?', $regex);

        // Robust handling for Required Parameters: {param}
        // Matches {param}.
        // Replaces with ([^/]+) -> Required capture group (anything but slash).
        $regex = preg_replace('#\\\{([a-zA-Z0-9_]+)\\\\}#', '([^/]+)', $regex);

        $regex = '#^' . $regex . '$#';

        // 3. Match
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
