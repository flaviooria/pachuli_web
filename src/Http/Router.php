<?php declare(strict_types=1);

namespace Pachuli\Web\Http;

use Exception;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class Router
{

    protected array $routes = [];
    protected RouteCollector $collector;

    public function __construct()
    {
        $this->collector = new RouteCollector(new Std(), new GroupCountBased());
    }

    public function view(string $nameView): void
    {
        $path = str_replace('.', '/', $nameView);

        if ($path === trim($_SERVER['REQUEST_URI'], '/')) {

            $this->addRoute("GET", "/" . $path);

            $viewParts = explode('.', $nameView);

            if (count($viewParts) === 1) {
                require_once ROOT . "/app/views/$nameView.php";
            }

            $parentView = ROOT . "/app/views/";
            foreach ($viewParts as $part) {
                $parentView .= "$part/";
            }

            $viewPath = rtrim($parentView, '/');
            $viewPath .= '.php';

            if (file_exists($viewPath)) {
                require_once $viewPath;
            }
        }
    }

    public function get(string $route, mixed $handler = null): void
    {
        try {
            self::addRoute("GET", $route, $handler);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    private function addRoute(string $httpMethod, string $route, mixed $handler = null): void
    {
        $this->routes[$httpMethod][] = [
            'route' => $route,
            'handler' => $handler
        ];
    }

    public function post(string $route, mixed $handler = null): void
    {
        try {
            self::addRoute("POST", $route, $handler);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function dispatcher(): void
    {

        foreach ($this->routes as $httpMethod => $routeInfo) {
            foreach ($routeInfo as $route) {
                $this->collector->addRoute($httpMethod, $route['route'], $route['handler']);
            }
        }

        $dispatcher = new Dispatcher\GroupCountBased($this->collector->processedRoutes());
        $routeInfo = $dispatcher->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER['REQUEST_URI']);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
//                echo 'Error 404: Página no encontrada';
                http_response_code(404);
                require_once __DIR__ . '/_404.php';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
//                $allowedMethods = $routeInfo[1];
                echo 'Error 405: Método no permitido';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1]; // Callback o controlador
                $vars = $routeInfo[2]; // Parámetros de la ruta
                self::handleCallback($handler, $vars);
                break;
        }
    }

    protected static function handleCallback(string|callable|array|null $callable, array $params = []): void
    {
        if (is_callable($callable)) {
            if ($params) {
                call_user_func_array($callable, $params);
                return;
            }

            call_user_func($callable);
            return;
        }

        if (is_string($callable)) {
            require_once ROOT . "/app/views/$callable.php";
        }

        if (is_array($callable)) {
            $class = $callable[0];
            $method = $callable[1];
            if (!is_null($method) && !is_numeric($method)) {
                try {
                    $methodReflection = new \ReflectionMethod($class, $method);
                    $callables = $methodReflection->isStatic() ? [$class, $method] : [new $class, $method];
                    if ($params) {
                        call_user_func_array($callables, $params);
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
}