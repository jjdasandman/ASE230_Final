<?php
if (!isset($_SESSION)) { 
    session_start(); 
}

include_once '../utils.php';
include_once '../db_connection.php';  // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Check if the username already exists in the database
        $stmt = $db->prepare("SELECT COUNT(*) FROM User WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Username already taken! Please choose another.";
        } else {
            // Insert new user into the User table with a default role_id (e.g., role_id = 1 for 'user')
            $stmt = $db->prepare("INSERT INTO User (username, password, role_id) VALUES (:username, :password, 1)");
            $stmt->execute([':username' => $username, ':password' => $password]);

            // Automatically log in the user
            $_SESSION['username'] = $username; // Set the session variable for the logged-in user

            // Redirect to posts/index.php
            header("Location: ../posts/index.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "An error occurred while trying to sign you up. Please try again later.";
        $errorDetails = "Error: " . $e->getMessage(); // Capture the specific error message
        echo "<p class='text-danger'>Error details: $errorDetails</p>"; // Output error details for debugging
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Sign Up</h1>
    <?php if (isset($error)) { echo "<p class='text-danger'>$error</p>"; } ?>
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <br />
        <button type="submit" class="btn btn-primary">Sign Up</button>
        <a href="../posts/index.php" class="btn btn-secondary">Go back</a>
    </form>
</div>
</body>
</html>
