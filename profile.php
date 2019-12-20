<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

require_once("config/init.php");
global $database;
if(isset($_GET['profile_user'])) {
    $username = $_GET['profile_user'];
    $get_user_query = $database->query("SELECT * FROM users WHERE username = '{$username}'");
    $user = mysqli_fetch_array($get_user_query);

    $count_friends = substr_count($user['friends_array'], ",");
}

$user_profile_obj = new User($username);
$loggedIn_user = new User($loggedIn);
?>

<?php
if(isset($_POST['add_friend'])) {
    $loggedIn_user->addFriend($username);
}

if(isset($_POST['remove_friend'])) {
    $loggedIn_user->removeFriend($username);
}

if(isset($_POST['request_received'])) {
    header("Location: requests.php");
}
?>

    <style>
        .wrapper {
            margin-left: 0px;
            padding-left: 0px;
            height: 100vh;
        }
    </style>
    <div class="user_profile">
        <img src="<?php echo $user['profile_picture'] ?>" height="80px" alt="profile_user_<?php echo $username; ?>">
        <div class="user_info">
            <p><?php echo "Posts: " . $user['num_posts']; ?></p>
            <p><?php echo "Followers: " . $count_friends; ?></p>
            <p><?php echo "Signup date: " . Carbon::create($user['signup_date'])->diffForHumans(); ?></p>
        </div>

        <?php 
            if($user_profile_obj->isClosed()) {
                header("Location: user_closed.php");
            }
            if($loggedIn != $username) {
                if($loggedIn_user->isFriend($username)) {
                    echo "<form action='{$username}' method='POST'>
                            <input type='submit' name='remove_friend' class='remove_friend' value='Remove Friend'>
                        </form>";
                } else if($loggedIn_user->didSentRequest($username)) {
                    echo "<form action='{$username}' method='POST'>
                            <input type='submit' name='request_sent' class='request_sent' value='Request Sent'>
                        </form>";
                } else if($loggedIn_user->didReceiveRequest($username)) {
                    echo "<form action='{$username}' method='POST'>
                            <input type='submit' name='request_received' class='request_received' value='Respond To Request'>
                        </form>";
                } else {
                    echo "<form action='{$username}' method='POST'>
                            <input type='submit' name='add_friend' class='add_friend' value='Add Friend'>
                        </form>";
                }
            }
        ?>
    </div>


    <div class="main col">
        <?php echo $username; ?>
    </div>


</div> <!-- wrapper div -->