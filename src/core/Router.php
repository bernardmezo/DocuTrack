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
    }

    public function dispatch($requestPath, $requestMethod)
    {
        foreach ($this->routes as $routePattern => $routeConfig) {
            $params = $this->matchRoute($routePattern, $requestPath);

            if ($params !== false) {
                if (isset($routeConfig['methods']) && !in_array($requestMethod, $routeConfig['methods'])) {
                    continue;
                }

                if (isset($routeConfig['middleware'])) {
                    foreach ($routeConfig['middleware'] as $middlewareClass) {
                        $middlewareFQN = "App\\Middleware\\{$middlewareClass}";
                        if (method_exists($middlewareFQN, 'check')) {
                            $middlewareFQN::check();
                        }
                    }
                }

                $controllerName = "App\\Controllers\\{$routeConfig['controller']}";
                $methodName = $routeConfig['method'];

                if (!class_exists($controllerName)) {
                     throw new NotFoundException("Controller $controllerName not found");
                }

                $controller = new $controllerName($this->db);

                if (!method_exists($controller, $methodName)) {
                     throw new NotFoundException("Method $methodName not found in $controllerName");
                }

                call_user_func_array([$controller, $methodName], array_values($params));
                return;
            }
        }

        throw new NotFoundException('Page not found.');
    }

    protected function matchRoute($routePattern, $requestPath)
    {
        $regex = preg_quote($routePattern, '#');

        // Keep track of parameter names
        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $routePattern, $paramMatches);
        $paramNames = $paramMatches[1];

        // Replace optional parameters /{name?} with (?:/([a-zA-Z0-9_-]+))?
        $regex = preg_replace('/\/{([a-zA-Z0-9_]+)\?\}/', '(?:/([a-zA-Z0-9_-]+))?', $regex);

        // Replace mandatory parameters {name} with ([a-zA-Z0-9_-]+)
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $regex);

        // Use '#' as the delimiter for safety
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $requestPath, $matches)) {
            array_shift($matches); // Remove full match

            $params = [];
            foreach ($paramNames as $index => $name) {
                if (isset($matches[$index]) && $matches[$index] !== '') {
                    $params[$name] = $matches[$index];
                } else {
                    $params[$name] = null;
                }
            }

            return $params;
        }

        return false;
    }
}
