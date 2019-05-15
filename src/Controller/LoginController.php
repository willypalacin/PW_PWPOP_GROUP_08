<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\Database\UserRepository;
use SallePW\SlimApp\Model\User;

final class LoginController
{

    const USERNAME_ERROR = 'Please enter your username';
    const USERNAME_MAX_CHARACTERS_ERROR = 'Maximum 20 characters. Offset of ';
    const EMAIL_ERROR = 'Please enter your email';
    const EMAIL_VALIDATION_ERROR = 'Please introduce a valid email';
    const PASSWORD_ERROR = 'Please introduce more than 5 characters';
    const ALPHANUMERIC_ERROR = 'Please only use alphanumeric characters';
    const USER_NOT_FOUND_ERROR = 'User not found';

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
        if ($_POST == null) return $this->container->get('view')->render($response, 'login.twig');

        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');

        $user = new User(null);
        $user->setEmail($_POST['email_address']);
        $user->setUsername($_POST['email_address']);
        $user->setPassword($_POST['password']);

        //Login validations
        $errors['username_error'] = $this->validateUsername($user->getUsername());
        $errors['email_error'] = $this->validateEmail($user->getEmail());
        $errors['password_error'] = $this->validatePassword($user->getPassword());


        //Exists any error?
        if ((strlen($errors['email_error']) != 0 && strlen($errors['username_error']) != 0) || strlen($errors['password_error']) != 0)
            return $this->container->get('view')->render($response, 'login.twig', [
                'email_error' => $errors['email_error'] . "\n" . $errors['username_error'] ,
                'password_error' => $errors['password_error'],
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
            ]);

        if(strlen($errors['email_error']) == 0){
            //Search for existing user in db
            $user->setUsername($repository->findUserByLoginEmail($user->getEmail(), $user->getPassword()));
            if ($user->getUsername() == '') {
                return $this->container->get('view')->render($response, 'login.twig', [
                    'error' => self::USER_NOT_FOUND_ERROR,
                    'email' => $user->getEmail(),
                    'password' => $user->getPassword(),
                ]);
            }
        }else{
            //Search for existing user in db
            if(!$repository->findUserByLoginUser($user->getUsername(), $user->getPassword())){
                return $this->container->get('view')->render($response, 'login.twig', [
                    'error' => self::USER_NOT_FOUND_ERROR,
                    'email' => $user->getEmail(),
                    'password' => $user->getPassword(),
                ]);
            }
        }
        $_SESSION['user_id'] = md5($user->getUsername());
        return $this->container->get('view')->render($response, 'home.twig',[
            'products' => $products = $this->container->get('home'),
        ]);
    }

    private function validateUsername($username) : string {
        if($username==null || $username==""){
            return self::USERNAME_ERROR;
        }else if(!preg_match("/^[a-z0-9]+$/i", $username)){
            return self::ALPHANUMERIC_ERROR;
        }else if(strlen($username) > 20){
            strlen($username) - 20 == 1 ? $return = self::USERNAME_MAX_CHARACTERS_ERROR . '1 character' :
                $return = self::USERNAME_MAX_CHARACTERS_ERROR . strlen($username) - 20 . ' characters';
            return $return;
        }
        return '';
    }

    private function validateEmail($email) : string {
        if($email==null || $email==""){
            return self::EMAIL_ERROR;
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return self::EMAIL_VALIDATION_ERROR;
        }
        return '';
    }

    private function validatePassword($password) : string {
        if(strlen($password) < 6) return self::PASSWORD_ERROR;
        return '';
    }

}