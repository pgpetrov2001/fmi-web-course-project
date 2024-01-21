<?php

class Db {
    private $connection;
    public function __construct() {
        $dbhost = "127.0.0.1";
        $dbName = "curriculum_generator_app";
        $userName = "root";
        $userPassword = "root";

        $this->connection = new PDO("mysql:host=$dbhost;port=3306;dbname=$dbName", $userName, $userPassword, [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection() {
        return $this->connection;
    }
}
