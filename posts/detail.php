<?php
include_once '../utils.php';
include_once '../db_connection.php';
include_once 'navbar.php';

if (!isset($_SESSION)) { 
    session_start(); 
}

// Get the post ID from the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    
    // Fetch the post from the database
    $post = getPostById($db, $post_id);
    
    if ($post === null) {
        echo "Post not found!";
    } else {
        // Proceed to display the post details
    }
} else {
    echo "No post ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post) ? htmlspecialchars($post['title']) : 'Post Details'; ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
</head>
<body>

<!-- Post Detail Section -->
<div class="container mt-4 text-center">
    <?php if (isset($post)): ?>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><em>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></em></p>
        <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
        
        <div class="image-container">
        <?php if (!empty($post['photo_url'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($post['photo_url']); ?>" alt="Post Image" class="post-image">
        <?php endif; ?>
        </div>

        <!-- Buttons -->
        <div class="post-buttons">
            <a href="index.php" class="btn btn-secondary">Back to Blog</a>

            <?php 
            $currentUser = getCurrentUser($db); // Fetch current user info
            $userRole = getCurrentUserRole($db); // Fetch the current user's role
            if ($currentUser && isLoggedIn() && ($currentUser['username'] === $post['username'] || $userRole === 'admin')): ?>
                <a href="edit.php?post_id=<?php echo htmlspecialchars($post['post_id']); ?>" class="btn btn-warning">Edit Post</a>
                <a href="delete.php?post_id=<?php echo htmlspecialchars($post['post_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete Post</a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p>Post not found!</p>
    <?php endif; ?>
</div>

</body>
</html>