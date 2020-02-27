<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

session_start();
include("config/define.php");
global $database; 

if(!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: register.php");
} else {
    $loggedIn = $_SESSION['username'];
}

if(isset($_GET['post_id'])) {
    $id = $_GET['post_id'];
}

$find_users_query = $database->query("SELECT * FROM posts WHERE id = {$id}");
$row = mysqli_fetch_array($find_users_query);
$added_by = $row['added_by'];
$user_to = $row['user_to'];

if(isset($_POST['submit_comment'])) {
    $comment_content = $database->escape_string($_POST['comment_content']);
    $date_added = Carbon::now();
    $insert_comment = $database->query("INSERT INTO comments(comment_body, comment_by, comment_to, date_added, removed, post_id) VALUES('{$comment_content}', '{$loggedIn}', '{$added_by}', '{$date_added}', 'no', {$id})");

    if($added_by != $loggedIn) { //komentarisanje posta nekom korisniku od strane logovanog
        $notification = new Notification($loggedIn);
        $notification->insertNotification($id, $added_by, 'comment');
    } 
    
    if($user_to != 'none' && $user_to != $loggedIn) { 
        $notification = new Notification($loggedIn);
        $notification->insertNotification($id, $user_to, 'profile_comment');
    }

    $select_other_comments = $database->query("SELECT * FROM comments WHERE post_id={$id}");
    $users_notified = array();
    while($row = mysqli_fetch_array($select_other_comments)) {
        if($row['comment_by'] != $added_by && $row['comment_by'] != $user_to && $row['comment_by'] != $loggedIn && !in_array($row['comment_by'], $users_notified)) {
            $notification = new Notification($loggedIn);
            $notification->insertNotification($id, $user_to, 'comment_post');

            array_push($users_notified, $row['comment_by']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <style>
        * {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    <script>
        function toggle() {
            var element = document.getElementById("load_comments");
            if(element.style.display == "block") {
                element.style.display = 'none';
            } else {
                element.style.display = 'block';
            }
        }
    </script>   
    <form action="" method="POST" id='comment_form'>
        <textarea name="comment_content"></textarea>
        <input type="submit" value="Comment" name='submit_comment'>
    </form>

    <?php
     $load_comments_query = $database->query("SELECT * FROM comments WHERE post_id = {$id} ORDER BY post_id");
     if(mysqli_num_rows($load_comments_query) != 0) {
         while($row = mysqli_fetch_array($load_comments_query)) {
             $comment_content = $row['comment_body'];
             $comment_author = $row['comment_by'];
             $comment_to = $row['comment_to'];
             $date_added = $row['date_added'];
             $date_added_full = Carbon::create($date_added)->diffForHumans();
             $removed = $row['removed'];

             $comment_by_user = new User($comment_author);

             ?>
            <div id='load_comments'>
                <a href="<?php echo $comment_author; ?>" target='_parent'> <img src="<?php echo $comment_by_user->getProfileImage() ?>" style="float:left;" height="30"> </a>
                <a href="<?php echo $comment_author; ?>" target='_parent'> <b> <?php echo $comment_by_user->getFirstAndLastName(); ?> </b></a>
                &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $date_added_full . "<br>" . $comment_content; ?>
                <hr>
            </div>
             <?php  }  
            } else {
                echo "<p style='text-align:center;'><br>No Comments</p>";
            } ?>        
    </body>
</html>