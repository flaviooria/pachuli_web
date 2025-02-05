<?php

use App\controllers\home\HomeController;
use App\controllers\profiles\ProfilesController;
use Pachuli\Web\Http\Router;

$router = new Router();
$router->get("/", "index");
$router->get("/profile", [ProfilesController::class, "index"]);
$router->get("/profile/edit/{id:\d+}", [ProfilesController::class, "edit"]);
$router->get("/home", [HomeController::class, "index"]);