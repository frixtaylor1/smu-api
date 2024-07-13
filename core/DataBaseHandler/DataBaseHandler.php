<?php

class DataBaseHandler
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $host     = getenv('MYSQL_HOST');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');
        $database = getenv('MYSQL_DATABASE');
        $port     = getenv('MYSQL_PORT');


        $this->conn = new mysqli(
            $host,
            $username,
            $password,
            $database,
            $port,
            null,
            MYSQLI_CLIENT_FOUND_ROWS | MYSQLI_CLIENT_COMPRESS | MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_IGNORE_SPACE | MYSQLI_CLIENT_INTERACTIVE | MYSQLI_CLIENT_CAN_HANDLE_EXPIRED_PASSWORDS | MYSQLI_CLIENT_NO_SCHEMA
        );

        if ($this->conn->connect_error) {
            die("Connection faild: " . $this->conn->connect_error);
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DataBaseHandler();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}