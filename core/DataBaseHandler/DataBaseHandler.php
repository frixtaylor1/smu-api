<?php

namespace SMU\Core;

use mysqli;

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
            null
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

    public function executeStoreProcedure($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if ($params) {
            $types  = '';
            $values = [];
            foreach ($params as $param) {
                $type  = key($param);
                $value = current($param);

                $types   .= $type;
                $values[] = $value;
            }

            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        $stmt->close();
        return $rows;
    }
}
