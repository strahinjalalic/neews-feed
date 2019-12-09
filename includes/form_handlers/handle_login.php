<?php
$login_errors = array();
if(isset($_POST['submit_login'])) {
    global $database;
    
    $username_email = $database->escape_string($_POST['username_email_login']);
    $db_query = $database->query("SELECT * FROM users WHERE username = '{$username_email}' OR email = '{$username_email}'");
    $row = mysqli_fetch_array($db_query);
    $db_pass = $row['password'];
    $password = $database->escape_string($_POST['password_login']);
    $password_ver = password_verify($password, $db_pass); //poredjenje userovog inputa sa hashiranim stringom u bazi
    if (empty($username_email) || empty($password)) {
        array_push($login_errors, "Fill out both fields!");
    } else {
        if($password_ver) {
                $full_name = $row['first_name'] . " " . $row['last_name'];
                $username = $row['username'];

                $acc_closed = $database->query("SELECT * FROM users WHERE (username='{$username_email}' OR email='{$username_email}') AND user_closed='yes'");
                if (mysqli_num_rows($acc_closed) == 1) {
                    $reopen_acc = $database->query("UPDATE users SET user_closed='no' WHERE (username='{$username_email}' OR email='{$username_email}')");
                }
                $_SESSION['name'] = $full_name;
                $_SESSION['username'] = $username;
                header("Location: index.php");
            }
            else {
                array_push($login_errors, "Username/email or password is wrong!");
            }
    }
}
?>