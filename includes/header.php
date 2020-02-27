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
    <link href="https://unpkg.com/dropzone/dist/dropzone.css" rel="stylesheet"/>
    <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
    
</head>
<body>
    <div class="top_bar">
        <div class="logo">
            <a href="index.php">FeeDNewS</a>
        </div>

        <div class="search">
            <form action="search.php" method="GET" name="search">
                <input type="text" name="q" onkeyup="getLiveSearchData(this.value, '<?php echo $loggedIn; ?>')" placeholder="Search.." autocomplete="off" id='search_input'>
                <div class="button_search">
                    <img src="assets/images/icons/images.png">
                </div>
            </form>

            <div class="search_results"></div>
            <div class="search_results_footer_empty"></div>
            <div class="search_results_footer"></div>
        </div>

        <nav>
            <?php 
                $message = new Message($loggedIn);
                $unread_msg = $message->getUnreadNumber();

                $notification = new Notification($loggedIn);
                $unread_notif = $notification->getUnreadNumber();

                $user_for_req = new User($loggedIn);
                $friend_req = $user_for_req->getRequestsNumber();
            ?>

            <a id='user' href="<?php echo $loggedIn; ?>"><?php echo $user['username']; ?></a>
            <a href="#"><i class="fas fa-home"></i></a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $loggedIn; ?>', 'message')"><i class="fas fa-envelope"></i>
                <?php
                    if($unread_msg > 0) {
                        echo "<span class='notification_msg' id='unread'> {$unread_msg} </span>";
                    }
                ?>
            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $loggedIn; ?>', 'notification')"><i class="fas fa-bell"></i>
            <?php
                if($unread_notif > 0) {
                    echo "<span class='notification_msg' id='unread_notification'> {$unread_notif} </span>";
                }
            ?>
            </a>
            <a href="requests.php"><i class="fas fa-users"></i>
            <?php
                if($friend_req > 0) {
                    echo "<span class='notification_msg' id='unread_notification'> {$friend_req} </span>";
                }
            ?>
            </a>
            <a href="#"><i class="fas fa-cog"></i></a>
            <a id='logout_icon' href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </nav>
        <div class="dropdown_data" style="height: 0px; border: none;"></div>
        <input type="hidden" id="dropdown_value" value="">
    </div>

    <!-- <script>
        var loggedIn = "<?php echo $loggedIn; ?>";

        $(document).ready(function() {

            $(".dropdown_data").scroll(function() {
                var inner_height = $('.dropdown_data').innerHeight();
                var scrollTop = $('.dropdown_data').scrollTop();
                var page = $('.dropdown_data').find('.nextPageDropdownData').val();
                var noData = $('.dropdown_data').find('.noMoreDropdownData').val();

                if((scrollTop + inner_height >= $(".dropdown_data").prop('scrollHeight')) && noData == 'false') {
                    var pageName;
                    var type = $(".dropdown_value").val();

                    if(type == 'notification') {
                        pageName = 'ajax_load_notifications.php';
                    } else if(type = 'message') {
                        pageName = 'ajax_load_messages.php';
                    }

                    $.ajax({
                        url: pageName,
                        type: "POST",
                        data: "page="+ page + "&loggedIn=" + loggedIn,
                        cache: false, 

                        success: function(response) {
                            $('.dropdown_data').find('.nextPageDropdownData').remove();
                            $('.dropdown_data').find('.noMoreDropdownData').remove();

                            $('.dropdown_data').append(response);
                        }
                    });
                }
                return false;
            });
        });
    </script> -->

    <div class="wrapper">