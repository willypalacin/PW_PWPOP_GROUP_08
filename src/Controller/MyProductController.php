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


final class MyProductController {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        $repository = $this->container->get('user_repo');

        /*$products = $this->container
            ->get('home');*/
        $products = $repository->getProductByUsername($_SESSION['user_id']);
        $categ = $this->checkProductCategory($products);
        $images = $repository->getImagesOfProductById();

        //echo $images[0]['id_product'];

        //$repository->saveProduct($products[0]);


        return $this->container->get('view')->render($response, 'myproduct.twig',[

            'products' => $products,
            'categ' => $categ,
            'images' => $images

        ]);
/*
        $repository = $this->container->get('user_repo');

        $products = $this->container
            ->get('myproducts');


        $titles = $this->seaechTitles($products);
        for ($i = 0; $i < count($titles); $i++) {
            $ti[$i] = $this->searchImg($titles[$i], $products);
        }
        var_dump($ti);


        //var_dump($products);

        return $this->container->get('view')->render($response, 'myproduct.twig',[

            'title' => $titles,

            'imgSegonsTitle' => $ti,

        ]);



        //return $this->container->get('view')->render($response, 'myproduct.twig',[]);
*/

    }
    /*public function searchImg($titles, $pro) {
        $imgT = [];
        for ($j = 0; $j < count($pro); $j++) {
            $p = $pro[$j];
                if ($titles == $p['title']) {
                array_push($imgT, $p['product_image']);
            }

        }
        return $imgT;

    }
    public function seaechTitles($products){
        $t = [];

        for($x = 0; $x < count($products); $x++){
            $prod = $products[$x];
            if(!$this->checkIfExist($prod['title'], $t, $x)){
                array_push($t,$prod['title']);
            }

        }
        return $t;
    }
    public function checkIfExist($tit, $arr, $fi){

        for($j = 0; $j < $fi; $j++){
            $aux = $arr[$j];
            if($aux == $tit){
                return true;
            }


        }
        return false;
    }

*/

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