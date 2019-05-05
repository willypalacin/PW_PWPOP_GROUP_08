<?php


namespace SallePW\SlimApp\Model\Database;

use PDO;


class Database
{
    private const CONNECTION_STRING = 'mysql:host=%s;dbname=%s';

    private static $instance = null;

    /** @var PDO */
    public $connection;     // ABANS ESTAVA EN PROTECTED, PREGUNTAR COM SOLUCIONAR-HO SENSE FER-LA PUBLIC!

    private function __construct(
        string $username,
        string $password,
        string $host,
        string $database
    ) {
        $db = new PDO(
            sprintf(self::CONNECTION_STRING, $host, $database),
            $username,
            $password
        );

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection = $db;
    }

    public static function getInstance(
        string $username,
        string $password,
        string $host,
        string $database
    ): Database {
        if (self::$instance === null) {
            self::$instance = new self($username, $password, $host, $database);
        }
        return self::$instance;
    }

}