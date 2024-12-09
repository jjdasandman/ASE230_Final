<?php
include_once '../utils.php';
include_once '../db_connection.php'; // Ensure you have a DB connection

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
    <style>
        /* Navbar Styles */
.navbar {
    background-color: #003DA5; /* Chelsea blue */
    padding: 10px 15px;
}

.navbar .navbar-brand,
.navbar .nav-link {
    color: white;
    font-weight: bold;
}

.navbar .nav-link:hover {
    color: #cce5ff; /* Lighter blue */
    text-decoration: underline;
}

.navbar .nav-item.special a {
    color: #FFD700; /* Gold color for special links */
    font-weight: bold;
}

.navbar .nav-item.special a:hover {
    color: #ffeb3b; /* Lighter gold */
}

.nav-item.sign-out a {
    color: #ff4d4d; /* Red for Sign Out */
    font-weight: bold;
}

.nav-item.sign-out a:hover {
    color: #ff6666; /* Lighter red */
}

/* Post Detail Section */
.container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #f9f9f9; /* Light gray background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #003DA5; /* Matching the navbar color */
    font-size: 2em;
    margin-bottom: 10px;
}

p em {
    color: #6c757d; /* Muted text color for metadata */
    font-size: 0.9em;
}

.post-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.post-buttons .btn {
    width: 150px;
    padding: 10px;
    border-radius: 5px;
    font-size: 0.9em;
    font-weight: bold;
}

.post-buttons .btn-secondary {
    background-color: #6c757d;
    color: white;
}

.post-buttons .btn-warning {
    background-color: #ffc107;
    color: black;
}

.post-buttons .btn-danger {
    background-color: #dc3545;
    color: white;
}

/* Image Container */
.image-container {
    width: 100%; /* Full width of the container */
    max-width: 300px; /* Ensure a maximum size */
    height: auto; /* Maintains aspect ratio */
    margin: 20px auto; /* Center the image */
    border: 1px solid #ddd; /* Add a subtle border */
    border-radius: 8px;
    overflow: hidden;
}

.image-container img {
    width: 100%; /* Ensures the image scales */
    height: 100%; /* Ensures it fits within the container */
    object-fit: cover; /* Keeps aspect ratio and crops excess */
    display: block;
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar .nav-link {
        font-size: 0.9em;
    }

    h1 {
        font-size: 1.5em;
    }

    .post-buttons .btn {
        width: 100px;
        font-size: 0.8em;
    }

    .image-container {
        max-width: 250px;
    }
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Closet Manager</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home</a>
        </li>
        <li class="nav-item special">
          <a class="nav-link" href="../profile/index.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../profile/settings.php">Settings</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item sign-out">
          <a href="../auth/logout.php" class="nav-link">Sign Out</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

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
