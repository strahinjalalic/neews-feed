<?php
include('config/define.php');
global $database;

$limit = 7;
$message = new Message($_REQUEST['loggedIn']);
echo $message->getDropdownConversations($_REQUEST, $limit); 
?>