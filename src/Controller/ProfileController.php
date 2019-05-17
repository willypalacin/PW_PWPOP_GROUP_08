<?php


namespace SallePW\SlimApp\Controller;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\Database\UserRepository;
use SallePW\SlimApp\Model\User;
use Slim\Http\UploadedFile;

final class ProfileController
{
    const NAME_ERROR = 'Please enter your name';
    const EMAIL_ERROR = 'Please enter your email';
    const EMAIL_VALIDATION_ERROR = 'Please introduce a valid email';
    const BIRTHDAY_ERROR = 'Please introduce a valid date';
    const BIRTHDAY_YEAR_ERROR = 'Please introduce a valid year';
    const BIRTHDAY_MONTH_ERROR = 'Please introduce a valid month';
    const BIRTHDAY_DAY_ERROR = 'Please introduce a valid day';
    const PHONE_ERROR = 'Please introduce a valid phone number';
    const PASSWORD_ERROR = 'Please introduce more than 5 characters';
    const CONFIRM_PASSWORD_ERROR = "Passwords don't match";
    const IMAGE_EXTENSION_ERROR = 'Images must be .jpg or .png';
    const IMAGE_SIZE_ERROR = 'Images size must not exceed 500Kb';
    const IMAGE_EXCEPTION_ERROR = 'Unexpected error occurs while uploading image';
    const ALPHANUMERIC_ERROR = 'Please only use alphanumeric characters';
    const REGISTER_SUCCESSFUL_MESSAGE = 'Register successful! Please validate your email';
    const DEFAULT_IMAGE_PATH = 'http://ssl.gstatic.com/accounts/ui/avatar_2x.png';

    private $container;

