<?php

namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
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
