<?php
include('config/init.php');
global $database;

if(isset($_GET['q'])) {
    $search = $_GET['q'];
} else {
    $search = "";
}

if(isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "name";
}
?>
<div class="main col">
    <?php 
        if($search == "") {
            echo "Enter something in search box.";
        } else {
            $full_names = explode(" ", $search);
            if(count($full_names) == 2) { 
                $find_user = $database->query("SELECT * FROM users WHERE (first_name LIKE '%{$full_names[0]}%' AND last_name LIKE '%{$full_names[1]}%') AND user_closed = 'no' LIMIT 5");
            } else {
                $find_user = $database->query("SELECT * FROM users WHERE (first_name LIKE '%{$full_names[0]}%' OR last_name LIKE '%{$full_names[0]}%') AND user_closed = 'no' LIMIT 5");
            }

            if(mysqli_num_rows($find_user) == 0) {
                echo "We can't find user by the name of ". $search;
            } else {
                echo mysqli_num_rows($find_user) . " results found: <br><br>";
            }

            while($row = mysqli_fetch_array($find_user)) {
                $new_user = new User($loggedIn);
                $button = "";
                $mutual_friends = "";
                if($loggedIn != $row['username']) {
                    if($new_user->isFriend($row['username'])) {
                        $button = "<input type='submit' name='". $row['username'] ."' class='remove_friend' value='Remove Friend'>";
                    } else if($new_user->didReceiveRequest($row['username'])) {
                        $button = "<input type='submit' name='". $row['username'] ."' class='request_received' value='Respond to request'>";
                    } else if($new_user->didSentRequest($row['username'])) {
                        $button = "<input class='request_sent_search' value='Request sent' disabled>";
                    } else {
                        $button = "<input type='submit' name='". $row['username'] ."' class='add_friend' value='Add Friend'>";
                    }

                    $mutual_friends = $new_user->mutualFriends($row['username']) . " friends in common";

                    if(isset($_POST[$row['username']])) {
                        if($new_user->isFriend($row['username'])) {
                            $new_user->removeFriend($row['username']);
                            header("Location: http://{$_SERVER}[HTTP_HOST]{$_SERVER}[REQUEST_URI]");
                        } else if($new_user->didReceiveRequest($row['username'])) {
                            header("Location: requests.php");
                        } else {
                            $new_user->addFriend($row['username']);
                            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        }
                    }
                }

                echo "<div class='search_all'>
                        <div class='friendButtons'>
                            <form action='' method='POST'> {$button} <br></form>
                        </div>
                        <div class='search_profile_picture'>
                            <a href='{$row['username']}'><img src='{$row['profile_picture']}' style='height:100px;'></a>
                        </div>

                        <a href='{$row['username']}'> {$row['first_name']} {$row['last_name']}
                        <p id='grey'> {$row['username']} </p></a><br>
                        {$mutual_friends}
                      </div><hr id='search_hr'>";
            }
        }
    ?>
</div>