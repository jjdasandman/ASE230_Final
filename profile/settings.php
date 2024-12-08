<?php

include '../utils.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../auth/login.php');
    exit();
}

$users = loadUsersFromJSON('../users.json');
$posts = loadPostsFromJSON('../posts.json');
$username = $_SESSION['username'];

// Handle form submission for updating user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the new username and password safely
    $new_username = isset($_POST['username']) ? $_POST['username'] : '';
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Update user details in users.json
    if (isset($_POST['delete_account'])) {
        // Confirm before deletion
        if (isset($_POST['confirm_delete'])) {
            // Remove user from users.json
            unset($users[$username]);
            saveUsersToJSON('../users.json', $users);

            // Remove all posts by the user
            $posts = array_filter($posts, fn($post) => $post['author'] !== $username);
            savePostsToJSON('../posts.json', $posts);

            // Log out and redirect to signup
            session_destroy();
            header('Location: ../auth/signup.php');
            exit();
        } else {
            echo "<script>alert('Please confirm deletion to proceed.');</script>";
        }
    } else {
        // Check if the new username is already taken
        if (array_key_exists($new_username, $users) && $new_username !== $username) {
            echo "Username already taken!";
        } else {
            // Update username and password in users.json
            if ($new_password) {
                $users[$new_username] = $new_password;
            } else {
                $users[$new_username] = $users[$username]; // Retain the old password if not updating
            }

            // Remove old username entry
            if ($new_username !== $username) {
                unset($users[$username]);
            }

            saveUsersToJSON('../users.json', $users);

            // Update posts with the new username
            foreach ($posts as &$post) {
                if ($post['author'] === $username) {
                    $post['author'] = $new_username;
                }
            }
            savePostsToJSON('../posts.json', $posts);

            // Update session
            $_SESSION['username'] = $new_username;
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
                <input type="text" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($username) ?>">
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank if not changing)</label>
                <input type="password" class="form-control" id="password" name="password">
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
