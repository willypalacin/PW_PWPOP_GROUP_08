<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'index.twig', [
        'name' => $args['name']
    ]);
});
