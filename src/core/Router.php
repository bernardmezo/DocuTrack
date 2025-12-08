<?php

namespace App\Core;

use App\Exceptions\NotFoundException;

class Router
{
    protected $routes = [];
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->routes = require __DIR__ . '/../routes.php';
        router_debug("Loaded " . count($this->routes) . " routes.");
    }

    public function dispatch($requestPath, $requestMethod)
    {
        router_debug("Dispatching request for Path: '{$requestPath}', Method: '{$requestMethod}'");

        foreach ($this->routes as $routePattern => $routeConfig) {
            router_debug("Checking route pattern: '{$routePattern}'");
            $params = $this->matchRoute($routePattern, $requestPath);

            if ($params !== false) {
                router_debug("Route pattern '{$routePattern}' matched. Extracted params: " . json_encode($params));

                if (isset($routeConfig['methods']) && !in_array($requestMethod, $routeConfig['methods'])) {
                    router_debug("Method '{$requestMethod}' not allowed for route '{$routePattern}'. Expected: " . json_encode($routeConfig['methods']));
                    continue;
                }
                router_debug("HTTP Method '{$requestMethod}' allowed for route '{$routePattern}'.");


                if (isset($routeConfig['middleware'])) {
                    foreach ($routeConfig['middleware'] as $middlewareClass) {
                        $middlewareFQN = "App\\Middleware\\{$middlewareClass}";
                        if (method_exists($middlewareFQN, 'check')) {
                            router_debug("Applying middleware: {$middlewareClass}");
                            $middlewareFQN::check();
                        } else {
                            error_log("WARNING Router: Middleware method 'check' not found for '{$middlewareClass}'.");
                        }
                    }
                }

                $controllerName = "App\\Controllers\\{$routeConfig['controller']}";
                $methodName = $routeConfig['method'];

                router_debug("Attempting to call Controller: '{$controllerName}', Method: '{$methodName}'");


                if (!class_exists($controllerName)) {
                     error_log("ERROR Router: Controller class '{$controllerName}' not found.");
                     throw new NotFoundException("Controller $controllerName not found");
                }

                $controller = new $controllerName($this->db);
                router_debug("Controller '{$controllerName}' instantiated successfully.");


                if (!method_exists($controller, $methodName)) {
                     error_log("ERROR Router: Method '{$methodName}' not found in Controller '{$controllerName}'.");
                     throw new NotFoundException("Method $methodName not found in $controllerName");
                }

                router_debug("Calling Controller method '{$controllerName}::{$methodName}' with params: " . json_encode($params));
                call_user_func_array([$controller, $methodName], array_values($params));
                return;
            }
        }

        error_log("ERROR Router: No route matched for path '{$requestPath}' and method '{$requestMethod}'.");
        throw new NotFoundException('Page not found.');
    }

    protected function matchRoute($routePattern, $requestPath)
    {
        router_debug("matchRoute: Matching pattern '{$routePattern}' against path '{$requestPath}'");

        // Keep track of parameter names BEFORE escaping
        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $routePattern, $paramMatches);
        $paramNames = $paramMatches[1];
        router_debug("matchRoute: Captured param names: " . json_encode($paramNames));

        // Escape special regex characters but preserve parameter placeholders temporarily
        $regex = preg_quote($routePattern, '#');
        router_debug("matchRoute: Initial regex: '{$regex}'");

        // Replace optional parameters /\{name?\} with (?:/([^/]+))?
        // Note: preg_quote escapes { } to \{ \}, so we need to match the escaped version
        $regex = preg_replace('/\\\\\{([a-zA-Z0-9_]+)\\\\\?\\\\\}/', '([^/]*)', $regex);
        router_debug("matchRoute: Regex after optional param replacement: '{$regex}'");

        // Replace mandatory parameters \{name\} with ([^/]+)
        $regex = preg_replace('/\\\\\{([a-zA-Z0-9_]+)\\\\\}/', '([^/]+)', $regex);
        router_debug("matchRoute: Regex after mandatory param replacement: '{$regex}'");

        // Use '#' as the delimiter for safety
        $regex = '#^' . $regex . '$#';
        router_debug("matchRoute: Final regex: '{$regex}'");


        if (preg_match($regex, $requestPath, $matches)) {
            router_debug("matchRoute: Regex matched. Raw matches: " . json_encode($matches));
            array_shift($matches); // Remove full match
            router_debug("matchRoute: Matches after shift: " . json_encode($matches));

            $params = [];
            foreach ($paramNames as $index => $name) {
                if (isset($matches[$index]) && $matches[$index] !== '') {
                    $params[$name] = $matches[$index];
                } else {
                    $params[$name] = null;
                }
            }
            router_debug("matchRoute: Final extracted params: " . json_encode($params));
            return $params;
        }
        router_debug("matchRoute: Regex did NOT match.");

        return false;
    }
}
