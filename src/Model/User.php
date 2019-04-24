<?php


namespace SallePW\SlimApp\Model;


class User
{
    private $name;
    private $username;
    private $email;
    private $birthday;
    private $phone_number;
    private $password;
    private $confirm_password;
    private $profile_image;

    /**
     * User constructor.
     * @param $name
     * @param $username
     * @param $email
     * @param $birthday
     * @param $phone_number
     * @param $password
     * @param $confirm_password
     * @param $profile_image
     */
    public function __construct($name, $username, $email, $birthday, $phone_number, $password, $confirm_password, $profile_image)
    {
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->phone_number = $phone_number;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
        $this->profile_image = $profile_image;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * @param mixed $confirm_password
     */
    public function setConfirmPassword($confirm_password)
    {
        $this->confirm_password = $confirm_password;
    }

    /**
     * @return mixed
     */
    public function getProfileImage()
    {
        return $this->profile_image;
    }

    /**
     * @param mixed $profile_image
     */
    public function setProfileImage($profile_image)
    {
        $this->profile_image = $profile_image;
    }



}