<?php

namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\Database\UserRepository;
use \SallePW\SlimApp\Model\Product;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

final class UploadProductController
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
        $repository = $this->container->get('user_repo');
        $p = $repository->getProductsFromDDBBbyID($prod_id);
        $categ = $this->checkProductCategory($p[0]['category']);
        $p = new Product($p[0]['title'], $p[0]['description'], $p[0]['price'], [], $p[0]['category']);

        return $this->container->get('view')->render($response, 'uploadProduct.twig', [
            'title' => $p->getTitle(),
            'id_product' => $prod_id,
            'price' => $p->getPrice(),
            'cat' => $categ,
            'description' => $p->getDescription(),
            'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
            'logged' => true,
            'validated' => $repository->isValidated($_SESSION['user_id']),
        ]);
    }
    public function checkProductCategory($cat) {


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

    public function update(Request $request, Response $response): Response {
        echo "hola amiga";


    }


}
