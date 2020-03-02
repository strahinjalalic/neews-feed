<?php
$errors_array = array();
if(isset($_POST['submit'])) {
    global $database;
    $first_name = $database->escape_string($_POST['reg_first']);
    if(strlen($first_name) > 25 || strlen($first_name) < 2) {
        array_push($errors_array, "You must specify name between 2 and 25 characters!");
    } else {
        $first_name = ucfirst(strtolower($first_name)); //samo prvo slovo veliko
    }
   

    $last_name = $database->escape_string( $_POST['reg_last']);
    if(strlen($last_name) > 25 || strlen($last_name) < 2) {
        array_push($errors_array, "You must specify last name between 2 and 25 characters!");
    } else {
        $last_name = ucfirst(strtolower($last_name));
    }

    $email =  $database->escape_string($_POST['reg_email']);
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $db_emails = $database->query("SELECT email FROM users WHERE email = '{$email}'");
        if(mysqli_num_rows($db_emails) > 0) {
            array_push($errors_array, "E-mail already exist!");
        } else {
            $email_insert = filter_var($email, FILTER_VALIDATE_EMAIL);
        }
    } else {
        array_push($errors_array, "Please use regular e-mail format!");
    }

    $username = $_POST['reg_username'];
    $query = $database->query("SELECT username FROM users WHERE username = '{$username}'");
    if(strlen($username) < 5 || strlen($username) > 25) {
        array_push($errors_array, "Username must be between 5 and 25 characters!");
    }
    else if(mysqli_num_rows($query) > 0){
        array_push($errors_array, "Username already exists!");
    } 


    $password = $database->escape_string($_POST['reg_password']);
    if(strlen($password) < 5 || strlen($password) > 25) {
        array_push($errors_array, "Your password must be between 5 and 25 characters!");
    }
    $confirm_pass = $database->escape_string($_POST['reg_confirm']);
    if($password != $confirm_pass) {
        array_push($errors_array, "Passwords doesn't match!");
    } else {
        if(preg_match('/[^A-Za-z0-9]/', $password)) {
            array_push($errors_array, "Your password can contain only standard characters!");
        }
    }

    if(empty($errors_array)) { //insert samo ako nema errora
        $password_hash = password_hash($password, PASSWORD_BCRYPT); 

        $date = date("Y-m-d");

        //default profilna
        $rand = rand(1,4);
        if($rand == 1) {
            $prof_pic = "assets/images/profile_pictures/defaults/head_amethyst.png";
        } else if($rand == 2) {
            $prof_pic = "assets/images/profile_pictures/defaults/head_deep_blue.png";
        } else if($rand == 3) {
            $prof_pic = "assets/images/profile_pictures/defaults/head_pomegranate.png";
        } else if($rand == 4) {
            $prof_pic = "assets/images/profile_pictures/defaults/head_turqoise.png";
        }

        $reg_query = $database->query("INSERT INTO users(first_name, last_name, username, email, password, signup_date, profile_picture, num_posts, num_likes, friends_array) VALUES('{$first_name}', '{$last_name}', '{$username}', '{$email_insert}', '{$password_hash}', '{$date}', '{$prof_pic}', '0', '0', '')");
    }
}
?>