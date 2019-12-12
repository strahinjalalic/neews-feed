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

        <div class='posts_lazy'></div>
        <img src="assets/images/icons/loading.gif" id="loading">
    </div>


    <script>
        var loggedIn = "<?php echo $loggedIn; ?>";

        $(document).ready(function() {
            $("#loading").show();

            $.ajax({
                url: "lazy_load_ajax.php",
                type: "POST",
                data: "page=1&loggedIn=" + loggedIn,
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
                        url: "lazy_load_ajax.php",
                        type: "POST",
                        data: "page="+ page + "&loggedIn=" + loggedIn,
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
        });
    </script>
</div>