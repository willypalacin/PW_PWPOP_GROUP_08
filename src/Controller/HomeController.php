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
use SallePW\SlimApp\Model\Database\UserRepository;
use SallePW\SlimApp\Model\Product;


final class HomeController {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');

        $products = $this->container
            ->get('home');



        $categ = $this->checkProductCategory($products);
        $images = $repository->getImagesOfProductById();


        //echo $images[0]['id_product'];

        //$repository->saveProduct($products[0]);


        if(isset($_SESSION['user_id']) && strlen($repository->findUserById($_SESSION['user_id'])) && !$repository->isDeletedUser($_SESSION['user_id'])){
            return $this->container->get('view')->render($response, 'home.twig',[
                'products' => $products,
                'categ' => $categ,
                'images' => $images,
                'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
                'logged' => true,
                'validated' => $repository->isValidated($_SESSION['user_id']),
            ]);
        }else{
            return $this->container->get('view')->render($response, 'home.twig',[
                'products' => $products,
                'categ' => $categ,
                'images' => $images,
                'logged' => false,
                'validated' => true,
            ]);
        }



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
        $repository = $this->container->get('user_repo');

        $id_product = $_POST["prod_id"];
        //$id_user = $_SESSION['user_id'];
        $id_user = "guille32";
        $this->container->get('user_repo')->saveFavouriteProduct($id_user, $id_product);
        $products = $this->container
            ->get('home');



        $categ = $this->checkProductCategory($products);
        $images = $repository->getImagesOfProductById();


        echo $images[0]['id_product'];

        //$repository->saveProduct($products[0]);


        return $this->container->get('view')->render($response, 'home.twig',[

            'products' => $products,
            'categ' => $categ,
            'images' => $images

        ]);
    }


}