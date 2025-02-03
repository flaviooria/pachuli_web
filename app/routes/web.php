<?php

use App\controllers\profiles\ProfilesController;
use Pachuli\Web\Http\Router;

$router = new Router();
$router->get("/profile", [ProfilesController::class, "index"]);
$router->get("/profile/edit/{id:\d+}", [ProfilesController::class, "edit"]);
$router->get("/create_user", "create_user");