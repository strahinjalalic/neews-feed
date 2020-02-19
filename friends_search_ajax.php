<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NEfeedWS</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://unpkg.com/dropzone/dist/dropzone.css" rel="stylesheet"/>
    <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
</head>


<?php include("config/define.php"); 
global $database;

$query = $_POST['query'];
$loggedIn = $_POST['loggedIn'];

$full_names = explode(" ", $query);
if(count($full_names) == 2) { //ime i prezime
    $find_user = $database->query("SELECT * FROM users WHERE (first_name LIKE '%{$full_names[0]}%' AND last_name LIKE '%{$full_names[1]}%') AND user_closed = 'no' LIMIT 3");
} else {
    $find_user = $database->query("SELECT * FROM users WHERE (first_name LIKE '%{$full_names[0]}%' OR last_name LIKE '%{$full_names[0]}%') AND user_closed = 'no' LIMIT 3");
}

if($query != "") {
    while($row = mysqli_fetch_array($find_user)) {
        $user = new User($loggedIn);
        if($row['username'] != $loggedIn) {
            $mutual_friends = $user->mutualFriends($row['username']) . " friends in common";
        } else {
            $mutual_friends = "";
        }

        if($user->isFriend($row['username'])) {
            echo "<div class='display_friend'>
                    <a href='messages.php?u=" . $row['username'] ."' style='color:#000;'>
                      <div class='live_search_img'>
                        <img src='". $row['profile_picture'] ."'>
                      </div>
                      <div class='live_search_text'>
                        " . $row['first_name'] . " " . $row['last_name'] . "<p>" . $row['username'] . "</p>
                        <p id='grey' style='padding-top:6px;'> " . $mutual_friends . "</p>
                      </div>
                    </a>  
                 </div>";
        }
    }
}

?>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="https://unpkg.com/dropzone"></script>
<script src="https://unpkg.com/cropperjs"></script>
<script src="https://kit.fontawesome.com/3e57996fc5.js" crossorigin="anonymous"></script>
</body>
</html>
