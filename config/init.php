<?php 
require_once("define.php");
if($_SERVER['PHP_SELF'] != '/nefeedws/register.php') { //ne ukljucuju se ovi fajlovi ukoliko smo na register stranici
    include("includes/header.php");
    include("includes/footer.php");
}
?>