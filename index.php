<?php require_once("config/init.php"); ?>

<?php if(isset($_POST['submit_post'])) {
    $post = new Post($loggedIn);
    $body = $_POST['post_content'];
    $post->submitPost($body, 'none');
} ?>

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
        <form action="" class='index_form' method="POST">
            <textarea name="post_content" placeholder="Share with friends!"></textarea>
            <input type="submit" value="Post" name="submit_post">
            <hr>
        </form>
        <?php $posts_to_load = new Post($loggedIn);
        $posts_to_load->postsByFriends();  ?>
    </div>
</div>