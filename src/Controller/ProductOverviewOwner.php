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


final class ProductOverviewOwner {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
       $prod_id = $_POST["prod_id"];

        $repository = $this->container->get('user_repo');

        $products = $repository->getProductsFromDDBBbyID($prod_id);
        $images = $repository->getImagesOfProductById();


        return $this->container->get('view')->render($response, 'overviewowner.twig',[

            'product' => $products[0],
            'images' => $images,


        ]);

    }

    public function checkProductCategory($products) {
        $p = [];

        for ($i = 0; $i < count($products); $i++) {
            $prod = $products[$i];

            switch ($prod['category']){
                case 0:
                    $prod[$i] = "Sports";
                    break;
                case 1:
                    $prod[$i] = "Fashion";
                    break;
                case 2:
                    $prod[$i] = "Computers and electronic";
                    break;
                case 3:
                    $prod[$i] = "Cars";
                    break;
                case 4:
                    $prod[$i] ="Games";
                    break;
                case 5:
                    $prod[$i] = "Home";
                    break;
                case 6:
                    $prod[$i] = "Other";
                    break;


            }
        }
        return $p;

    }



    public function uploadAction(Request $request, Response $response): Response {
        $prod_id = $_POST["id_product"];
        $repository = $this->container->get('user_repo');
        $p = $repository->getProductsFromDDBBbyID($prod_id);
        $categ = $this->checkkProductCategory($p[0]['category']);
        $p = new Product($p[0]['title'], $p[0]['description'], $p[0]['price'], [], $p[0]['category']);

        return $this->container->get('view')->render($response, 'uploadProduct.twig', [
            'title' => $p->getTitle(),
            'price' => $p->getPrice(),
            'cat' => $categ,
            'description' => $p->getDescription(),
        ]);


    }


    public function checkkProductCategory($cat) {


        echo $cat;
        switch ($cat){
            case 0:
                $p= "Sports";
                break;
            case 1:
                $p = "Fashion";
                break;
            case 2:
                $p= "Computers and electronic";
                break;
            case 3:
                $p = "Cars";
                break;
            case 4:
                $p ="Games";
                break;
            case 5:
                $p = "Home";
                break;
            case 6:
                $p= "Other";
                break;


        }

        return $p;

    }



}