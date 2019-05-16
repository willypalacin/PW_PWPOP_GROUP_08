<?php

namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use \SallePW\SlimApp\Model\Product;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

final class FileController
{
    private const UPLOADS_DIR = __DIR__ . '/../../pictures';

    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";

    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'JPG'];



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
    public function indexAction(Request $request, Response $response): Response
    {
        return $this->container->get('view')->render($response, 'upload.twig', []);
    }



    //post del form de upload
    public function uploadAction(Request $request, Response $response): Response {
        $errors = [];
        $counterImg = 111;
        $fileNames[] = "";

        $title = $_POST['title'];
        $num = $_POST['price'];
        $des = $_POST['description'];
        $cat = $_POST['cat'];


        //validacion de las imagenes
        $uploadedFiles = $request->getUploadedFiles();
        /** @var UploadedFileInterface $uploadedFile */
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $errors[] = sprintf(self::UNEXPECTED_ERROR, $uploadedFile->getClientFilename());
                continue;
            }

            $name = $uploadedFile->getClientFilename();

            $fileInfo = pathinfo($name);
            $fileNames[$counterImg] = $fileInfo;

            $format = $fileInfo['extension'];
            $counterImg=3;

            if (!$this->isValidFormat($format)) {
                $errors[] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                return $this->container->get('view')->render($response, 'upload.twig', [
                    'errors' => $errors,
                ]);
                //continue;
            }

            //We generate a custom name here instead of using the one coming form the form
            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name);



        }

        if(!$this->validTitle($title)| !$this->validNumber($num) | !$this->validDescription($des)){
            $errors[] = "Something was wrong with your info, please try again!";
            return $this->container->get('view')->render($response, 'upload.twig', [
                'errors' => $errors,
            ]);
        }else {
            //todo OK, guardo a la BBDD


            //GUARDO PRODUCTE
            $repository = $this->container->get('user_repo');
            /*$p = $this->container
                ->get('home');
            */


            $p = new Product($title, $des, $num, [], $cat);

            $repository->saveProduct($p);

            /*return $this->container->get('view')->render($response, 'home.twig',[

                'products' => $p

            ]);*/

            //GUARDO IMATGE DEL PRODUCTE


            $uploadedFiles = $request->getUploadedFiles();
            foreach ($uploadedFiles['files'] as $uploadedFile) {
                $name = $uploadedFile->getClientFilename();
                $repository->saveImageProduct($name);
            }


            return $this->container->get('view')->render($response, 'upload.twig', [
                'errors' => $errors,
            ]);

        }









    }











    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }


    private function validTitle(string $title): bool{
        if($title==null || $title==="blanca"){
            return false;
        }
        if(strlen($title)>10){
            return false;
        }
        return true;
    }
    private function validNumber(string $num): bool{

        if($num==null || $num=="") {

            return false;
        }
        //if(isNaN($num)){
          //  return false;
        //}else{
            if($num <= 0){
                return false;
            }
        //}
        return true;
    }
    private function validDescription(string $des) :bool{
        if($des==null || $des==="") {
            return false;
        }
        if(strlen($des) < 20) {
            return false;
        }
        if(strlen($des) > 20 && strlen($des) < 100) {
            return true;
        }
        if(strlen($des) > 100) {
            return false;
        }
        return true;
    }

}
