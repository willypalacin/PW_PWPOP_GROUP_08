<?php


namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\Database\UserRepository;

final class DeleteAccountController
{
    const SUCCESSFUL_DELETED_ACCOUNT_IMAGE = 'Account successfully deleted';

    private $container;

    /**
     * DeleteAccountController constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');
        if(!(isset($_SESSION['user_id']) && strlen($repository->findUserById($_SESSION['user_id']))) &&
            !(isset($_COOKIE['user_id']) && strlen($repository->findUserById($_COOKIE['user_id'])))){
            http_response_code(403);
            die('Forbidden');
        }

        if(isset($_SESSION['user_id'])){
            $usernameId = $_SESSION['user_id'];
        }else{
            $usernameId = $_COOKIE['user_id'];
        }

        if(!$repository->isValidated($usernameId)){
            http_response_code(403);
            die('Please, accept validation mail in order to delete account');
        }

        $repository->deleteUser($usernameId);

        //Go to login
        return $this->container->get('view')->render($response, 'login.twig', [
            'message' =>self::SUCCESSFUL_DELETED_ACCOUNT_IMAGE,
        ]);
    }

}