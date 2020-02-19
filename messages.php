<?php include("config/init.php");
require 'vendor/autoload.php';
use Carbon\Carbon; 
global $database;
$message = new Message($loggedIn);

if(isset($_GET['u'])) {
    $user_to = $_GET['u'];
} else {
    $user_to = $message->getRecentUser();
    if($user_to == false) {
        $user_to = 'new';
    }
}

if($user_to != 'new') {
    $user_to_ins = new User($user_to);
}

if(isset($_POST['send_message'])) {
    if(isset($_POST['message_content'])) {
        $message_content = $database->escape_string($_POST['message_content']);
        $date_added =  Carbon::now()->toDateTimeString();
        $message->sendMessage($user_to, $message_content, $date_added);
    }
}
?>
<div class="user_det col">
        <a href="<?php echo $loggedIn; ?>"><img src="<?php echo $user['profile_picture']; ?>"></a>
        <div class="user_det_lt_rt">
            <a href="<?php echo $loggedIn; ?>">
                <?php echo $user['first_name'] . " " . $user['last_name']; ?>
            </a>
            <br>
            <?php echo 'Likes: ' . $user['num_likes'] . '<br>';
                echo 'Posts: '. $user['num_posts'] . '<br>'  ?>
        </div>
</div>
<div class="main col">
    <?php 
      if($user_to != 'new') {
          echo "<h3>You and <a href='{$user_to}'>" . $user_to_ins->getFirstAndLastName() . "</a></h4><hr><br>";
          echo "<div class='message_retrieve' id='scroll'>";
            echo $message->getAllMessages($user_to);
          echo "</div>";
      } else {
          echo "<h3>New Message</h3>";
      }
    ?>

    <div class="message_send">
        <form action="" method="POST">
            <?php if($user_to == "new") {
                echo "Select friend to chat with!"; ?>
                    To: <input type='text' onkeyup='getUser(this.value, "<?php echo $loggedIn; ?>")' name='search_friends' placeholder='Name' autocomplete='off' id='search_friends'>
                <?php
                echo "<div class='results'></div>";
            } else {
                echo "<textarea name='message_content' id='message_content' placeholder='Write your message..'></textarea>";
                echo "<input type='submit' class='request_received' id='send_message' name='send_message' value='Send'>";
            } ?>
        </form>
    </div>

    <script>
        var div = document.getElementById("scroll");
        div.scrollTop = div.scrollHeight;
    </script>
</div>

<div class="user_det col" id='conversations'>
    <h4>Conversations</h4>
    <div class="all_conversations">
        <?php
            echo $message->getAllConversations();
        ?>
    </div>
    <br>
    <a href="messages.php?u=new">New Message</a>
</div>