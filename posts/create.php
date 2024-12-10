<?php
include_once '../utils.php';
include_once '../db_connection.php'; // Ensure you have a DB connection
include_once 'navbar.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the current logged-in user's username
$currentUser = getCurrentUser($db); // Assuming this function gets the current logged-in user
$author = $currentUser['username']; // Automatically set author to logged-in username

// Fetch color options from the database
$queryColors = $db->prepare("SELECT color_id, color FROM colors");
$queryColors->execute();
$colors = $queryColors->fetchAll(PDO::FETCH_ASSOC);

// Fetch clothing type options from the database
$queryTypes = $db->prepare("SELECT clothing_type_id, type FROM clothingtype");
$queryTypes->execute();
$types = $queryTypes->fetchAll(PDO::FETCH_ASSOC);

// Fetch material options from the database
$queryMaterials = $db->prepare("SELECT material_id, material FROM materials");
$queryMaterials->execute();
$materials = $queryMaterials->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $color_id = $_POST['color'];
    $clothing_type_id = $_POST['clothing_type'];
    $material_id = $_POST['material'];
    $date = date("Y-m-d"); // Set current date automatically

    // Handle image upload
	$photo_url = 'default.png'; // Default to empty if no file uploaded
	$photoUrl = 'default.png';
	if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
		$photoTmpPath = $_FILES['photo']['tmp_name'];
		$photoName = basename($_FILES['photo']['name']);
		$photoUrl = "uploads/" . $photoName; // Only store the filename, not the full path

		// Move the uploaded file to the uploads directory
		if (move_uploaded_file($photoTmpPath, "../" . $photoUrl)) {
			$photo_url = $photoName;  // Store only the filename in the database
		}
	}

	// Insert new post into the database
	$stmt = $db->prepare("INSERT INTO clothingpost (username, title, photo_url, color_id, clothing_type_id, material_id, description, created_at) 
						   VALUES (:username, :title, :photo_url, :color_id, :clothing_type_id, :material_id, :content, :created_at)");

	$stmt->execute([
		'username' => $author,
		'title' => $title,
		'photo_url' => $photo_url, // Now this stores the filename (e.g., "image.jpg")
		'color_id' => $color_id,
		'clothing_type_id' => $clothing_type_id,
		'material_id' => $material_id,
		'content' => $content,
		'created_at' => $date
	]);

    // Redirect back to the index.php after submission
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
    <link rel="stylesheet" href="/Final2/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Create New Post</h1>
        <form method="POST" enctype="multipart/form-data"> <!-- enctype needed for file upload -->
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Select Photo</label>
                <input type="file" class="form-control-file" id="photo" name="photo">
            </div>
            <div class="form-group">
                <label for="color">Color</label>
                <select class="form-control" id="color" name="color" required>
                    <option value="">Select a color</option>
                    <?php foreach ($colors as $color): ?>
                        <option value="<?= $color['color_id'] ?>"><?= $color['color'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="clothing_type">Clothing Type</label>
                <select class="form-control" id="clothing_type" name="clothing_type" required>
                    <option value="">Select a clothing type</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['clothing_type_id'] ?>"><?= $type['type'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="material">Material</label>
                <select class="form-control" id="material" name="material" required>
                    <option value="">Select a material</option>
                    <?php foreach ($materials as $material): ?>
                        <option value="<?= $material['material_id'] ?>"><?= $material['material'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit Post</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
