<?php
include("config/init.php");
global $database;

if(isset($_POST['cancel'])) {
    header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
    $close_account_query = $database->query("UPDATE users SET user_closed = 'yes' WHERE username = '{$loggedIn}'");
    session_destroy();
    header("Location: register.php");
}
?>

<div class="main col">
    <h3>Close Account</h3>
        Are you sure you want to close your account?<br><br>
        Closing account will hide your profile and your activity to other users. <br><br>
        Re-open your account by simply logging in! <br><br>

    <form action="" method="POST">
        <input type="submit" value="Yes! Close my account!" name="close_account" id="close_account" class="remove_friend settings_update">
        <input type="submit" value="No!" name="cancel" class="request_received settings_update">
    </form>

</div>