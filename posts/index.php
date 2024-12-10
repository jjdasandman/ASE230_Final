<?php
session_start();
include_once '../utils.php';
include_once '../db_connection.php';
include_once 'navbar.php';
//$username = $_SESSION['username'];
$isAdmin = isset($_SESSION['isValidAdmin']) && $_SESSION['isValidAdmin'] ? true : false;
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Fetch posts from the database
$posts = loadPostsFromDatabase($db);

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}

// Handle sorting logic
if (isset($_POST['sort'])) {
    $sortOption = $_POST['sort'];

    switch ($sortOption) {
        case 'date_asc':
            usort($posts, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
            break;
        case 'date_desc':
            usort($posts, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            break;
        case 'author_az':
            usort($posts, function ($a, $b) {
                return strcmp($a['username'], $b['username']);
            });
            break;
        case 'author_za':
            usort($posts, function ($a, $b) {
                return strcmp($b['username'], $a['username']);
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
    <link rel="stylesheet" href="../css/styles.css">

    
</head>

<body>
    

    <div class="container">
        <div class="jumbotron mt-4">
            <h1 class="display-4">Welcome to Your Closet Manager</h1>
            <p class="lead">Organize, analyze, and optimize your wardrobe with ease. Tag your clothes by color, brand,
                type, and material.</p>
        </div>

        <h2 class="my-4">Your Wardrobe</h2>


        <!-- Authentication message -->
        <div class="mb-4">
            <?php if (isLoggedIn()) { ?>
                <p>Welcome, <?php print ($username) ?>!</p>
            <?php } ?>
        </div>

        <!-- Sort Dropdown -->
        <form method="POST" class="form-inline mb-4">
            <label for="sort" class="mr-2">Sort By:</label>
            <select name="sort" id="sort" class="form-control mr-2">
                <option value="date_asc">Date (Oldest First)</option>
                <option value="date_desc">Date (Newest First)</option>
                <option value="author_az">Author (A-Z)</option>
                <option value="author_za">Author (Z-A)</option>
            </select>
            <button type="submit" class="btn btn-primary">Sort</button>
        </form>

        <div class="row">
			<?php foreach ($posts as $post): ?>
				<div class="col-md-4">
					<div class="card">
						<img src="../uploads/<?php echo htmlspecialchars($post['photo_url']); ?>" class="card-img-top"
							alt="Post Image">
						<div class="card-body">
							<h5 class="card-title">
								<a href="detail.php?post_id=<?php echo $post['post_id']; ?>">
									<?php echo htmlspecialchars($post['title']); ?>
								</a>
							</h5>
							<p class="card-text">By <?php echo htmlspecialchars($post['username']); ?> on
								<?php echo htmlspecialchars(date('Y-m-d', strtotime($post['created_at']))); ?>
							</p>
							<p class="card-text">
								<?php echo htmlspecialchars(substr($post['description'], 0, 100)) . '...'; ?>
							</p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</body>

</html>