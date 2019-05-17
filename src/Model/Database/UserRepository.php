<?php
namespace SallePW\SlimApp\Model\Database;
use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepositoryInterface;
use SallePW\SlimApp\Model\Product;
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
            "INSERT INTO User(name,username,email_address,birthday,phone_number,password,validated,profile_image,is_active) VALUES (:name,:username,:email,:birthday,:phone,MD5(:password),FALSE, :profile_image,TRUE);"
        );
        $statement->bindParam('name',$user->getName(),PDO::PARAM_STR);
        $statement->bindParam('username',$user->getUsername(),PDO::PARAM_STR);
        $statement->bindParam('email', $user->getEmail(), PDO::PARAM_STR);
        $statement->bindParam('birthday',$user->getBirthday(),PDO::PARAM_STR);
        $statement->bindParam('phone',$user->getPhoneNumber(),PDO::PARAM_STR);
        $statement->bindParam('password',$user->getPassword(),PDO::PARAM_STR);
        $statement->bindParam('profile_image',$user->getProfileImage(), PDO::PARAM_STR);

        $statement->execute();
    }
    public function findUser(User $user): bool
    {
        $statement = $this->database->connection->prepare("SELECT COUNT(*) FROM User WHERE username =:username");
        $statement->bindParam('username', $user->getUsername(), PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($results[0]['COUNT(*)'] == 0) return false;
        return true;
    }
    public function findUserByUsername(User $user): bool {
        $statement = $this->database->connection->prepare("SELECT COUNT(*) FROM User WHERE username =:username");
        $statement->bindParam('username',$user->getUsername(),PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['COUNT(*)'] == 0) return false;
        return true;
    }
    public function findUserById(string $id): string {
        $statement = $this->database->connection->prepare("SELECT username FROM User WHERE MD5(username) = :id");
        $statement->bindParam('id',$id,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['username'] == null) return '';
        return $results[0]['username'];
    }
    public function validateAccount(string $username) {
        $statement = $this->database->connection->prepare("UPDATE User SET validated = TRUE WHERE username = :username");
        $statement->bindParam('username',$username,PDO::PARAM_STR);
        $statement->execute();
    }

    public function isValidated(string $id) : bool {
        $statement = $this->database->connection->prepare("SELECT validated FROM User WHERE MD5(username) = :id");
        $statement->bindParam('id',$id,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['validated'];
    }

    public function isValidatedByUser(string $username, string $password) : bool {
        $statement = $this->database->connection->prepare("SELECT validated FROM User WHERE username = :username AND password = MD5(:password)");
        $statement->bindParam('username',$username,PDO::PARAM_STR);
        $statement->bindParam('password',$password,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['validated'];
    }

    public function isValidatedByEmail(string $email, string $password) : bool {
        $statement = $this->database->connection->prepare("SELECT validated FROM User WHERE email_address = :email AND password = MD5(:password)");
        $statement->bindParam('email',$email,PDO::PARAM_STR);
        $statement->bindParam('password',$password,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['validated'];
    }

    //Product
    public function saveProduct(Product $product,string $username) {
        $title = $product->getTitle();
        $description = $product->getDescription();
        $price = $product->getPrice();
        $cat = $product->getCategory();
        $statement = $this->database->connection->prepare(
            "INSERT INTO Product (title, description, price, category,username,is_active) VALUES (:title, :description, :price, :category,:username,TRUE);");
        echo $cat;
        switch ($cat){
            case "Sports":
                $a = 0;
                break;
            case "Fashion":
                $a = 1;
                break;
            case "Computers and electronic":
                $a = 2;
                break;
            case "Cars":
                $a = 3;
                break;
            case "Games":
                $a = 4;
                break;
            case "Home":
                $a = 5;
                break;
            case "Other":
                $a = 6;
                break;
            default:
                $a = 1;
                break;
        }
        $statement->bindParam('title',$title,PDO::PARAM_STR);
        $statement->bindParam('description', $description,PDO::PARAM_STR);
        $statement->bindParam('price', $price, PDO::PARAM_STR);
        $statement->bindParam('category',$a,PDO::PARAM_STR);
        $statement->bindParam('username',$username,PDO::PARAM_STR);
        $statement->execute();
    }

    public function saveFavouriteProduct($id_user, $id_product) {

        $statement = $this->database->connection->prepare(
            "INSERT INTO Favourite_Product (id_user, id_product) VALUES (:id_user, :id_product);");

        $statement->bindParam('id_user',$id_user,PDO::PARAM_STR);
        $statement->bindParam('id_product', $id_product,PDO::PARAM_STR);
        $statement->execute();
    }
    //imageProduct
    public function saveImageProduct(string $product_image)
    {
        $statement = $this->database->connection->prepare('SELECT * FROM Product ORDER BY id_product DESC LIMIT 1;');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $id = $results[0]['id_product'];
        $statement = $this->database->connection->prepare(
            "INSERT INTO ImageProduct (product_image, id_product) VALUES (:product_image, :id_product);");
        $statement->bindParam('product_image', $product_image, PDO::PARAM_STR);
        $statement->bindParam('id_product', $id, PDO::PARAM_STR);
        $statement->execute();
    }
    public function  getProductsFromDDBB() {
        $statement = $this->database->connection->prepare('SELECT * FROM Product;');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function  getProductsFromDDBBbyID($id_product) {
        $statement = $this->database->connection->prepare('SELECT * FROM Product WHERE id_product = :id_product;');
        $statement->bindParam('id_product',$id_product,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function  getFavouriteProduct($id_user) {
        $statement = $this->database->connection->prepare('SELECT * FROM Product AS p, Favourite_Product AS fp WHERE p.id_product = fp.id_product AND fp.id_user LIKE :id_user;');
        $statement->bindParam('id_user',$id_user,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function getImagesOfProductById() {
        $statement = $this->database->connection->prepare("SELECT * FROM ImageProduct;");
        //$statement->bindParam('id_prod',$id_prod,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo "count results" . count( $results);
        return $results;
    }
    public function findUserByLoginEmail(string $email, string $pass) : string{
        $statement = $this->database->connection->prepare("SELECT username FROM User WHERE email_address = :email AND password = MD5(:password) AND is_active = TRUE");
        $statement->bindParam('email',$email,PDO::PARAM_STR);
        $statement->bindParam('password',$pass,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['username'] == null) return '';
        return $results[0]['username'];
    }
    public function findUserByLoginUser(string $user, string $pass) : bool{
        $statement = $this->database->connection->prepare("SELECT username FROM User WHERE username = :user AND password = MD5(:password) AND is_active = TRUE");
        $statement->bindParam('user',$user,PDO::PARAM_STR);
        $statement->bindParam('password',$pass,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['username'] == null) return false;
        return true;
    }
    public function getUserById(string $id) : User{
        $statement = $this->database->connection->prepare("SELECT * FROM User WHERE MD5(username) = :id");
        $statement->bindParam('id',$id,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($results[0]['username'] == null) return null;

        $user = new User(null);
        $user->setName($results[0]['name']);
        $user->setUsername($results[0]['username']);
        $user->setEmail($results[0]['email_address']);
        $user->setBirthday($results[0]['birthday']);
        $user->setPhoneNumber($results[0]['phone_number']);
        $user->setPassword($results[0]['password']);
        $user->setValidated($results[0]['validated']);
        $user->setProfileImage($results[0]['profile_image']);

        return $user;
    }
    public function updateUser(User $user){
        $statement = $this->database->connection->prepare("UPDATE User SET name = :name, email_address = :email_address, 
                birthday = :birthday, phone_number = :phone_number, password = MD5(:password), profile_image = :profile_image WHERE username = :username");
        $statement->bindParam('name',$user->getName(),PDO::PARAM_STR);
        $statement->bindParam('email_address',$user->getEmail(),PDO::PARAM_STR);
        $statement->bindParam('birthday',$user->getBirthday(),PDO::PARAM_STR);
        $statement->bindParam('phone_number',$user->getPhoneNumber(),PDO::PARAM_STR);
        $statement->bindParam('password',$user->getPassword(),PDO::PARAM_STR);
        $statement->bindParam('profile_image',$user->getProfileImage(),PDO::PARAM_STR);
        $statement->bindParam('username',$user->getUsername(),PDO::PARAM_STR);
        $statement->execute();
    }
    public function deleteUser(string $id){
        $statement = $this->database->connection->prepare("UPDATE User SET is_active = FALSE WHERE MD5(username) = :username");
        $statement->bindParam('username',$id,PDO::PARAM_STR);
        $statement->execute();
        $statement = $this->database->connection->prepare("UPDATE Product SET is_active = FALSE WHERE MD5(username) = :username");
        $statement->bindParam('username',$id,PDO::PARAM_STR);
        $statement->execute();
    }
    public function isDeletedUser(string $id){
        $statement = $this->database->connection->prepare("SELECT is_active FROM User WHERE MD5(username) = :username");
        $statement->bindParam('username',$id,PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return !$results[0]['is_active'];
    }
}