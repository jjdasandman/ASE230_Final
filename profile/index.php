<?php

include '../utils.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$posts = loadPostsFromJSON('../posts.json');


// Function to display the user's posts
function displayUserPosts($posts, $username) {
    foreach ($posts as $index => $post) {
        if ($post['author'] === $username) {
            $post_id = $index; // Use the index as the post ID
            echo "<div class='card my-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'><a href='../posts/detail.php?post_id={$post_id}'>{$post['title']}</a></h5>";
            echo "<p class='card-text'>By {$post['author']} on {$post['date']}</p>";
            echo "</div>";
            echo "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - <?php echo htmlspecialchars($username); ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #003DA5; /* Chelsea blue */
        }
        .navbar .navbar-brand,
        .navbar .nav-link {
            color: white; 
        }
        .navbar .nav-link:hover {
            color: #cce5ff; 
        }
        .navbar .nav-item.special a {
            color: #FFD700; /* Gold color  */
        }
        .navbar .nav-item.special a:hover {
            color: #ffeb3b; /* Lighter gold  */
        }
        .nav-item.sign-out a {
            color: red; /* Red color for Sign Out */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Your Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../posts/index.php">Home</a>
                </li>
                <li class="nav-item special">
                    <a class="nav-link" href="../profile/index.php">Profile</a>
                </li>
                <li class="nav-item special">
                    <a class="nav-link" href="../profile/settings.php">Settings</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item sign-out">
                    <a class="nav-link" href="../auth/logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="my-4">
        <h1>User Profile</h1>
        <p class="lead">Welcome, <?php echo ucfirst(htmlspecialchars($username)); ?>!</p>
    </div>

    <div>
        <h2>Your Posts</h2>
        <?php displayUserPosts($posts, $username); ?>
    </div>

    <div class="mt-4">
        <a href="../posts/index.php" class="btn btn-primary">Back to Blog</a>
        <a href="../profile/settings.php" class="btn btn-secondary">Account Settings</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
