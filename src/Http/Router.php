<?php

namespace Pachuli\Web\Http;

use Exception;
use ReflectionMethod;

class Router
{

    protected static array $routes = [];

    public static function get(string $route, mixed $handler = null): void
    {
        try {
            $patternRoute = self::convertRouteToRegex($route);

            self::addRoute($patternRoute, $handler);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * @throws Exception if route is not valid
     */
    private static function convertRouteToRegex(string $route): string
    {
        $pattern = null;

        // Reemplazar {id?} con (\d+)? para hacerlo opcional
        if (str_contains($route, '{id?}')) {
            // Eliminar la barra antes de {id?} y reemplazar {id?} con (?:\/(\d+))?
            $pattern = preg_replace('/\/\{id\?}/', '(?:\/(?P<id>\d+))?', $route);
        }
        // Reemplazar {id} con (\d+) para hacerlo obligatorio
        elseif (str_contains($route, '{id}')) {
            $pattern = preg_replace('/\{id}/', '(?P<id>\d+)', $route);
        }

        if ($pattern) return '#^' . $pattern . '$#';

        throw new Exception('Route not valid');
    }

    private static function addRoute(string $route, mixed $handler = null): void
    {

        self::$routes[$route] = $handler;
    }

    public static function dispatcher(): void
    {

        $url = rtrim($_SERVER['REQUEST_URI'], '/');

        foreach (self::$routes as $pattern => $handler) {
            if (preg_match($pattern, $url, $matches)) {

                $param = $matches['id'] ?? null;
                $class = $handler[0];
                $method = $handler[1] ?? null;

                self::handleCallback($class, $method, $param);
                break;
            }
        }

        http_response_code(404);
        require_once ROOT . "/src/Http/_404.php";
    }

    private static function handleCallback($class, $method, $param = null): void
    {
        if (!is_null($method) && !is_numeric($method)) {

            try {
                $methodReflection = new ReflectionMethod($class, $method);

                $callables = $methodReflection->isStatic() ? [$class, $method] : [new $class, $method];

                if ($param) {
                    call_user_func_array($callables, [$param]);
                    return;
                }

                call_user_func($callables);

            } catch (\ReflectionException $e) {
                echo '404';
                exit;
            }


        }
    }
}