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
use Slim\Http\Response as Response;
use SallePW\SlimApp\Model\Product;


final class HomeCorController {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {




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

    public function refresh(Request $request, Response $response) {



        $data = $request->getParsedBody();

        $products = $this->container
            ->get('home');
        $size = count($products);

        $repository = $this->container->get('user_repo');
        $id_product = $data["id_product"];
        $id_user = $_SESSION['user_id'];
        $id_user = $repository->findUserById($id_user);
        $this->container->get('user_repo')->saveFavouriteProduct($id_user, $id_product);


        return $response->withJson([], 200);

    }


}