<?php
include("config/define.php");
global $database;

if(isset($_POST['update_details'])) {
    $first_name = $database->escape_string($_POST['first_name']);
    $last_name = $database->escape_string($_POST['last_name']);
    $username = $database->escape_string($_POST['username']);

    $check_query = $database->query("SELECT * FROM users WHERE email = '{$user['email']}'");
    $user_check = mysqli_fetch_array($check_query);
    $username_db = $user_check['username'];

    if($username_db == $loggedIn) {
        $update_query = $database->query("UPDATE users SET first_name = '{$first_name}', last_name = '{$last_name}', username = '{$username}' WHERE email = '{$user['email']}'");
        $message = "You successfully updated your info!<br>";
    } else {
        $message = "Unable to update information.<br>";
    }
} else {
    $message = "";
}



if(isset($_POST['update_password'])) {
    $old_password = $database->escape_string($_POST['old_p']);
    $new_password = $database->escape_string($_POST['new_p']);
    $repeat_new_password = $database->escape_string($_POST['new_p2']);

    $check_password_query = $database->query("SELECT password FROM users WHERE username = '{$loggedIn}'");
    $pass_row = mysqli_fetch_array($check_password_query);
    $old_password_db = $pass_row['password'];
    

    if(password_verify($old_password, $old_password_db)) {
        if(!empty($new_password) && !empty($repeat_new_password)) {
            if($new_password == $repeat_new_password) {
                if(strlen($new_password) < 5 || strlen($new_password) > 25) {
                    $message2 = "New password must be between 5 and 25 characters!<br>";
                } else {
                    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $update_password_query = $database->query("UPDATE users SET password = '{$new_password_hash}'");
                    $message2 = "Password is successfully changed!<br>";
                }
            } else {
                $message2 = "Passwords must match!<br>";
            }
        } else {
            $message2= "Password input fields can't be blank!<br>";
        }
    } else {
        $message2 = "Enter correct old password!<br>";
    }
} else {
    $message2 = "";
}


if(isset($_POST['close_account'])) {
    header("Location: close_account.php");
}
?>