    /**
     * ProfileController constructor.
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
            die('Forbidden');
        }

        if($_POST == null){
            $user = $repository->getUserById($usernameId);
            return $this->container->get('view')->render($response, 'profile.twig', [
                'username' => $user->getUsername(),
                'image' => $user->getProfileImage(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhoneNumber(),
                'birthday' => $user->getBirthday(),
            ]);
        }

        $uploadedFiles = $request->getUploadedFiles();

        $user = new User($_POST);
        //Form validations
        $errors['name_error'] = $this->validateName($user->getName());
        $errors['email_error'] = $this->validateEmail($user->getEmail());
        $errors['birthday_error'] = $this->validateBirthday($user->getBirthday());
        $errors['phone_error'] = $this->validatePhone($user->getPhoneNumber());
        $errors['password_error'] = $this->validatePassword($user->getPassword());
        $errors['confirm_password_error'] = $this->validConfirmPassword($user->getPassword(),$user->getConfirmPassword());
        $errors['image_error'] = $this->validateImage($uploadedFiles['profile_image']);

        //Exists any error?
        if(strlen($errors['name_error']) != 0 || strlen($errors['email_error']) != 0 ||
            strlen($errors['birthday_error']) != 0 || strlen($errors['phone_error']) != 0 || strlen($errors['password_error']) != 0 ||
            strlen($errors['confirm_password_error']) != 0 || (strlen($errors['image_error']) != 0 && $errors['image_error'] != '0'))
            return $this->container->get('view')->render($response, 'profile.twig', [
                'name_error' => $errors['name_error'],
                'email_error' => $errors['email_error'],
                'birthday_error' => $errors['birthday_error'],
                'phone_error' => $errors['phone_error'],
                'password_error' => $errors['password_error'],
                'confirm_password_error' => $errors['confirm_password_error'],
                'image_error' => $errors['image_error'],
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'birthday' => $user->getBirthday(),
                'phone' => $user->getPhoneNumber(),
                'password' => $user->getPassword(),
                'confirm_password' => $user->getConfirmPassword(),
            ]);

        //Update other parameters
        $user->setUsername($repository->findUserById($usernameId));
        $user->setProfileImage($repository->getUserById($usernameId)->getProfileImage());
        if(strlen($user->getPassword() == 0)){
            $user->setPassword($repository->getUserById($usernameId)->getPassword());
            $user->setConfirmPassword($repository->getUserById($usernameId)->getConfirmPassword());
        }

        //Delete and Save image
        if($errors['image_error'] == ''){
            $this->deleteImage($user);
            $this->writeImage($uploadedFiles['profile_image'],$user);
        }

        //Update user
        $repository->updateUser($user);

        //Return to home
        return $this->container->get('view')->render($response, 'home.twig',[
            'products' => $products = $this->container->get('home'),
        ]);
    }


    private function validateName($name) : string {
        if($name==null || $name==""){
            return self::NAME_ERROR;
        }else if(!preg_match("/^[a-z0-9]+$/i", $name)){
            return self::ALPHANUMERIC_ERROR;
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

    private function validateBirthday($birthday) : string {
        $parts = explode('-', $birthday);
        if($birthday == "") return '';
        if(sizeof($parts) !== 3){
            return self::BIRTHDAY_ERROR;
        }else if(is_nan(intval($parts[0])) || intval($parts[0]) <= 1900 || intval($parts[0]) > date('Y')){
            return self::BIRTHDAY_YEAR_ERROR;
        }else if(is_nan(intval($parts[1])) || intval($parts[1]) <= 0 || (intval($parts[0]) >= date('Y')) && intval($parts[1]) > date('n')){
            return self::BIRTHDAY_MONTH_ERROR;
        }else if(is_nan(intval($parts[2])) || intval($parts[2]) <= 0 || (intval($parts[0]) >= date('Y')) && (intval($parts[1]) >= date('n')) &&
            (intval($parts[2]) > date('j'))){
            return self::BIRTHDAY_DAY_ERROR;
        }
        return '';
    }

    private function validatePhone($phone) : string {
        $parts = explode(' ', $phone);
        if(sizeof($parts) != 3 || is_nan(intval($parts[0])) ||
            (strlen($parts[0]) > 3 || intval($parts[0]) < 100 || intval($parts[0]) > 999) ||
            (strlen($parts[1]) > 3 || intval($parts[1]) < 100 || intval($parts[1]) > 999) ||
            (strlen($parts[2]) > 3 || intval($parts[2]) < 100 || intval($parts[2]) > 999)) return self::PHONE_ERROR;
        return '';
    }

    private function validatePassword($password) : string {
        if(strlen($password) < 6 && strlen($password) != 0) return self::PASSWORD_ERROR;
        return '';
    }

    private function validConfirmPassword($password,$confirmPassword){
        if(strcmp($password,$confirmPassword) != 0) return self::CONFIRM_PASSWORD_ERROR;
        return '';
    }

    private function validateImage(UploadedFile $uploadedFile) : string {

        if(strlen($uploadedFile->getClientFilename()) == 0) return '0';
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        if(strcmp($extension,'jpg') != 0 && strcmp($extension,'png') != 0){        //Extension error?
            return self::IMAGE_EXTENSION_ERROR;
        }else if($uploadedFile->getError() === UPLOAD_ERR_FORM_SIZE){                                                   //Surpassed file max size defined on .twig ?
            return self::IMAGE_SIZE_ERROR;
        }
        return '';

    }

    private function deleteImage(User $user){
        if(strcmp($user->getProfileImage(),$this->container->get('default_image')) == 0) return;
        unlink($this->container->get('upload_directory') . DIRECTORY_SEPARATOR . $user->getProfileImage());
    }

    private function writeImage(UploadedFile $uploadedFile,User $user){
        try {
            $filename = sprintf('%s.%0.8s',bin2hex(random_bytes(8)),pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION)); //Random filename
            $user->setProfileImage($filename);
            if(strcmp($user->getProfileImage(),$this->container->get('default_image')) == 0) return;
            $uploadedFile->moveTo($this->container->get('upload_directory') . DIRECTORY_SEPARATOR . $filename);      //Write image on ./uploads                                                                       //Relate user with their own images
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}