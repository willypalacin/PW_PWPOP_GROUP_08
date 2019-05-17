<?php

namespace SallePW\SlimApp\Controller\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TestMiddleware
{
    public function __invoke(Request $request, Response $response, callable $nextMiddleware)
    {
        $response->getBody()->write('Before');

        /** @var Response $response */
        $response = $nextMiddleware($request, $response);

        $response->getBody()->write('After');

        return $response;
    }
}
