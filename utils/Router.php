<?php

declare(strict_types=1);

include_once('RouterConstants.php');

class Router
{
    private static $routes = [];

    public static function get(string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        self::addRoute('GET', $path, $callback, $middlewareCallback);
    }

    public static function post(string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        self::addRoute('POST', $path, $callback, $middlewareCallback);
    }

    public static function put(string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        self::addRoute('PUT', $path, $callback, $middlewareCallback);
    }

    public static function patch(string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        self::addRoute('PATCH', $path, $callback, $middlewareCallback);
    }

    public static function delete(string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        self::addRoute('DELETE', $path, $callback, $middlewareCallback);
    }

    private static function convertPathToRegex(string $path): string
    {
        return '#^' . preg_replace_callback('#\{([^\}]+)\}#', function($matches) {
            return '([^/]+)';
        }, $path) . '$#';
    }

    private static function addRoute(string $method, string $path, callable $callback, ?callable $middlewareCallback = null): void
    {
        $compiledRegex = self::convertPathToRegex($path);
        self::$routes[$method][$compiledRegex] = [
            'callback'   => $callback,
            'middleware' => $middlewareCallback,
            'path'       => $path
        ];
    }

    public static function dispatch($request, $response): void
    {
        $method = $request->getMethod();
        $path   = $request->getPath();

        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $compiledRegex => $route) {
                if (preg_match($compiledRegex, $path, $matches)) {
                    if ($route['middleware'] && call_user_func($route['middleware'], [$request, $response])) {
                        $response->setStatusCode(401)
                            ->setHeader('Content-Type', 'application/json')
                            ->setBody(['message' => 'Unauthorized'])
                            ->send();
                        return;
                    }

                    // Construir array asociativo de parámetros
                    $params = [];
                    preg_match_all('#\{([^\}]+)\}#', $route['path'], $paramNames);
                    foreach ($paramNames[1] as $index => $name) {
                        $params[$name] = $matches[$index + 1];
                    }
                    $request->setParams($params);

                    // Llamar al callback con los parámetros
                    call_user_func_array($route['callback'], [$request, $response]);
                    return;
                }
            }
        }

        // Ruta no encontrada
        $response->setStatusCode(404)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(['message' => 'Not Found'])
            ->send();
    }
}
