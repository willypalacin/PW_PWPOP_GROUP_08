<?php

namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\Database\UserRepository;
use \SallePW\SlimApp\Model\Product;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

final class UpdateBBDDController
{
    private const UPLOADS_DIR = __DIR__ . '/../../public/pictures';

    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";

    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'JPG', 'jpeg', 'JPEG'];



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
    //get de upload
    public function __invoke(Request $request, Response $response, array $args)
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');
        if(!(isset($_SESSION['user_id']) && strlen($repository->findUserById($_SESSION['user_id']))) &&
            !(isset($_COOKIE['user_id']) && strlen($repository->findUserById($_COOKIE['user_id'])))){
            http_response_code(404);
            die('Forbidden');
        }

        if(!isset($_SESSION['user_id'])){
            $_SESSION['user_id'] = $_COOKIE['user_id'];
        }

        if($repository->isDeletedUser($_SESSION['user_id'])){
            http_response_code(403);
            die('Forbidden');
        }


        $prod_id = $_POST["id_product"];
        $title = $_POST["title"];
        $des = $_POST["description"];
        $price = $_POST["price"];
        $cat = $_POST["cat"];




        var_dump($title);
        $repository = $this->container->get('user_repo');
       // $p = $repository->getProductsFromDDBBbyID($prod_id);
        $categ = $this->checkProductCategory($cat);
        $p = new Product($title,$des, $price, [], $categ);
        var_dump($p);

       // $p = new Product($p[0]['title'], $p[0]['description'], $p[0]['price'], [], $p[0]['category']);
        $repository->updateProduct($p, $prod_id);





        $products = $this->container
        ->get('home');



        $categ = $this->checkProductCategory($products);
        $images = $repository->getImagesOfProductById();


        echo $images[0]['id_product'];

        //$repository->saveProduct($products[0]);


        return $this->container->get('view')->render($response, 'home.twig',[

            'products' => $products,
            'categ' => $categ,
            'images' => $images,
            'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
            'logged' => true,
            'validated' => $repository->isValidated($_SESSION['user_id']),

        ]);

    }
    public function checkProductCategory($cat)
    {
        switch ($cat) {
            case "Sports":
                $a = 0;
                break;
            case "Fashion":
                $a = 1;
                break;
            case "Computers and electronic":
                $a = 2;
                break;
            case "Cars":
                $a = 3;
                break;
            case "Games":
                $a = 4;
                break;
            case "Home":
                $a = 5;
                break;
            case "Other":
                $a = 6;
                break;
            default:
                $a = 1;
                break;
        }
        return $a;
    }


}
