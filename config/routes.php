<?php

use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HelloController;
use SallePW\SlimApp\Controller\Middleware\TestMiddleware;
use SallePW\SlimApp\Controller\Middleware\SessionMiddleware;

$app
    ->get('/hello/{name}', HelloController::class)
    ->add(TestMiddleware::class);

$app->get('/flash', FlashController::class);

$app->add(SessionMiddleware::class);
