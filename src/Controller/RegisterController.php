<?php


namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\Database\UserRepository;
use SallePW\SlimApp\Model\User;
use Slim\Http\UploadedFile;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class RegisterController
{
    const NAME_ERROR = 'Please enter your name';
    const USERNAME_ERROR = 'Please enter your username';
    const USERNAME_MAX_CHARACTERS_ERROR = 'Maximum 20 characters. Offset of ';
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
        if($_POST == null) return $this->container->get('view')->render($response, 'register.twig');

        /** @var UserRepository $repository */
        $repository = $this->container->get('user_repo');

        $uploadedFiles = $request->getUploadedFiles();

        $user = new User($_POST);
        $filenames = array();
        //Form validations
        $errors['name_error'] = $this->validateName($user->getName());
        $errors['username_error'] = $this->validateUsername($user->getUsername());
        $errors['email_error'] = $this->validateEmail($user->getEmail());
        $errors['birthday_error'] = $this->validateBirthday($user->getBirthday());
        $errors['phone_error'] = $this->validatePhone($user->getPhoneNumber());
        $errors['password_error'] = $this->validatePassword($user->getPassword());
        $errors['confirm_password_error'] = $this->validConfirmPassword($user->getPassword(),$user->getConfirmPassword());
        $errors['image_error'] = $this->validateImage($uploadedFiles['profile_image'],$filenames);

        //Exists any error?
        if(strlen($errors['name_error']) != 0 || strlen($errors['username_error']) != 0 || strlen($errors['email_error']) != 0 ||
            strlen($errors['birthday_error']) != 0 || strlen($errors['phone_error']) != 0 || strlen($errors['password_error']) != 0 ||
            strlen($errors['confirm_password_error']) != 0 || (strlen($errors['image_error']) != 0 && $errors['image_error'] != '0'))
                return $this->container->get('view')->render($response, 'register.twig', [
                    'name_error' => $errors['name_error'],
                    'username_error' => $errors['username_error'],
                    'email_error' => $errors['email_error'],
                    'birthday_error' => $errors['birthday_error'],
                    'phone_error' => $errors['phone_error'],
                    'password_error' => $errors['password_error'],
                    'confirm_password_error' => $errors['confirm_password_error'],
                    'image_error' => $errors['image_error'],
                    'name' => $user->getName(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'birthday' => $user->getBirthday(),
                    'phone' => $user->getPhoneNumber(),
                    'password' => $user->getPassword(),
                    'confirm_password' => $user->getConfirmPassword(),
                ]);

        //Search for repeated users in DB
        if($repository->findUserByUsername($user))
            return $this->container->get('view')->render($response, 'register.twig', [
                'username_error' => 'User already registered',
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'birthday' => $user->getBirthday(),
                'phone' => $user->getPhoneNumber(),
                'password' => $user->getPassword(),
                'confirm_password' => $user->getConfirmPassword(),
            ]);

        //Save image
        if($errors['image_error'] == '') $this->writeImage($uploadedFiles['profile_image'],$filenames,$user);

        //Save user
        $repository->save($user);

        //Send mail
        $this->sendMail($user);

        //Go to login
        return $this->container->get('view')->render($response, 'login.twig', [
            'message' =>self::REGISTER_SUCCESSFUL_MESSAGE,
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
        if(strlen($password) < 6) return self::PASSWORD_ERROR;
        return '';
    }

    private function validConfirmPassword($password,$confirmPassword){
        if(strcmp($password,$confirmPassword) != 0) return self::CONFIRM_PASSWORD_ERROR;
        return '';
    }

    private function validateImage(array $pathImages, array &$filenames) : string {
        /** @var UploadedFile $uploadedFile */
        foreach($pathImages as $uploadedFile){
            if($uploadedFile->getSize() == 0 || $uploadedFile->getSize() == null) return '0';
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            if(strcmp($extension,'jpg') != 0 && strcmp($extension,'png') != 0){        //Extension error?
                return self::IMAGE_EXTENSION_ERROR;
            }else if($uploadedFile->getError() === UPLOAD_ERR_FORM_SIZE){                                                   //Surpassed file max size defined on .twig ?
                return self::IMAGE_SIZE_ERROR;
            }else if($uploadedFile->getError() === UPLOAD_ERR_OK){
                try {
                    $basename = bin2hex(random_bytes(8));                                                           //Random filename
                    array_push($filenames,sprintf('%s.%0.8s',$basename,$extension));
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    return self::IMAGE_EXCEPTION_ERROR;
                }
            }
        }

        return '';
    }

    private function writeImage(array $pathImages,array $filenames, User $user){
        for($i = 0; $i < sizeof($pathImages); $i++){
            $pathImages[$i]->moveTo($this->container->get('upload_directory') . DIRECTORY_SEPARATOR . $filenames[$i]);      //Write image on ./uploads
            $user->addProfileImage($filenames[$i]);                                                                         //Relate user with their own images
        }
    }

    private function sendMail(User $user){
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $address = 'http://www.pwpop.test/account-validation/?id=' . md5($user->getUsername());
            $body = "<!DOCTYPE html>
                    <html lang='en'>
                        <head>
                            <title>Register Mail Validation</title>
                        </head>
                        <body>
                            <div>Welcome to PWPOP. Click on the following link to validate your account</div>
                            <div><a href = $address>VALIDATION LINK</a></div>                   
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