<?php
require 'vendor/autoload.php';
use Carbon\Carbon; 

class Post
{
    private $user_object;

    public function __construct($user)
    {
        $this->user_object = new User($user);
    }

    public function submitPost($body, $user_to, $img_name)
    {
        global $database;
        $body = $database->escape_string($body);
        $delete_spaces = preg_replace('/\s+/', '', $body);

        if ($delete_spaces != '') { //ako ima sadrzaja
            $date_added = Carbon::now()->toDateTimeString();
            $added_by = $this->user_object->getUsername();

            if($user_to == $added_by) { //ako je korisnik na svom profilu
               $user_to = "none";
            }   

            $insert = $database->query("INSERT INTO posts VALUES('', '{$body}', '{$added_by}', '{$user_to}', '{$date_added}', 'no', 'no', '0', '{$img_name}')");
            $last_id = mysqli_insert_id($database->db_connection());

            if($user_to != 'none') {
                $notification = new Notification($added_by);
                $notification->insertNotification($last_id, $user_to, 'profile_post');
            }

            $increment_user_posts = $this->user_object->incrementUserPosts();
        }
    }

    public function postsByFriends($data, $limit) {
        global $database;
        $str_posts = "";
        $page = $data['page'];

        if($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $posts = $database->query("SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");
        if(mysqli_num_rows($posts) > 0) {
            $iterations = 0; 
            $count = 1;

            while($row = mysqli_fetch_array($posts)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $user_to = $row['user_to'];
                $date_added = $row['date_added'];
                $image = $row['image'];
                
                if($user_to != "none") { //postavljanje linka za profil user-a na cijem se profilu pise post
                    $user_to_instance = new User($user_to);
                    $user_to_name = $user_to_instance->getFirstAndLastName();
                    $user_to = "to <a href='" . $user_to . "'>{$user_to_name}</a>";
                } else {
                    $user_to = "";
                }

                $added_by_obj = new User($added_by);
                if($added_by_obj->isClosed()) { //ako je profil ugasen, ne prikazuj postove
                    continue;
                }

                if($this->user_object->isFriend($added_by)) { //pozivamo funkciju ovde, radi boljeg izvrsavanja koda

                    if($iterations++ < $start) { //da se ne bi svi load-ovani postovi iznova ucitavali
                        continue;
                    }

                    if($count > $limit) { //izvlacimo 10 postova, cim predje limit iskace iz petlje
                        break;
                    } else {
                        $count++;
                    }

                    $post_user_info = $database->query("SELECT first_name, last_name, profile_picture FROM users WHERE username = '{$added_by}'");
                    $user_row = mysqli_fetch_array($post_user_info);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_img = $user_row['profile_picture'];
                    ?>

                    <script>
                        function toggle<?php echo $id?>() {
                            var target = $(event.target);
                            if(!target.is("a")) {
                                var element = document.getElementById("toggleComment<?php echo $id?>");
                            if(element.style.display == "block") {
                                element.style.display = 'none';
                            } else {
                                element.style.display = 'block';
                            }
                          }  
                        }
                    </script>

                    <?php
                    $num_comments_query = $database->query("SELECT * FROM comments WHERE post_id = {$id}");
                    $num_comments = mysqli_num_rows($num_comments_query);
                    $count_likes_query = $database->query("SELECT * FROM likes WHERE post_id = {$id}");
                    $count_likes = mysqli_num_rows($count_likes_query);
                    $date_added_full = Carbon::create($date_added)->diffForHumans();
                    
                    if($image != "") {
                        $image_div = "<div class='post_img'>
                                        <img src='{$image}'>
                                    </div>";
                    } else {
                        $image_div = "";
                    }

                    $str_posts .= "
                                    <div class='posts'>
                                        <div class='profile_img_post'>
                                        <img src='{$profile_img}' width='60'>
                                        </div>
                                        <div class='posted_by' style='color:#acacac'>
                                            <a href='{$added_by}'> {$first_name} {$last_name} </a> {$user_to} &nbsp;&nbsp;&nbsp;&nbsp; <span class='date_added'>{$date_added_full}</span>
                                        </div>
                                        <div id='post_content'>
                                            {$body}
                                            <br>
                                            {$image_div}
                                            <br>
                                            <br>
                                        </div>
                                        <div class='comm_likes'>
                                            <span id='comment_toggle' onclick='javascript:toggle{$id}()'>Comments({$num_comments})</span> &nbsp;&nbsp;&nbsp;
                                            <iframe src='like.php?post_id={$id}&username={$added_by}' scrolling='no'></iframe> &nbsp;&nbsp;&nbsp;&nbsp;
                                           "; 
                                            if($added_by == $this->user_object->getUsername()) $str_posts .= "<a class='delete_post' href='delete_post.php?post_id={$id}&added_by={$added_by}'>Delete</a>";
                                            $str_posts .= "
                                        </div>
                                    </div>
                                    <div class='post_comment' id='toggleComment{$id}' style='display:none'>
                                        <iframe src='comments.php?post_id={$id}' id='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                    <hr>
                                    "; 
                }
            }   if($count > $limit) {
                    $str_posts .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noPostsLeft' value='false'>";
                    } else {
                        $str_posts .= "<input type='hidden' class='noPostsLeft' value='true'><p style='text-align:centre;'>No More Posts To Show!</p>";
                }
            echo $str_posts;
        }
    }

    public function profilePosts($data, $limit) {
        global $database;
        $str_posts = "";
        $page = $data['page'];
        $profile_user = $data['profileUser'];

        if($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $posts = $database->query("SELECT * FROM posts WHERE deleted = 'no' AND ((added_by = '{$profile_user}' AND user_to = 'none') OR user_to = '{$profile_user}') ORDER BY id DESC");
        if(mysqli_num_rows($posts) > 0) {
            $iterations = 0; 
            $count = 1;

            while($row = mysqli_fetch_array($posts)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $user_to = $row['user_to'];
                $date_added = $row['date_added'];

                if($iterations++ < $start) { //da se ne bi svi load-ovani postovi iznova ucitavali
                    continue;
                }

                if($count > $limit) { //izvlacimo 10 postova, cim predje limit iskace iz petlje
                    break;
                } else {
                    $count++;
                }

                $post_user_info = $database->query("SELECT first_name, last_name, profile_picture FROM users WHERE username = '{$added_by}'");
                $user_row = mysqli_fetch_array($post_user_info);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_img = $user_row['profile_picture'];
                ?>

                <script>
                    function toggle<?php echo $id?>() {
                        var target = $(event.target);
                        if(!target.is("a")) {
                            var element = document.getElementById("toggleComment<?php echo $id?>");
                        if(element.style.display == "block") {
                            element.style.display = 'none';
                        } else {
                            element.style.display = 'block';
                        }
                        }  
                    }
                </script>

                <?php
                $num_comments_query = $database->query("SELECT * FROM comments WHERE post_id = {$id}");
                $num_comments = mysqli_num_rows($num_comments_query);
                $count_likes_query = $database->query("SELECT * FROM likes WHERE post_id = {$id}");
                $count_likes = mysqli_num_rows($count_likes_query);
                $date_added_full = Carbon::create($date_added)->diffForHumans();

                $str_posts .= "
                            <div class='posts'>
                                <div class='profile_img_post'>
                                    <img src='{$profile_img}' width='60' id='profile_img_posts'>
                                </div>
                                <div class='posted_by' style='color:#acacac'>
                                    <a href='{$added_by}' id='added_by'> {$first_name} {$last_name} </a> &nbsp;&nbsp;&nbsp;&nbsp; <span class='date_added'>{$date_added_full}</span>
                                </div>
                                <div id='post_content'>
                                    {$body}
                                    <br>
                                    <br>
                                    <br>
                                </div>
                                <div class='comm_likes'>
                                    <span id='comment_toggle' onclick='javascript:toggle{$id}()'>Comments({$num_comments})</span> &nbsp;&nbsp;&nbsp;
                                    <iframe src='like.php?post_id={$id}&username={$added_by}' scrolling='no'></iframe> &nbsp;&nbsp;&nbsp;&nbsp;
                                    "; 
                                    if($added_by == $this->user_object->getUsername()) $str_posts .= "<a class='delete_post' href='delete_post.php?post_id={$id}&added_by={$added_by}'>Delete</a>";
                                    $str_posts .= "
                                </div>
                            </div>
                            <div class='post_comment' id='toggleComment{$id}' style='display:none'>
                                <iframe src='comments.php?post_id={$id}' id='comment_iframe' frameborder='0'></iframe>
                            </div>
                            <hr>
                            "; 
                
            }   if($count > $limit) {
                    $str_posts .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noPostsLeft' value='false'>";
                    } else {
                        $str_posts .= "<input type='hidden' class='noPostsLeft' value='true'><p style='text-align:centre;'>No More Posts To Show!</p>";
                }
            echo $str_posts;
        } else {
            echo "There Are No Posts!";
        }
    }

    public function showPost($id) {
        global $database;
        $str_posts = "";
        $loggedIn = $this->user_object->getUsername();
        $update_notifications = $database->query("UPDATE notifications SET opened = 'yes' WHERE user_to = '{$loggedIn}' AND link LIKE '%=$id'");

        $posts = $database->query("SELECT * FROM posts WHERE deleted = 'no' AND id = {$id}");
        if(mysqli_num_rows($posts) > 0) {
            $row = mysqli_fetch_array($posts);
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $user_to = $row['user_to'];
            $date_added = $row['date_added'];
                
                if($user_to != "none") { 
                    $user_to_instance = new User($user_to);
                    $user_to_name = $user_to_instance->getFirstAndLastName();
                    $user_to = "to <a href='" . $user_to . "'>{$user_to_name}</a>";
                } else {
                    $user_to = "";
                }

                $added_by_obj = new User($added_by);
                if($added_by_obj->isClosed()) { 
                    return;
                }

                if($this->user_object->isFriend($added_by)) { 
                    $post_user_info = $database->query("SELECT first_name, last_name, profile_picture FROM users WHERE username = '{$added_by}'");
                    $user_row = mysqli_fetch_array($post_user_info);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_img = $user_row['profile_picture'];
                    ?>

                    <script>
                        function toggle<?php echo $id?>() {
                            var target = $(event.target);
                            if(!target.is("a")) {
                                var element = document.getElementById("toggleComment<?php echo $id?>");
                            if(element.style.display == "block") {
                                element.style.display = 'none';
                            } else {
                                element.style.display = 'block';
                            }
                          }  
                        }
                    </script>

                    <?php
                    $num_comments_query = $database->query("SELECT * FROM comments WHERE post_id = {$id}");
                    $num_comments = mysqli_num_rows($num_comments_query);
                    $count_likes_query = $database->query("SELECT * FROM likes WHERE post_id = {$id}");
                    $count_likes = mysqli_num_rows($count_likes_query);
                    $date_added_full = Carbon::create($date_added)->diffForHumans();

                    $str_posts .= "
                                    <div class='posts'>
                                        <div class='profile_img_post'>
                                        <img src='{$profile_img}' width='60'>
                                        </div>
                                        <div class='posted_by' style='color:#acacac'>
                                            <a href='{$added_by}'> {$first_name} {$last_name} </a> {$user_to} &nbsp;&nbsp;&nbsp;&nbsp; <span class='date_added'>{$date_added_full}</span>
                                        </div>
                                        <div id='post_content'>
                                            {$body}
                                            <br>
                                            <br>
                                            <br>
                                        </div>
                                        <div class='comm_likes'>
                                            <span id='comment_toggle' onclick='javascript:toggle{$id}()'>Comments({$num_comments})</span> &nbsp;&nbsp;&nbsp;
                                            <iframe src='like.php?post_id={$id}&username={$added_by}' scrolling='no'></iframe> &nbsp;&nbsp;&nbsp;&nbsp;
                                           "; 
                                            if($added_by == $this->user_object->getUsername()) $str_posts .= "<a class='delete_post' href='delete_post.php?post_id={$id}&added_by={$added_by}'>Delete</a>";
                                            $str_posts .= "
                                        </div>
                                    </div>
                                    <div class='post_comment' id='toggleComment{$id}' style='display:none'>
                                        <iframe src='comments.php?post_id={$id}' id='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                    <hr>
                                    "; 
                } else {
                    echo "<p>You must be friend with person to see the post!</p>";
                    return;
                }
            echo $str_posts;
        } else {
            echo "<p>No post found!</p>";
        }
    }
}
