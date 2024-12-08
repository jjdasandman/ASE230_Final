<?php
include '../utils.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $posts = loadPostsFromJSON('../posts.json');
    $post = getPost($posts, $post_id);

    if ($post && getCurrentUser() === $post['author']) {
        unset($posts[$post_id]);
        savePostsToJSON('../posts.json', array_values($posts)); // Reindex array
    }
}

header('Location: index.php');
exit();
?>
