<?php
/**
 * Created by PhpStorm.
 * User: MB
 * Date: 2019-04-10
 * Time: 20:17
 */


namespace SallePW\SlimApp\Controller\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class SessionMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        session_start();
        return $next($request, $response);
    }
}
