<?php ob_start();
session_start();
if(!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: register.php");
} else {
    $loggedIn = $_SESSION['username'];
}
global $database;

$name_query = $database->query("SELECT * FROM users WHERE username= '{$loggedIn}'");
$user = mysqli_fetch_array($name_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NEfeedWS</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="top_bar">
        <div class="logo">
            <a href="index.php">FeeDNewS</a>
        </div>
        <nav>
            <a id='user' href="<?php echo $loggedIn; ?>"><?php echo $user['first_name']; ?></a>
            <a href="#"><i class="fas fa-home"></i></a>
            <a href="#"><i class="fas fa-envelope"></i></a>
            <a href="#"><i class="fas fa-bell"></i></a>
            <a href="#"><i class="fas fa-users"></i></a>
            <a href="#"><i class="fas fa-cog"></i></a>
            <a id='logout_icon' href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </nav>
    </div>
    <div class="wrapper">