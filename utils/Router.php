<?php

declare(strict_types=1);

use SMU\Request as Request;
use SMU\Response as Response;

include_once('RouterConstants.php');
include_once('Route.php');

class Router
{
    private static $routes = [];

    public static function get(Route $route): void
    {
        self::addRoute('GET', $route);
    }

    public static function post(Route $route): void
    {
        self::addRoute('POST', $route);
    }

    public static function put(Route $route): void
    {
        self::addRoute('PUT', $route);
    }

    public static function patch(Route $route): void
    {
        self::addRoute('PATCH', $route);
    }

    public static function delete(Route $route): void
    {
        self::addRoute('DELETE', $route);
    }

    private static function convertPathToRegex(string $path): string
    {
        return '#^' . preg_replace_callback('#\{([^\}]+)\}#', function ($matches) {
            return '([^/]+)';
        }, $path) . '$#';
    }

    private static function addRoute(string $method, Route $route): void
    {
        $compiledRegex = self::convertPathToRegex($route->getPath());
        self::$routes[$method][$compiledRegex] = [$route];
    }

    public static function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $path   = $request->getPath();

        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $compiledRegex => $routeData) {
                if (preg_match($compiledRegex, $path, $matches)) {
                    $route = $routeData[0];

                    $params = [];
                    preg_match_all('#\{([^\}]+)\}#', $route->getPath(), $paramNames);
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
