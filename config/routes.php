<?php

use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HelloController;
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\UploadController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\Middleware\TestMiddleware;
use SallePW\SlimApp\Controller\Middleware\SessionMiddleware;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\FileController;
use SallePW\SlimApp\Controller\FavouritesController;
use SallePW\SlimApp\Controller\AccountValidationController;
use SallePW\SlimApp\Controller\LoginController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\MyProductController;
use SallePW\SlimApp\Controller\DeleteAccountController;
use SallePW\SlimApp\Controller\ProductOverviewOwner;
use SallePW\SlimApp\Controller\UploadProductController;
use SallePW\SlimApp\Controller\UpdateBBDDController;

$app
    ->get('/hello/{name}', HelloController::class)
    ->add(TestMiddleware::class);

$app->get('/flash', FlashController::class);

$app->add(SessionMiddleware::class);

$app->get('/home', HomeController::class);
$app->post('/home', HomeController::class . ':refresh');


$app->get('/register',RegisterController::class);
$app->post('/register',RegisterController::class);
$app->get('/account-validation/', AccountValidationController::class);
$app->get('/login', LoginController::class);
$app->post('/login',LoginController::class);

$app->get('/upload', UploadController::class)
    ->add(UploadController::class);

$app->post('/favourites', FavouritesController::class);
$app->get('/favourites', FavouritesController::class);


$app->get('/myproduct', MyProductController::class);





$app->post('/myproduct', ProductOverviewOwner::class)
    ->setName('myproduct');



$app->post('/overview', UploadProductController::class)
    ->setName('overview');



$app->post('/uploadBBDD', UpdateBBDDController::class)
    ->setName('uploadBBDD');







/*

$app->get('/uploadProduct', UploadProductController::class)
    ->setName('uploadProduct');


$app->post('/uploadProduct', ProductOverviewOwner::class . ':uploadAction')
    ->setName('owner');

$app->post('/uploadProduct', UploadProductController::class . ':update')
    ->setName('update');*/

$app->post('/search', SearchController::class);

//$app->get('/files', FileController::class . ':formAction');

$app->get('/files', FileController::class . ':indexAction');

$app->post('/upload', FileController::class . ':uploadAction')
    ->setName('uploadd');



$app->get('/profile', ProfileController::class);
$app->post('/profile', ProfileController::class);

$app->get('/delete-account',DeleteAccountController::class);

