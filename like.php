<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="assets/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- <script src="assets/js/bootstrap.min.js"></script> -->
    <script src="https://kit.fontawesome.com/3e57996fc5.js" crossorigin="anonymous"></script>
    <style>
        * {
            font-family:Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #fff;
        }

        form {
            position: absolute;
            top: 1px;
        }
    </style>
</head>
<?php
session_start();
include "config/define.php";
global $database;

if (!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: register.php");
} else {
    $loggedIn = $_SESSION['username'];
}
if (isset($_GET['post_id']) && isset($_GET['username'])) {
    $id = $_GET['post_id'];
    $username = $_GET['username'];
}

$posts_query = $database->query("SELECT likes FROM posts WHERE id = {$id}");
$row = mysqli_fetch_array($posts_query);
$total_likes = $row['likes'];

if (isset($_POST['like'])) {
    $total_likes++;
    $likes_query = $database->query("INSERT INTO likes(username, post_id) VALUES('{$loggedIn}', {$id})");
    $update_user = $database->query("UPDATE users SET num_likes = num_likes+1 WHERE username = '{$username}'");
    $update_posts = $database->query("UPDATE posts SET likes = {$total_likes} WHERE id = {$id}");
    header('Location: ' . $_SERVER['REQUEST_URI']);
}

if (isset($_POST['unlike'])) {
    $total_likes--;
    $delete_likes = $database->query("DELETE FROM likes WHERE post_id = {$id} AND username = '{$loggedIn}'");
    $update_user = $database->query("UPDATE users SET num_likes = num_likes - 1 WHERE username = '{$username}'");
    $update_posts = $database->query("UPDATE posts SET likes = {$total_likes} WHERE id = {$id}");
    header('Location: ' . $_SERVER['REQUEST_URI']);
}

$user_liked_post = $database->query("SELECT * FROM likes WHERE post_id = {$id} AND username = '{$loggedIn}'");
if (mysqli_num_rows($user_liked_post) == 0) { //ako je logovani user lajkovo post
    echo "<form action='like.php?post_id={$id}&username={$username}' method='POST'>
            <i class='far fa-thumbs-up'></i><input type='submit' name='like' value='Like' class='like_post'>
          </form>
          <span id='likes_span'> {$total_likes} Likes </span>
          ";
} else {
    echo "<form action='like.php?post_id={$id}&username={$username}' method='POST'>
            <i class='far fa-thumbs-down'></i><input type='submit' name='unlike' value='Unlike' class='unlike_post'>
          </form>
          <span id='likes_span'> {$total_likes} Likes </span>";
}

?>