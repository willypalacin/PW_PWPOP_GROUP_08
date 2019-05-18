<?php
namespace SallePW\SlimApp\Controller;
use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\Database\UserRepository;
use \SallePW\SlimApp\Model\Product;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
final class FileController
{
    private const UPLOADS_DIR = __DIR__ . '/../../public/pictures';
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

        $errors = [];
        $counterImg = 111;
        $fileNames[] = "";
        $title = $_POST['title'];
        $num = $_POST['price'];
        $des = $_POST['description'];
        $cat = $_POST['cat'];
        echo $cat;
        //validacion de las imagenes
        $uploadedFiles = $request->getUploadedFiles();
        /** @var UploadedFileInterface $uploadedFile */
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $errors[] = sprintf(self::UNEXPECTED_ERROR, $uploadedFile->getClientFilename());
                continue;
            }
            $name = $uploadedFile->getClientFilename();
            $size = $uploadedFile->getSize();
            $fileInfo = pathinfo($name);
            $fileNames[$counterImg] = $fileInfo;
            $format = $fileInfo['extension'];
            $counterImg=3;
            if (!$this->isValidFormat($format) | !$this->isValidSize($size) ) {
                $errors[] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                return $this->container->get('view')->render($response, 'upload.twig', [
                    'errors' => $errors,
                    'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
                    'logged' => true,
                    'validated' => $repository->isValidated($_SESSION['user_id']),
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
                'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
                'logged' => true,
                'validated' => $repository->isValidated($_SESSION['user_id']),
            ]);
        }else {
            //todo OK, guardo a la BBDD
            //GUARDO PRODUCTE
            $repository = $this->container->get('user_repo');
            /*$p = $this->container
                ->get('home');
            */

            $p = new Product($title, $des, $num, [], $cat);
            $repository->saveProduct($p, $repository->findUserById($_SESSION['user_id']));
            /*return $this->container->get('view')->render($response, 'home.twig',[
                'products' => $p
            ]);*/
            //GUARDO IMATGE DEL PRODUCTE
            $uploadedFiles = $request->getUploadedFiles();
            foreach ($uploadedFiles['files'] as $uploadedFile) {
                $name = $uploadedFile->getClientFilename();
                $repository->saveImageProduct($name);
            }
            /*return $this->container->get('view')->render($response, 'upload.twig', [
                'errors' => $errors,
                'profile_image' => $repository->getUserById($_SESSION['user_id'])->getProfileImage(),
                'logged' => true,
                'validated' => $repository->isValidated($_SESSION['user_id']),
            ]);*/


            $p = $this->container
                ->get('home');

            $categ = $this->checkProductCategory($p);
            $images = $repository->getImagesOfProductById();

            return $this->container->get('view')->render($response, 'home.twig',[
                'products' => $p,
                 'images' => $images,
                'categ' => $categ,

            ]);
        }
    }
    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    private function isValidSize($size) : bool {

        if($size > 1000000){                                                   //Surpassed file max size defined on .twig ?
            return false;
        }
        return true;

    }
    private function validTitle(string $title): bool{
        if($title==null || $title===""){
            return false;
        }
        if(strlen($title)>5){
            return false;
        }
        if(!preg_match("/^[a-z0-9]+$/i", $title)){
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
        if(!preg_match("/^[1-9][0-9]*$/", $num)){
            return false;
        }
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
        if(!preg_match("/^[a-z0-9]+$/i", $des)){
            return false;
        }
        return true;
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
}