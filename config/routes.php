<?php

use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HelloController;
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\UploadController;
use SallePW\SlimApp\Controller\Middleware\TestMiddleware;
use SallePW\SlimApp\Controller\Middleware\SessionMiddleware;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\FileController;

$app
    ->get('/hello/{name}', HelloController::class)
    ->add(TestMiddleware::class);

$app->get('/flash', FlashController::class);

$app->add(SessionMiddleware::class);

$app->get('/home', HomeController::class)
    ->add(HomeController::class);

$app->get('/upload', UploadController::class)
    ->add(UploadController::class);

$app->post('/search', SearchController::class);

//$app->get('/files', FileController::class . ':formAction');

$app->get('/files', FileController::class . ':indexAction');

$app->post('/upload', FileController::class . ':uploadAction')
    ->setName('upload');

