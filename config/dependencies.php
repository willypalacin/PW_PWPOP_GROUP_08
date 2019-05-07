<?php

use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use \SallePW\SlimApp\Model\Product;
use SallePW\SlimApp\Model\Database\Database;
use SallePW\SlimApp\Model\Database\UserRepository;
use Slim\Container;

$container = $app->getContainer();

$container['upload_directory'] = __DIR__ . '/../uploads';

$container['view'] = function ($c) {
    $view = new Twig(__DIR__ . '/../templates', [
        'cache' => false,
    ]);

    $router = $c->get('router');

    $uri = Uri::createFromEnvironment(new Environment($_SERVER));

    $view->addExtension(new TwigExtension($router, $uri));

    return $view;
};

$container['flash'] = function () {
    return new Messages();
};

$container['db'] = function (Container $c) {
    return Database::getInstance(
        $c['settings']['db']['username'],
        $c['settings']['db']['password'],
        $c['settings']['db']['host'],
        $c['settings']['db']['dbName']
    );
};

$container['mail_address'] = function(Container $c){
    return $c['settings']['mail']['address'];
};

$container['mail_password'] = function(Container $c){
    return $c['settings']['mail']['password'];
};

$container['user_repo'] = function (Container $c) {
    return new UserRepository($c->get('db'));
};


$container['search'] = function () {
    $a = [new Product("iPhone 7", "Telefono semi-nuevo 32GB", "435", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" )];
  return $a;
};
