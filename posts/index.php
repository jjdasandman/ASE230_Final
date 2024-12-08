<?php
include '../utils.php';
$posts = loadPostsFromJSON('../posts.json');
// echo '<pre>';
// print_r($posts);
// echo '</pre>';
// Handle sorting logic
if (isset($_POST['sort'])) {
    $sortOption = $_POST['sort'];
    
    switch ($sortOption) {
        case 'date_asc':
            usort($posts, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            break;
        case 'date_desc':
            usort($posts, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            break;
        case 'author_az':
            usort($posts, function($a, $b) {
                return strcmp($a['author'], $b['author']);
            });
            break;
        case 'author_za':
            usort($posts, function($a, $b) {
                return strcmp($b['author'], $a['author']);
            });
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blog Posts</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
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


<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Closet Manager</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item special">
          <a class="nav-link" href="../profile/index.php">Profile</a> 
        </li>
        <?php if (isLoggedIn()) { ?>
        
          <li class="nav-item special">
            <a class="nav-link" href="../posts/create.php">Create New Post</a>
          </li>
          <li class="nav-item special">
            <a class="nav-link" href="../profile/settings.php">Settings</a>
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
    <div class="jumbotron mt-4">
        <h1 class="display-4">Welcome to Your Closet Manager</h1>
        <p class="lead">Organize, analyze, and optimize your wardrobe with ease. Tag your clothes by color, brand, type, and material.</p>
    </div>

    <h2 class="my-4">Your Wardrobe</h2>

    <!-- Authentication message -->
    <div class="mb-4">
        <?php if (isLoggedIn()) { ?>
            <p>Welcome, <?php echo ucfirst(getCurrentUser()); ?>!</p>
        <?php } ?>
    </div>

    <!-- sort Dropdown -->
    <div class="row mb-4">
        <div class="col-md-6">
            <!-- Sort dropdown -->
            <form method="POST" class="form-inline">
                <div class="form-group">
                    <label for="sort" class="mr-2">Sort By:</label>
                    <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
                        <option value="">Choose...</option>
                        <option value="color">Color</option>
                        <option value="brand">Brand</option>
                        <option value="type">Type</option>
                        <option value="material">Material</option>
                    </select>
                </div>
            </form>
        </div>
       
    </div>

    <!-- Display clothes in a grid -->
	<div class="row">
		<?php foreach ($posts as $post): ?>
			<div class="col-md-4">
				<div class="card">
					<?php if (isset($post['image']) && !empty($post['image'])): ?>
						<img src="<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="Post Image">
					<?php else: ?>
						<img src="../uploads/default.png" class="card-img-top" alt="Default Image"> <!-- Set a default image path -->
					<?php endif; ?>
					<div class="card-body">
						<h5 class="card-title"><a href="detail.php?post_id=<?php echo ($post['id'] - 1); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h5>
						<p class="card-text">By <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['date']); ?></p>
						<p class="card-text"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

</body>
</html>