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

    public function incrementUserPosts() {
        global $database;
        $increment_num_posts = $database->query("UPDATE users SET num_posts = num_posts + 1 WHERE username = '{$this->user['username']}'");
    }

    public function isClosed() {
        global $database;
        $username = $this->user['username'];
        $user_closed = $database->query("SELECT * FROM users WHERE user_closed = 'yes' AND username = '{$username}'");
        if(mysqli_num_rows($user_closed) == 1) {
            return true;
        }
        return false;
    }

}



?>