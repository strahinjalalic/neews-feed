<?php 
include("config/define.php");

$limit = 10;
$posts = new Post($_REQUEST['loggedIn']);
$posts->postsByFriends($_REQUEST, $limit);
?>