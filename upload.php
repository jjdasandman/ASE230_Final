<?php


// Function to load existing posts from the JSON file
function loadPostsFromJSON($filename) {
    if (file_exists($filename)) {
        $json_data = file_get_contents($filename);
        return json_decode($json_data, true);
    }
    return [];
}

// Function to save posts back to the JSON file
function savePostsToJSON($filename, $posts) {
    $json_data = json_encode($posts, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json_data);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];
    $date = date("Y-m-d"); // Set current date automatically

    // Load existing posts
    $filename = 'posts.json';
    $posts = loadPostsFromJSON($filename);

    // Add new post to the array
    $posts[] = [
        'title' => $title,
        'content' => $content,
        'author' => $author,
        'date' => $date,
    ];

    // Save updated posts to JSON
    savePostsToJSON($filename, $posts);

    // Redirect back to index.php after submission
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Create New Post</h1>
        <form method="POST" action="upload.php">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Post</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>