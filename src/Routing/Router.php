<?php

namespace App\Routing;

class Router
{
    private $routes = [];

    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch($requestMethod, $requestPath)
    {
        $requestMethod = strtoupper($requestMethod);
        $requestPath = $requestPath ?: '';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);
            
            if (preg_match($pattern, $requestPath, $matches)) {
                $params = array_slice($matches, 1);
                
                if (is_array($route['handler']) && count($route['handler']) === 2) {
                    $controller = new $route['handler'][0]();
                    $method = $route['handler'][1];
                    call_user_func_array([$controller, $method], $params);
                    return true;
                }
            }
        }

        return false;
    }

    private function convertPathToRegex($path)
    {
        if ($path === '') {
            return '/^$/';
        }
        
        $pattern = str_replace('/', '\/', $path);
        $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $pattern);
        
        return '/^' . $pattern . '$/';
    }
}

