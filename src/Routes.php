<?php

use Vaida\CargoPuzzleMaster\Core\Router;
use Vaida\CargoPuzzleMaster\Controllers\ContainerController;

$router = new Router();
$router->addRoute('/', ContainerController::class, 'index');
$router->addRoute('/calculate', ContainerController::class, 'calculate');

$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);