<?php
include("config/init.php");

if(isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}
?>

<div class="user_det col">
    <a href="<?php echo $loggedIn; ?>"><img src="<?php echo $user['profile_picture']; ?>"></a>
    <div class="user_det_lt_rt">
        <a href="<?php echo $loggedIn; ?>">
            <?php echo $user['first_name'] . " " . $user['last_name']; ?>
        </a>
        <br>
        <?php echo 'Likes: ' . $user['num_likes'] . '<br>';
            echo 'Posts: '. $user['num_posts'] . '<br>'  ?>
    </div>
</div>
<div class="main col">
    <div class="posts_area">
        <?php 
            $post = new Post($loggedIn);
            $post->showPost($id);
        ?>
    </div>
</div>