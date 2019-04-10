<?php

namespace SallePW\SlimApp\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HelloController
{
    private const COOKIES_ADVICE = 'cookies_advice';

    /** @var ContainerInterface */
    private $container;

    /**
     * HelloController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $messages = $this->container
            ->get('flash')
            ->getMessage('test');

        $adviceCookie = FigRequestCookies::get($request, self::COOKIES_ADVICE);

        $isWarned = $adviceCookie->getValue();

        if (!$isWarned) {
            $response = $this->setAdviceCookie($response);
        }

        $_SESSION['counter'] = isset($_SESSION['counter']) ?
            $_SESSION['counter'] + 1 : 1;

        return $this->container->get('view')->render($response, 'index.twig', [
            'name' => $args['name'],
            'visits' => $_SESSION['counter'],
            'isWarned' => $isWarned,
            'messages' => $messages,
        ]);
    }

    private function setAdviceCookie(Response $response): Response
    {
        return FigResponseCookies::set(
            $response,
            SetCookie::create(self::COOKIES_ADVICE)
                ->withHttpOnly(true)
                ->withMaxAge(3600)
                ->withValue(1)
                ->withDomain('slimapp.test')
                ->withPath('/')
        );
    }
}
