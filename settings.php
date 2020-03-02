<?php
include("config/init.php");
include("includes/form_handlers/settings_handler.php");
global $database;
?>

<?php
$pull_query = $database->query("SELECT * FROM users WHERE username = '{$loggedIn}'");
$row = mysqli_fetch_array($pull_query);
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$userame = $row['username'];

?>


<div class="main col">
    <h4>Account Settings</h4>
    <?php
        echo "<img src='{$user['profile_picture']}' id='settings_pic'>";
    ?>
    <br><br>
    <p>To update your personal information, change input fields and click Update.</p>
    <form action="" method="POST" class='settings_form'>
        <label for="first_name">First name: </label>
        <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br>
        <label for="last_name">Last name: </label>
        <input type="text" name="last_name" value="<?php echo $last_name; ?>"><br>
        <label for="email">Username: </label>
        <input type="text" name="username" value="<?php echo $userame; ?>"><br>
        <?php echo $message; ?>
        <input type="submit" value="Update" name="update_details" class='request_received settings_update'>
    </form>
    <hr>
    <h5>Change Password</h5>
    <form action="" method="POST" class="settings_form">
        <label for="old_p">Old Password</label>
        <input type="password" name="old_p"><br>
        <label for="new_p">New Password</label>
        <input type="password" name="new_p"><br>
        <label for="new_p2">New Password Again</label>
        <input type="password" name="new_p2"><br>
        <?php echo $message2; ?>
        <input type="submit" value="Update" name="update_password" class='request_received settings_update'>
    </form>
    <hr>
    <h5>Close Account</h5>
    <form action="" method="POST">
        <input type="submit" value="Close Account" name="close_account" id='close_account' class="remove_friend settings_update">
    </form>
</div>