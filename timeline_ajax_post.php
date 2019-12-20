<?php include("config/define.php");
global $database;

if(isset($_POST['post_content'])) {
    $post_content = $_POST['post_content'];
    if(!empty($post_content)) {
        $post = new Post($_POST['user_from']);
        $post->submitPost($post_content, $_POST['user_to']);
    }
}

?>