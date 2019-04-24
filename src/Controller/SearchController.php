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

        $products = $this->container
            ->get('search');


        $names = [];
        $descriptions = [];
        $prices = [];
        $categories = [];
        $i = 0;

        foreach ($products as &$product) {
            $names[$i] = $product -> getTitle();
            $descriptions[$i] = $product->getDescription();
            $prices[$i] = $product ->getPrice();
            $categories[$i] = $product-> getCategory();
            $i = $i + 1;
        }

        return $this->container->get('view')->render($response, 'search.twig',[
            'names' => $names,
            'descriptions' => $descriptions,
            'prices' => $prices,
            '$categories' => $categories,
        ]);

    }


}