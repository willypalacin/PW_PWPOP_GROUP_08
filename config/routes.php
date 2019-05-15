<?php

use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HelloController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\UploadController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\Middleware\TestMiddleware;
use SallePW\SlimApp\Controller\Middleware\SessionMiddleware;
use SallePW\SlimApp\Controller\AccountValidationController;
use SallePW\SlimApp\Controller\LoginController;

$app
    ->get('/hello/{name}', HelloController::class)
    ->add(TestMiddleware::class);

$app->get('/flash', FlashController::class);

$app->add(SessionMiddleware::class);

$app->get('/home', SearchController::class)
    ->add(SearchController::class);

$app->get('/register',RegisterController::class);
$app->post('/register',RegisterController::class);
$app->get('/account-validation/', AccountValidationController::class);
$app->get('/login', LoginController::class);
$app->post('/login',LoginController::class);

$app->get('/upload', UploadController::class)
    ->add(UploadController::class);