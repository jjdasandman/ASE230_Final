<?php
include_once '../utils.php';
include_once '../db_connection.php';  // Include your database connection

if (!isset($_SESSION['username'])) {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];

// Handle form submission for updating user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the new username and password safely
    $new_username = isset($_POST['username']) ? $_POST['username'] : '';
    $new_password = !empty($_POST['password']) ? $_POST['password'] : null;

    // Update user details in database
    if (isset($_POST['delete_account'])) {
        // Confirm before deletion
        if (isset($_POST['confirm_delete'])) {
            // Delete user posts from the posts table
            $stmt = $db->prepare("DELETE FROM clothingpost WHERE username = :username");
            $stmt->execute([':username' => $username]);

            // Delete user from the users table
            $stmt = $db->prepare("DELETE FROM User WHERE username = :username");
            $stmt->execute([':username' => $username]);

            // Log out and redirect to signup
            session_destroy();
            header('Location: ../auth/signup.php');
            exit();
        } else {
            echo "<script>alert('Please confirm deletion to proceed.');</script>";
        }
    } else {
        // Check if the new username is already taken
        if ($new_username && $new_username !== $username) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM User WHERE username = :new_username");
            $stmt->execute([':new_username' => $new_username]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo "Username already taken!";
            } else {
                // First, update username in the User table
                $stmt = $db->prepare("UPDATE User SET username = :new_username WHERE username = :username");
                $stmt->execute([':new_username' => $new_username, ':username' => $username]);

                // Now update username in the clothingpost table (the foreign key table)
                $stmt = $db->prepare("UPDATE clothingpost SET username = :new_username WHERE username = :username");
                $stmt->execute([':new_username' => $new_username, ':username' => $username]);

                if ($new_password) {
                    // If password is being updated, hash it before saving
                    //$new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE User SET password = :password WHERE username = :username");
                    $stmt->execute([':password' => $new_password, ':username' => $new_username]);
                }

                // Update session with the new username
                $_SESSION['username'] = $new_username;
                header('Location: index.php');
                exit();
            }
        } else {
            // If no username change or it's valid
            if ($new_password) {
                // If password is being updated, hash it before saving
                //$new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE User SET password = :password WHERE username = :username");
                $stmt->execute([':password' => $new_password, ':username' => $username]);
            }

            header('Location: index.php');
            exit();
        }
    } 
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Account Settings</h1>
        <form method="POST" action="settings.php">
            <div class="form-group">
                <label for="username">New Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Leave blank if not changing">
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank if not changing)</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if not changing">
            </div>
            <button type="submit" class="btn btn-primary">Update Account</button>
        </form>

        <form method="POST" action="settings.php" class="mt-3">
            <input type="hidden" name="delete_account" value="1">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="confirm_delete" name="confirm_delete">
                <label for="confirm_delete" class="form-check-label">I confirm I want to delete my account.</label>
            </div>
            <button type="submit" class="btn btn-danger mt-2">Delete Account</button>
        </form>

        <a href="index.php" class="btn btn-secondary mt-3">Back to Profile</a>
    </div>
</body>
</html>
