<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

class Message {
    private $user;

    function __construct($user)
    {
        $this->user = new User($user);
    }

    public function getRecentUser() {
        global $database;
        $loggedIn = $this->user->getUsername();

        $find_user = $database->query("SELECT user_to, user_from FROM messages WHERE user_to ='{$loggedIn}' OR user_from = '{$loggedIn}' ORDER BY id DESC");
        if(mysqli_num_rows($find_user) == 0) {
            return false;
        }

        $row = mysqli_fetch_array($find_user);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if($user_to != $loggedIn) { //vracamo drugog ucesnika chatovanja
            return $user_to;
        } else {
            return $user_from;
        }
    }

    public function sendMessage($user_to, $content, $date_added) {
        global $database;
        if($content != "") {
            $user_from = $this->user->getUsername();
            $send_message_query = $database->query("INSERT INTO messages VALUES('', '{$user_to}', '{$user_from}', '{$content}', '{$date_added}', 'no', 'no', 'no')");
        }
    }

    public function getAllMessages($user_from) {
        global $database;
        $message = "";
        $loggedIn = $this->user->getUsername();
        $update_messages_query = $database->query("UPDATE messages SET opened = 'yes' WHERE user_to = '{$loggedIn}' AND user_from = '{$user_from}'");
        $get_messages_query = $database->query("SELECT * FROM messages WHERE (user_to = '{$loggedIn}' AND user_from = '{$user_from}') OR (user_to = '{$user_from}' AND user_from = '{$loggedIn}')");

        while($row = mysqli_fetch_array($get_messages_query)) {
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $content = $row['body'];

            $div_choice = ($user_to == $loggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
            $message .= $div_choice . $content . "</div><br><br>";
        }

        return $message;
    }

    public function getLatestMsg($loggedIn, $user) {
        global $database;
        $latest_msg_details = array();

        $get_latest_msg_query = $database->query("SELECT user_to, user_from, body, date FROM messages WHERE (user_to = '{$loggedIn}' AND user_from = '{$user}') OR (user_to = '{$user}' AND user_from = '{$loggedIn}') ORDER BY id DESC LIMIT 1");
        $row = mysqli_fetch_array($get_latest_msg_query);
        $sent_by = ($row['user_to'] == $loggedIn) ? "They said: " : "";
        $time_to_display = Carbon::create($row['date'])->diffForHumans();
        $body = $row['body'];

        array_push($latest_msg_details, $sent_by);
        array_push($latest_msg_details, $time_to_display);
        array_push($latest_msg_details, $body);

        return $latest_msg_details;
    }

    public function getAllConversations() { 
        global $database;
        $loggedIn = $this->user->getUsername();
        $str = "";
        $conv_array = array();

        $get_other_user = $database->query("SELECT user_to, user_from FROM messages WHERE user_to = '{$loggedIn}' OR user_from = '{$loggedIn}' ORDER BY id DESC");
        while($row = mysqli_fetch_array($get_other_user)) {
            $other_user = ($row['user_to'] != $loggedIn) ? $row['user_to'] : $row['user_from'];
            if(!in_array($other_user, $conv_array)) {
                array_push($conv_array, $other_user); //ubacuje se user u niz, ukoliko vec nije u nizu
            }
        }

        foreach($conv_array as $user) {
            $user_ins = new User($user);
            $latest_msg = $this->getLatestMsg($loggedIn, $user);

            if($latest_msg[0] == "") {
                $dots = (strlen($latest_msg[2]) >= 17) ? "..." : "";
                $split = str_split($latest_msg[2], 17);
                $split = $split[0] . $dots;
            } else {
                $dots = (strlen($latest_msg[2]) >= 12) ? "..." : "";
                $split = str_split($latest_msg[2], 12);
                $split = $split[0] . $dots;
            }

            $str .= "<a href='messages.php?u={$user}'> <div class='user_messages'>
                       <img src='" . $user_ins->getProfileImage() . "' style='border-radius:5px; margin-right:5px; position:relative; top:-6px;'>
                       ". $user_ins->getFirstAndLastName() ." <span class='timestamp_small' id='grey' style='font-size:12px;'>" . $latest_msg[1] . "</span><br><br>
                       <p id='grey' style='margin:0;'>". $latest_msg[0] . $split ."</p></div>
                    </a>";
        }
        return $str;
    }

    public function getDropdownConversations($data, $limit) {
        global $database;
        $page = $data['page'];
        $loggedIn = $this->user->getUsername();
        $str = "";
        $conv_array = array();

        ($page == 1) ? $start = 1 : $start = ($page - 1) * $limit;

        $viewed_query = $database->query("UPDATE messages SET viewed = 'yes' WHERE user_to = '{$loggedIn}'");
        $get_other_user = $database->query("SELECT user_to, user_from FROM messages WHERE user_to = '{$loggedIn}' OR user_from = '{$loggedIn}' ORDER BY id DESC");
        while($row = mysqli_fetch_array($get_other_user)) {
            $other_user = ($row['user_to'] != $loggedIn) ? $row['user_to'] : $row['user_from'];
            if(!in_array($other_user, $conv_array)) {
                array_push($conv_array, $other_user); //ubacuje se user u niz, ukoliko vec nije u nizu
            }
        }

        $num_iterations = 0;
        $count = 1;

        foreach($conv_array as $user) {
            if(++$num_iterations < $start) {
                continue;
            }
            if($count > $limit) {
                break;
            } else {
                $count++;
            }
            
            $pull_opened = $database->query("SELECT opened FROM messages WHERE user_to = '{$loggedIn}' AND user_from = '{$user}' ORDER BY id DESC");
            $row = mysqli_fetch_array($pull_opened);
            $css = ($row['opened'] == 'no') ? "background-color: #c1d9f5;" : "";

            $user_ins = new User($user);
            $latest_msg = $this->getLatestMsg($loggedIn, $user);

            $dots = (strlen($latest_msg[2]) >= 12) ? "..." : "";
            $split = str_split($latest_msg[2], 12);
            $split = $split[0] . $dots;

            $str .= "<a href='messages.php?u={$user}'> <div class='user_messages' style='". $css  ."'>
                       <img src='" . $user_ins->getProfileImage() . "' style='border-radius:5px; margin-right:5px; position:relative; top:-6px;'>
                       ". $user_ins->getFirstAndLastName() ." <span class='timestamp_small' id='grey' style='font-size:12px;'>" . $latest_msg[1] . "</span><br><br>
                       <p id='grey' style='margin:0;'>". $latest_msg[0] . $split ."</p></div>
                    </a>";
        }
        // if($count > $limit) { //ako su poruke ucitane
        //     $str .= "<input type='hidden' class='nextPageDropdownData' value='". ($page+1) ."'><input type='hidden' class='noMoreDropdownData' value='false'>";
        // } else {
        //     $str .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align:center;'>No More Messages</p>"; //ukoliko se nije ispunio uslov, ne moze da prestigne limit poruka koje se ucitavaju
        // }
        return $str;
    }

    public function getUnreadNumber() {
        global $database;
        $loggedIn = $this->user->getUsername();
        $query = $database->query("SELECT * FROM messages WHERE viewed = 'no' AND user_to = '{$loggedIn}'");
        return mysqli_num_rows($query);
    }
}
?>