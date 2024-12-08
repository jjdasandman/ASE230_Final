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

    if (!$post || getCurrentUser() !== $post['author']) {
        echo "Unauthorized access.";
        exit();
    } 
} else {
    echo "No post specified.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Handle image upload if a new image is submitted
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = '../uploads/' . $imageName;

        // Move uploaded file to the uploads directory
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            $posts[$post_id]['image'] = $imagePath;  // Update the post with the new image path
        }
    }

    // Update title and content
    $posts[$post_id]['title'] = $title;
    $posts[$post_id]['content'] = $content;

    // Save updated posts data to JSON
    savePostsToJSON('../posts.json', $posts);

    header('Location: detail.php?post_id=' . $post_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="my-4">Edit Post</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="content">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
            <?php if (!empty($post['image'])): ?>
                <p>Current Image: <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Current Post Image" width="200"></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="detail.php?post_id=<?php echo htmlspecialchars($post_id); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
