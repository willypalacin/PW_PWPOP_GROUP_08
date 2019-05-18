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
use SallePW\SlimApp\Model\Database\UserRepository;
use SallePW\SlimApp\Controller\RegisterController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


final class BuyProductController {
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        $repository = $this->container->get('user_repo');
        $prod_id = $_POST["prod_id2"];
        $prod = $repository->getProductsFromDDBBbyID($prod_id);

        $user_name = $prod[0]["username"];

        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');
        $repository -> deleteProduct($prod_id);

        $products = $this->container
            ->get('home');



        $categ = $this->checkProductCategory($products);
        $images = $repository->getImagesOfProductById();
        $user = $repository->getUserByUsername($user_name);
        $user2 = $repository->getUserById($_SESSION['user_id']);


        $this->sendMailProduct($user, $prod[0]["title"], $user2->getPhoneNumber(), $user2->getUsername());




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

    public function sendMailProduct($user, $name_product, $telefono, $userInteresado){
        try {

            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $body = "<!DOCTYPE html>
                    <html lang='en'>
                        <head>
                            <title>El usuario $userInteresado esta interesado en tu producto $name_product</title>
                        </head>
                        <body>
                        <div>El usuario $userInteresado esta interesado en tu producto $name_product</div>
                            <div>Su telefono es $telefono, contacta con el</div>
                                      
                        </body>
                    </html>";

            $mail->IsSMTP();
            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = true;

            $mail->Username   = $this->container['mail_address'];
            $mail->Password   = $this->container['mail_password'];

            $mail->SetFrom($this->container['mail_address'], 'PWPOP' );
            $mail->Subject    = 'PWPOP Validation Account';
            $mail->MsgHTML($body);
            $mail->AddAddress($user->getEmail(), 'user');

            $mail->send();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }




}