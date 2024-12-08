<?php
include '../utils.php';

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $posts = loadPostsFromJSON('../posts.json');
    $post = getPost($posts, $post_id);

    if (!$post) {
        echo "Post not found.";
        exit();
    }
} else {
    echo "No post specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
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
    <a class="navbar-brand" href="#">Closet Manager</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item special">
          <a class="nav-link" href="../profile/index.php">Profile</a> 
        </li>
        <?php if (isLoggedIn()) { ?>
          <li class="nav-item special">
            <a class="nav-link" href="../profile/settings.php">Settings</a>
          </li>

          <li class="nav-item special">
            <a class="nav-link" href="../posts/create.php">Create New Post</a>
          </li>
        <?php } ?>
      </ul>

      <!-- Authentication buttons in the navbar -->
      <ul class="navbar-nav ms-auto"> 
        <?php if (isLoggedIn()) { ?>
            <li class="nav-item sign-out">
                <a href="../auth/logout.php" class="nav-link">Sign Out</a>
            </li>
        <?php } else { ?>
            <li class="nav-item">
                <a href="../auth/login.php" class="nav-link">Sign In</a>
            </li>
            <li class="nav-item">
                <a href="../auth/signup.php" class="nav-link">Sign Up</a>
            </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
	<a href="index.php" class="btn btn-secondary mt-3">Back to Blog Index</a>
    <h1 class="my-4"><?php echo htmlspecialchars($post['title']); ?></h1>
    <p>By <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['date']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <!-- Displaying the uploaded image if it exists -->
    <?php if (isset($post['image']) && !empty($post['image'])): ?>
        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="img-fluid mt-4">
    <?php else: ?>
        <img src="../uploads/default.png" alt="Default Image" class="img-fluid mt-4">
    <?php endif; ?>

    <!-- Buttons to edit and delete (only for the author) -->
    <?php if (isLoggedIn() && getCurrentUser() === $post['author']) { ?>
        <a href="edit.php?post_id=<?php echo htmlspecialchars($post_id); ?>" class="btn btn-warning">Edit Post</a>
        <a href="delete.php?post_id=<?php echo htmlspecialchars($post_id); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete Post</a>
    <?php } ?>
</div>

</body>
</html>
