<?php
include_once '../utils.php';
include_once '../db_connection.php'; // Ensure you have a DB connection
include_once 'navbar.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if a post_id is provided in the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Fetch the post from the database
    $stmt = $db->prepare("SELECT p.*, u.username FROM clothingpost p INNER JOIN user u ON p.username = u.username WHERE p.post_id = :post_id");
    $stmt->execute([':post_id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user is authorized (author of the post or admin)
    if (!$post || 
        (getCurrentUser($db)['username'] !== $post['username'] && getCurrentUserRole($db) !== 'admin')) {
        echo "Unauthorized access.";
        exit();
    }
} else {
    echo "No post specified.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = $post['photo_url']; // Retain current image unless a new one is uploaded

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = "../uploads/" . $imageName;

        // Move the uploaded file to the uploads directory
        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            $imagePath = $post['photo_url']; // If upload failed, keep the old image
        }
    }

    // Update the post in the database
    $stmt = $db->prepare("UPDATE clothingpost SET title = :title, description = :description, photo_url = :image WHERE post_id = :post_id");
    $stmt->execute([
        ':title' => $title,
        ':description' => $content,
        ':image' => $imagePath,
        ':post_id' => $post_id
    ]);

    // Redirect to the post detail page
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
            <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
            <?php if (!empty($post['photo_url'])): ?>
                <p>Current Image: <img src="../uploads/<?php echo htmlspecialchars($post['photo_url']); ?>" alt="Current Post Image" width="200"></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="detail.php?post_id=<?php echo htmlspecialchars($post_id); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
