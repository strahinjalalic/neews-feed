<?php

class Database {
    private $connection;

    function __construct()
    {
        $this->db_connection();   
    }

    function db_connection() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        return $this->connection;
    }

    function query($sql) {
        $result = $this->connection->query($sql);
        if(!$result) {
            echo "QUERY FAILED! " . $this->connection->error;
        }

        return $result;
    }

    function escape_string($property) {
        $escaped = $this->connection->real_escape_string($property);
        return $escaped;
    }


}

$database = new Database();
?>