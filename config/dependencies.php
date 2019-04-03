<?php

$container = $app->getContainer();

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../templates', [
        'cache' => false
    ]);

    $router = $c->get('router');

    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));

    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};
