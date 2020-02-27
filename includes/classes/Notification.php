<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

class Notification {
    private $user;

    function __construct($user)
    {
        $this->user = new User($user);
    }

    public function getUnreadNumber() {
        global $database;
        $loggedIn = $this->user->getUsername();
        $query = $database->query("SELECT * FROM notifications WHERE viewed = 'no' AND user_to = '{$loggedIn}'");
        return mysqli_num_rows($query);
    }

    public function insertNotification($post_id, $user_to, $type) {
        global $database;
        $loggedIn = $this->user->getUsername();
        $loggedIn_name = $this->user->getFirstAndLastName();

        $date = Carbon::now();
        switch($type) {
            case 'comment':
                $message = $loggedIn_name . " commented on your post.";
                break;
            case 'like':
                $message = $loggedIn_name . " liked your post.";
                break;
            case 'profile_post':
                $message = $loggedIn_name . " posted on your profile.";
                break;
            case 'comment_post':
                $message = $loggedIn_name . " commented on post you commented on.";
                break;
            case 'profile_comment':
                $message = $loggedIn_name . " commented on profile post.";
                break;
        }

        $link = "post.php?id=" . $post_id;
        $insert_notification_query = $database->query("INSERT INTO notifications(user_to, user_from, message, link, datetime, opened, viewed) VALUES('{$user_to}', '{$loggedIn}', '{$message}', '{$link}', '{$date}', 'no', 'no')");
    }

    public function getNotifications($data, $limit) {
        global $database;
        $page = $data['page'];
        $loggedIn = $this->user->getUsername();
        $str = "";

        ($page == 1) ? $start = 1 : $start = ($page - 1) * $limit;

        $viewed_query = $database->query("UPDATE notifications SET viewed = 'yes' WHERE user_to = '{$loggedIn}'");
        $get_user = $database->query("SELECT * FROM notifications WHERE user_to = '{$loggedIn}' ORDER BY id DESC");

        if(mysqli_num_rows($get_user) == 0) {
            echo "You have no notifications.";
            return;
        }

        $num_iterations = 0;
        $count = 1;

        while($row = mysqli_fetch_array($get_user)) {
            if(++$num_iterations < $start) {
                continue;
            }
            if($count > $limit) {
                break;
            } else {
                $count++;
            }

            $user_from = $row['user_from'];
            $query = $database->query("SELECT * FROM users WHERE username = '{$user_from}'");
            $user_details = mysqli_fetch_array($query);

            $date = Carbon::create($row['datetime'])->diffForHumans();
        
            $css = ($row['opened'] == 'no') ? "background-color: dimgrey;" : "";

            $str .= "<a href='" . $row['link'] . "'> 
                        <div class='result resultNotification' style='". $css ."'>
                            <div class='notificationsImg'>
                                <img src='". $user_details['profile_picture'] ."'>
                            </div>
                            <p class='timestamp' id='grey'>". $date ."</p>" . $row['message'] ."
                        </div><hr>
                    </a>";
        }
        // if($count > $limit) { //ako su poruke ucitane
        //     $str .= "<input type='hidden' class='nextPageDropdownData' value='". ($page+1) ."'><input type='hidden' class='noMoreDropdownData' value='false'>";
        // } else {
        //     $str .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align:center;'>No More Messages</p>"; //ukoliko se nije ispunio uslov, ne moze da prestigne limit poruka koje se ucitavaju
        // }
        return $str;
    }
  }

?>
