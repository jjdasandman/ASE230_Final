<?php
include '../utils.php';
include_once '../db_connection.php'; // Include DB connection
include_once $_SERVER['DOCUMENT_ROOT'] . '/Final2/posts/navbar.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch the current logged-in user's details (username and role)
$currentUser = getCurrentUser($db); 
$currentUsername = $currentUser['username']; // Logged-in user's username
$currentUserRole = getCurrentUserRole($db); // Get the logged-in user's role

// Check if post_id is set in the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Fetch the post from the database
    $query = $db->prepare("SELECT * FROM clothingpost WHERE post_id = :post_id");
    $query->execute(['post_id' => $post_id]);
    $post = $query->fetch(PDO::FETCH_ASSOC);

    // Check if the post exists and if the current user is either the author or an admin
    if (!$post || 
        ($post['username'] !== $currentUsername && $currentUserRole !== 'admin')) {
        echo "Unauthorized access.";
        exit();
    }

    // If the post has a photo, delete it from the uploads folder
    if ($post['photo_url'] && file_exists("../uploads/" . $post['photo_url'])) {
        unlink("../uploads/" . $post['photo_url']); // Delete the photo file
    }

    // Delete the post from the database
    $deleteQuery = $db->prepare("DELETE FROM clothingpost WHERE post_id = :post_id");
    $deleteQuery->execute(['post_id' => $post_id]);
}

header('Location: index.php'); // Redirect back to index.php after deletion
exit();
?>
