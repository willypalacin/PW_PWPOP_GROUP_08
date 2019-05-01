<?php
/**
 * Created by PhpStorm.
 * User: MB
 * Date: 2019-04-18
 * Time: 12:39
 */

namespace SallePW\SlimApp\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Model\Product;

final class SearchController {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        $title = $_POST['title'];
        $products = $this->container
            ->get('home');

        $products2 = [];
        for ($i = 1; $i <= sizeof($products); $i++) {


            if( strpos($products[$i]->getTitle(), $title) == true ) {
                array_push($products2, $products[$i]);
            }
        }



        return $this->container->get('view')->render($response, 'home.twig',[

            'products' => $products2

        ]);

    }


}