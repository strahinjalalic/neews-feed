<?php include("config/init.php");
global $database; ?>

<div class="main col">
    <h3>Friend Requests</h3>
    <?php 
      $select_requests = $database->query("SELECT * FROM friend_requests WHERE user_to = '{$loggedIn}'"); 
      if(mysqli_num_rows($select_requests) == 0) {
          echo "You have no friend requests.";
      } else {
          while($row = mysqli_fetch_array($select_requests)) {
              $user_from = $row['user_from'];
              $user_from_ins = new User($user_from);
              $loggedIn_user = new User($loggedIn);

              echo "<p style='padding:10px 0px 0px 10px;'>{$user_from_ins->getFirstAndLastName()} sent you friend request.</p>";
              $user_from_friends = $user_from_ins->getFriendArray();
              $loggedIn_user_friends = $loggedIn_user->getFriendArray();

              if(isset($_POST['accept_request' . $user_from])) {
                   if(!empty($user_from_friends)) {
                        $new_friends_array_user_from = str_replace($user_from_friends, $user_from_friends . $loggedIn . ",", $user_from_friends);
                        $update_database = $database->query("UPDATE users SET friends_array = '{$new_friends_array_user_from}' WHERE username = '{$user_from}'");
                   } else {
                        $new_friends_array_user_from .= $loggedIn . ","; 
                        $update_database = $database->query("UPDATE users SET friends_array = '{$new_friends_array_user_from}' WHERE username = '{$user_from}'");
                   }

                   if(!empty($loggedIn_user_friends)) {
                        $new_friends_array_loggedIn = str_replace($loggedIn_user_friends, $loggedIn_user_friends . $user_from . ",", $loggedIn_user_friends);
                        $update_array = $database->query("UPDATE users SET friends_array = '{$new_friends_array_loggedIn}' WHERE username = '{$loggedIn}'");
                   } else {
                        $new_friends_array_loggedIn .= $user_from . ",";
                        $update_array = $database->query("UPDATE users SET friends_array = '{$new_friends_array_loggedIn}' WHERE username = '{$loggedIn}'");
                   }
                   $delete_from_requests = $database->query("DELETE FROM friend_requests WHERE user_to = '{$loggedIn}' AND user_from = '{$user_from}'");
                   echo "You are now friends with {$user_from_ins->getFirstAndLastName()}!";
                   header("Location: requests.php");
              }

              if(isset($_POST['ignore_request' . $user_from])) {
                  $delete_from_requests = $database->query("DELETE FROM friend_requests WHERE user_to = '{$loggedIn}' AND user_from = '{$user_from}'");
                  echo "You ignored this request.";
                  header("Location: requests.php");
              }
        ?>
            <form action="" method="POST">
                <input type="submit" name="accept_request<?php echo $user_from; ?>" value="Accept" class='accept_request'>
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" value="Ignore" class='ignore_request'>
            </form>
        <?php
          }
      }
    ?>
</div>