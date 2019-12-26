<?php session_start();
if(isset($_SESSION['username'])) {
  $loggedIn = $_SESSION['username'];
}
include("config/define.php");
global $database;
if(isset($_FILES['file'])) {
  $image_uploaded = $_FILES['file']['name'];
  $image_uploaded_tmp = $_FILES['file']['tmp_name'];

  if(move_uploaded_file($image_uploaded_tmp, "assets/images/profile_pictures/{$image_uploaded}")) {
    echo "assets/images/profile_pictures/{$image_uploaded}";
  }
  $database->query("UPDATE users SET profile_picture = 'assets/images/profile_pictures/{$image_uploaded}' WHERE username = '{$loggedIn}'");
}
?>