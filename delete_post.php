<?php include("config/define.php");
global $database;

if(isset($_GET['post_id']) && isset($_GET['added_by'])) {
    $id = $_GET['post_id'];
    $added_by = $_GET['added_by'];

    $count_likes_query = $database->query("SELECT likes FROM posts WHERE id = {$id}");
    $row = mysqli_fetch_array($count_likes_query);
    $post_likes = $row['likes'];

    $delete_from_posts = $database->query("DELETE FROM posts WHERE id = {$id}");
    $delete_from_likes = $database->query("DELETE FROM likes WHERE post_id = {$id}");
    $delete_from_users = $database->query("UPDATE users SET num_likes = num_likes - {$post_likes}, num_posts = num_posts - 1 WHERE username = '{$added_by}'");
    header("Location: index.php");
}

?>