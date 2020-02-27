<?php
include('config/define.php');
global $database;

$limit = 7;
$notification = new Notification($_REQUEST['loggedIn']);
echo $notification->getNotifications($_REQUEST, $limit); 
?>