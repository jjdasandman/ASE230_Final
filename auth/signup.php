<?php
include '../utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Load existing users
    $users = loadUsers();

    // Check if the username already exists
    if (array_key_exists($username, $users)) {
        echo "<script>alert('Username already taken! Please choose another.');</script>";
    } else {
        // Add new user
        $users[$username] = $password;
        saveUsers($users);

        // Automatically log in the user
        session_start();
        $_SESSION['username'] = $username; // Set the session variable for the logged-in user
        
        // Redirect to posts/index.php
        header("Location: ../posts/index.php");
        exit();
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
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Sign Up</button>
		<a href="../posts/index.php" class="btn btn-secondary">Go back</a>
    </form>
</div>
</body>
</html>
