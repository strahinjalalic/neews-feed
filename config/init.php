<?php 

defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);
defined("DB_HOST") ? null : define("DB_HOST", "localhost");
defined("DB_USER") ? null : define("DB_USER", "root");
defined("DB_PASSWORD") ? null : define("DB_PASSWORD", "Levaobala1!");
defined("DB_NAME") ? null : define("DB_NAME", "nefeedws");
require_once("database.php");
require_once("includes/classes/User.php");

if($_SERVER['PHP_SELF'] != '/nefeedws/register.php') { //ne ukljucuju se ovi fajlovi ukoliko smo na register stranici
    include("includes/header.php");
    include("includes/footer.php");
}
?>