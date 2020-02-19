<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

require_once("config/init.php");
global $database;
if(isset($_GET['profile_user'])) {
    $username = $_GET['profile_user'];
    $get_user_query = $database->query("SELECT * FROM users WHERE username = '{$username}'");
    $user = mysqli_fetch_array($get_user_query);

    $count_friends = substr_count($user['friends_array'], ",");
}

$user_profile_obj = new User($username);
$loggedIn_user = new User($loggedIn);
?>

<?php
if(isset($_POST['add_friend'])) {
    $loggedIn_user->addFriend($username);
}

if(isset($_POST['remove_friend'])) {
    $loggedIn_user->removeFriend($username);
}

if(isset($_POST['request_received'])) {
    header("Location: requests.php");
}
?>

    <style>
        .wrapper {
            margin-left: 0px;
            padding-left: 0px;
            min-height: 130vh;
        }

        .user_profile {
            min-height: 100vh;
            overflow: auto;
            display: block;
        }
    </style>
    <div class="user_profile">
        <img src="<?php echo $user['profile_picture'] ?>" height="80px" alt="profile_user_<?php echo $username; ?>" id='profile_img' >
        <span id='name'><?php echo $user['first_name'] . " " . $user['last_name']; ?></span>
        <?php  if($user['username'] == $loggedIn){ ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="file" id="file" class="inputfile" />
                <label for="file" class='label_file'>Upload image</label>
            </form>
        <?php } ?>
        <div class="user_info">
            <p><?php echo "Posts: " . $user['num_posts']; ?></p>
            <p><?php echo "Friends: " . $count_friends; ?></p>
            <p><?php echo "Signup date: " . Carbon::create($user['signup_date'])->diffForHumans(); ?></p>
            <p><?php if($loggedIn != $username) echo "<br><span id='mutual'data-toggle='modal' data-target='#mutual_friends'>Mutual Friends: " . $loggedIn_user->mutualFriends($username) . "</span>";?></p>
        </div>

        <?php 
            if($user_profile_obj->isClosed()) {
                header("Location: user_closed.php");
            }
            if($loggedIn != $username) {
                if($loggedIn_user->isFriend($username)) {
                    echo "<br><form action='{$username}' method='POST'>
                            <input type='submit' name='remove_friend' class='remove_friend' value='Remove Friend'>
                        </form>";
                } else if($loggedIn_user->didSentRequest($username)) {
                    echo "<br><form action='{$username}' method='POST'>
                            <input type='submit' name='request_sent' class='request_sent' value='Request Sent'>
                        </form>";
                } else if($loggedIn_user->didReceiveRequest($username)) {
                    echo "<br><form action='{$username}' method='POST'>
                            <input type='submit' name='request_received' class='request_received' value='Respond To Request'>
                        </form>";
                } else {
                    echo "<br><form action='{$username}' method='POST'>
                            <input type='submit' name='add_friend' class='add_friend' value='Add Friend'>
                        </form>";
                }    
        ?>
        <br>
        <input type="submit" class='request_received' data-toggle='modal' data-target="#post_form" value="Post to <?php echo $username; ?>">
            <?php } ?>
    </div>


    <?php if($loggedIn_user->mutualFriends($username) > 0) { ?>
        <div class="modal fade" id="mutual_friends" tabindex="-1" role="dialog" aria-labelledby="mutualFriendsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">You and <?php echo $user_profile_obj->getFirstAndLastName(); ?> have mutual friends! </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php 
                       $mutual_friends = $loggedIn_user->displayMutualFriends($username); 
                       foreach($mutual_friends as $friend) {
                        $mutual_friend = new User($friend);
                        if($mutual_friend->getUsername()) { 
                           echo "
                           <div class='mutual_friends_list'>
                                <div class='friend_pic'>
                                    <img src='{$mutual_friend->getProfileImage()}'>
                                </div>
                                <div class='friend_info'>
                                    <a href='{$mutual_friend->getUsername()}' id='modal_href'>{$mutual_friend->getFirstAndLastName()}</a> ({$mutual_friend->getUsername()})
                                </div>
                           </div>
                           <hr>
                           ";
                       } 
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="main_prof col">
        <div class='posts_lazy'></div>
        <img src="assets/images/icons/loading.gif" id="loading"> 
    </div>
    
    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Post Something! </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tell friend what's on your mind!</p>
                <form action="" class="profile_post" method="POST">
                    <div class="form-group">
                        <textarea name="post_content" class="form-control"></textarea>
                        <input type="hidden" name="user_from" value="<?php echo $loggedIn; ?>">
                        <input type="hidden" name="user_to" value="<?php echo $username; ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" name="submit" id="submit_timeline" class="btn btn-primary">Post!</button>
            </div>
            </div>
        </div>
    </div>
</div> <!-- wrapper div -->

<script>
    var loggedIn = "<?php echo $loggedIn; ?>";
    var profileUser = "<?php echo $username; ?>";

    $(document).ready(function() {
        $("#loading").show();

        $.ajax({
            url: "lazy_load_user_profile.php",
            type: "POST",
            data: "page=1&loggedIn=" + loggedIn + "&profileUser=" + profileUser,
            cache: false, 

            success: function(data) {
                $("#loading").hide();
                $(".posts_lazy").html(data);
            }
        });

        $(window).scroll(function() {
            var height = $(".posts_lazy").height();
            var scrollTop = $(this).scrollTop();
            var page = $('.posts_lazy').find('.nextPage').val();
            var noPostsLeft = $('.posts_lazy').find('.noPostsLeft').val();

            if((document.body.scrollHeight == scrollTop + window.innerHeight) && noPostsLeft == 'false') {
                $("#loading").show();

                $.ajax({
                    url: "lazy_load_user_profile.php",
                    type: "POST",
                    data: "page="+ page + "&loggedIn=" + loggedIn + "&profileUser=" + profileUser,
                    cache: false, 

                    success: function(response) {
                        $('.posts_lazy').find('.nextPage').remove();
                        $('.posts_lazy').find('.noPostsLeft').remove();

                        $('#loading').hide();
                        $('.posts_lazy').append(response);
                    }
                });
            }
            return false;
        });

        $("#file").change(function() {
            var username_loggedIn = "<?php echo $loggedIn; ?>";
            var data = new FormData();
            data.append('file', $("#file")[0].files[0]);
            $.ajax({
                url: "upload.php",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#profile_img').attr("src", data);
                        $('.profile_img_post').each(function() {
                            if($("#added_by").attr("href") == username) { 
                                $(this).html("<img src='" + data + "' width='60'>");
                            }
                        });
                }
            });
        });
    });
</script>