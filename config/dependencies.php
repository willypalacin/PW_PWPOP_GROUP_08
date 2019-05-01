<?php

use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use \SallePW\SlimApp\Model\Product;

$container = $app->getContainer();
$numItems = 5;

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

$container['home'] = function () {
    $a = [new Product("iPhone 7", "Telefono semi-nuevo 32GB", "435", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" ), new Product("MacBook Air", "13 pulgadas 2011", "735", [],"Computers and Electronics" )];
  return $a;
};

$container['numItems'] = function ($a) {


};
