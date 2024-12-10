<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once '../utils.php';
include_once '../db_connection.php'; // Include the database connection file
include_once '../posts/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Prepare and execute the query to fetch the user
        $stmt = $db->prepare("SELECT password FROM User WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Check if user exists and password matches
        if ($user && $password === $user['password']) { //was going to hash pw w password_verify($password, $user['password']) but was causing issues on update and admin.php pages, 
            $_SESSION['username'] = $username; // Set session for logged-in user
            $userRole = getUserRole($db, $username);
            if ($userRole == 2) {
                $_SESSION['isValidAdmin'] = true;
            }
            header("Location: ../posts/index.php"); // Redirect to posts index
            exit();
        } else {
            $error = "Invalid username or password."; // Error message for invalid login
        }
    } catch (PDOException $e) {
        $error = "An error occurred while trying to log you in. Please try again later.";
        // Uncomment the line below for debugging purposes (in development only)
        // $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($error)) {
            echo "<p class='text-danger'>$error</p>";
        } ?>
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
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="../auth/signup.php" class="btn btn-secondary">Sign up</a>
            <a href="../posts/index.php" class="btn btn-secondary">Don't Login</a>
        </form>
    </div>
</body>

</html>