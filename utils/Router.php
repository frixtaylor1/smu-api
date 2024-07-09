<?php

declare(strict_types=1);

include_once('RouterConstants.php');

class Route 
{
    private $middlewareCallback;
    private $callback;

    public function middleware(callable $callback): self {
        $this->middlewareCallback = $callback;
        return $this;
    }

    public function callback(callable $callback): self {
        $this->callback = $callback;
        return $this;
    }

    public function execute(Request $request, Response $response) {
        if ($this->middlewareCallback) {       
            $middlewareResult = call_user_func_array($this->middlewareCallback, [$request, $response]);
            if (!$middlewareResult) {
                return;
            }
        }
        call_user_func_array($this->callback, [$request, $response]);
    }
}

class Router
{
    private static $routes = [];

    public static function get(string $path, Route $route): void
    {
        self::addRoute('GET', $path, $route);
    }

    public static function post(string $path, Route $route): void
    {
        self::addRoute('POST', $path, $route);
    }

    public static function put(string $path, Route $route): void
    {
        self::addRoute('PUT', $path, $route);
    }

    public static function patch(string $path, Route $route): void
    {
        self::addRoute('PATCH', $path, $route);
    }

    public static function delete(string $path, Route $route): void
    {
        self::addRoute('DELETE', $path, $route);
    }

    private static function convertPathToRegex(string $path): string
    {
        return '#^' . preg_replace_callback('#\{([^\}]+)\}#', function($matches) {
            return '([^/]+)';
        }, $path) . '$#';
    }

    private static function addRoute(string $method, string $path, Route $route): void
    {
        $compiledRegex = self::convertPathToRegex($path);
        self::$routes[$method][$compiledRegex] = [
            'route' => $route,
            'path'  => $path
        ];
    }

    public static function dispatch($request, $response): void
    {
        $method = $request->getMethod();
        $path   = $request->getPath();

        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $compiledRegex => $routeData) {
                if (preg_match($compiledRegex, $path, $matches)) {
                    $route = $routeData['route'];
                    
                    $params = [];
                    preg_match_all('#\{([^\}]+)\}#', $routeData['path'], $paramNames);
                    foreach ($paramNames[1] as $index => $name) {
                        $params[$name] = $matches[$index + 1];
                    }
                    $request->setParams($params);

                    $route->execute($request, $response);
                    return;
                }
            }
        }

        $response->setStatusCode(404)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(['message' => 'Not Found'])
            ->send();
    }
}
