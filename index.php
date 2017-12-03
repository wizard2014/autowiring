<?php

use App\Container\Container;
use App\Controllers\HomeController;

require_once __DIR__ . '/vendor/autoload.php';

$container = new Container;

dump($container->get(HomeController::class)->index());
