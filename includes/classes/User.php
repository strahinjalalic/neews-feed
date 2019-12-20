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

    public function getFriendArray() {
        return $this->user['friends_array'];
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

    public function isFriend($added_by) {
        if((strstr($this->user['friends_array'], $added_by)) || $added_by == $this->user['username']) {
            return true;
        } else {
            return false;
        }
    }

    public function getProfileImage() {
        return $this->user['profile_picture'];
    }

    public function didSentRequest($user_to) {
        global $database;
        $user_from = $this->user['username'];
        $check_request = $database->query("SELECT * FROM friend_requests WHERE user_to = '{$user_to}' AND user_from = '{$user_from}'");
        if(mysqli_num_rows($check_request) > 0) {
            return true;
        }

        return false;
    }

    public function didReceiveRequest($user_from) {
        global $database;
        $user_to = $this->user['username'];
        $check_request = $database->query("SELECT * FROM friend_requests WHERE user_to = '{$user_to}' AND user_from = '{$user_from}'");
        if(mysqli_num_rows($check_request) > 0) {
            return true;
        }
        return false;
    }

    public function removeFriend($friend_username) {
        global $database;
        $username_loggedIn = $this->user['username'];
        $select_friends_array = $database->query("SELECT friends_array FROM users WHERE username = '{$username_loggedIn}'");
        $row = mysqli_fetch_array($select_friends_array);
        $friends_array = $row['friends_array'];
        $new_friends_array = str_replace($friend_username . ",", " ", $friends_array); //izbacivanje username-a iz niza
        $migrate_new_array = $database->query("UPDATE users SET friends_array = '{$new_friends_array}' WHERE username = '{$username_loggedIn}'");

        $select_other_friends_array = $database->query("SELECT friends_array FROM users WHERE username = '{$friend_username}'");
        $row2 = mysqli_fetch_array($select_other_friends_array);
        $friends_array_other_user = $row2['friends_array'];
        $new_friends_array_other_user = str_replace($username_loggedIn . ",", " ", $friends_array_other_user);
        $migrate_new_array2 = $database->query("UPDATE users SET friends_array = '{$new_friends_array_other_user}' WHERE username = '{$friend_username}'");
        header("Location: {$friend_username}");
    }

    public function addFriend($friend_username) {
        global $database;
        $loggedIn = $this->user['username'];
        $insert_into_requests = $database->query("INSERT INTO friend_requests(user_to, user_from) VALUES('{$friend_username}', '{$loggedIn}')");
        header("Location: {$friend_username}");
    }
}


?>