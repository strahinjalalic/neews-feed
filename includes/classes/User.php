<?php 
class User {
    private $user;

    function __construct($user)
    {
        global $database;
        $find_user_query = $database->query("SELECT * FROM users WHERE username = '{$user}'");
        $this->user = mysqli_fetch_array($find_user_query);
    }

    public function getFirstAndLastName() {
        $full_name = $this->user['first_name'] . " " . $this->user['last_name'];
        return $full_name;
    }

    public function getUsername() {
        return $this->user['username'];
    }

}



?>