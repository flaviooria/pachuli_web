<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Pachuli\Web\Core\Application;

$app = new Application();
$app->run();