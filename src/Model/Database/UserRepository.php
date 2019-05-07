<?php


namespace SallePW\SlimApp\Model\Database;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
    /** @var Database */
    private $database;

    /**
     * UserRepository constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function save(User $user)
    {
        $statement = $this->database->connection->prepare(
            "INSERT INTO User(name,username,email_address,birthday,phone_number,password,validated) VALUES (:name,:username,:email,:birthday,:phone,MD5(:password),FALSE);"
        );

        $statement->bindParam('name',$user->getName(),PDO::PARAM_STR);
        $statement->bindParam('username',$user->getUsername(),PDO::PARAM_STR);
        $statement->bindParam('email', $user->getEmail(), PDO::PARAM_STR);
        $statement->bindParam('birthday',$user->getBirthday(),PDO::PARAM_STR);
        $statement->bindParam('phone',$user->getPhoneNumber(),PDO::PARAM_STR);
        $statement->bindParam('password',$user->getPassword(),PDO::PARAM_STR);

        $statement->execute();
        foreach ($user->getProfileImage() as $profileImage){
            $statement = $this->database->connection->prepare(
                "INSERT INTO Image(id_user, profile_image) VALUES (:username,:image);"
            );
            $statement->bindParam('username',$user->getUsername(), PDO::PARAM_STR);
            $statement->bindParam('image',$profileImage,PDO::PARAM_STR);
            $statement->execute();
        }
    }

    public function findUser(User $user): bool {
        $statement = $this->database->connection->prepare("SELECT COUNT(*) FROM User WHERE username =:username");
        $statement->bindParam('username',$user->getUsername(),PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['COUNT(*)'] == 0) return false;
        return true;
    }

}