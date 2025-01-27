<?php

define('ROOT', dirname(__DIR__));

require ROOT . '/vendor/autoload.php';

use Pachuli\Web\Http\Router;

$uri = $_SERVER['REQUEST_URI'];

if ($uri === '/') {
    echo 'Home';
    exit;
}

$uri = trim($uri, '/');
$uriParser = parse_url($uri, PHP_URL_PATH);
$segments = explode('/', $uriParser);

//print_r($segments);

$controller = $segments[0] ?? null;
$method = $segments[1] ?? null;

if ($controller === null) {
    echo '404';
    exit;
}

$className = ucfirst($controller) . 'Controller';
$class = "App\\Controllers\\" . ucfirst($controller) . "\\$className";

if (!class_exists($class)) {

    $viewFile = !is_null($method) && !is_numeric($method) ? ROOT . "/app/views/$controller/$method.php" : ROOT . "/app/views/$controller.php";

    if (file_exists($viewFile)) {
        require_once $viewFile;
        exit;
    }
} else {
    if (!is_null($method) && !is_numeric($method)) {

        try {
            $methodReflection = new ReflectionMethod($class, $method);

            if ($methodReflection->isStatic()) {
                call_user_func([$class, $method]);
                return;
            }

        } catch (ReflectionException $e) {
            echo '404';
            exit;
        }

        call_user_func([new $class, $method]);
    } else {
        echo '404';
    }
}

Router::get("/profile/{id?}", [\App\Controllers\Profiles\ProfilesController::class, "index"]);
Router::get("/profile/edit/{id}", [\App\Controllers\Profiles\ProfilesController::class, "edit"]);
Router::dispatcher();

//$route = '/profile/{id?}';
//$pattern = null;
//
//// Reemplazar {id?} con (?:\/(\d+))? para hacerlo opcional
//if (str_contains($route, '{id?}')) {
//    // Eliminar la barra antes de {id?} y reemplazar {id?} con (?:\/(\d+))?
//    $pattern = preg_replace('/\/\{id\?}/', '(?:\/(?P<id>\d+))?', $route);
//}
//// Reemplazar {id} con (\d+) para hacerlo obligatorio
//elseif (str_contains($route, '{id}')) {
//    $pattern = preg_replace('/\{id}/', '(?P<id>\d+)', $route);
//}
//
//$patternReplaced = null;
//
//if ($pattern) {
//    $patternReplaced = '#^' . $pattern . '$#';
//}
//
//var_dump($patternReplaced);
//$fullUri = rtrim($_SERVER['REQUEST_URI'], '/');
//
//if ($patternReplaced && preg_match($patternReplaced, $fullUri, $matches)) {
//    var_dump($matches);
//}