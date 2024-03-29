<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\Database\UserRepository;

final class AccountValidationController
{

    const ID_MISMATCH_ERROR = "ERROR! ID do not match any user";
    const ID_MISSING_ERROR = "ERROR! ID has not been specified";
    const VALIDATION_ACCOUNT_SUCCESS_MESSAGE = "Successfuly validated mail";

    private $container;

    /**
     * RegisterController constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        if(!empty($_GET['id'])){
            /** @var UserRepository $repository */
            $repository = $this->container->get('user_repo');
            $username = $repository->findUserById($_GET['id']);
            if(!strlen($username)){
                echo self::ID_MISMATCH_ERROR;
            }else{
                $repository->validateAccount($username);
                return $this->container->get('view')->render($response, 'login.twig', [
                    'message' =>self::VALIDATION_ACCOUNT_SUCCESS_MESSAGE,
                ]);
            }
        }else{
            echo self::ID_MISSING_ERROR;
        }
        return null;
    }
}