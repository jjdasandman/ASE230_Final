<?php
if (!isset($_SESSION)) {
    session_start();
}

// Check if the user is logged in
function isLoggedIn()
{
    return isset($_SESSION['username']);
}

// Get the current logged-in user
function getCurrentUser($db)
{
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
        $stmt->execute([':username' => $_SESSION['username']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error retrieving user: " . $e->getMessage();
        return null;
    }
}

function getCurrentUserRole($db)
{
    if (!isLoggedIn()) {
        return null; // If not logged in, return null
    }

    try {
        // Query to join user table with role table and fetch the role for the current user
        $stmt = $db->prepare("SELECT r.role FROM user u INNER JOIN role r ON u.role_id = r.role_id WHERE u.username = :username");
        $stmt->execute([':username' => $_SESSION['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user['role'] : null; // Return the role or null if not found
    } catch (PDOException $e) {
        echo "Error retrieving user role: " . $e->getMessage();
        return null;
    }
}

// Load posts from the database
function loadPostsFromDatabase($db)
{
    try {
        $stmt = $db->prepare("SELECT * FROM clothingpost ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error loading posts: " . $e->getMessage();
        return [];
    }
}

// Save a new post to the database
function savePostToDatabase($db, $post)
{
    try {
        $stmt = $db->prepare("INSERT INTO clothingpost (title, description, photo_url, username, created_at) VALUES (:title, :description, :photo_url, :username, NOW())");
        $stmt->execute([
            ':title' => $post['title'],
            ':description' => $post['description'],
            ':photo_url' => $post['photo_url'],
            ':username' => $post['username']
        ]);
    } catch (PDOException $e) {
        echo "Error saving post: " . $e->getMessage();
    }
}

// Display posts
function displayPosts($posts)
{
    foreach ($posts as $post) {
        echo "<h3><a href='detail.php?post_id={$post['id']}'>" . htmlspecialchars($post['title']) . "</a></h3>";
        echo "<p><em>By " . htmlspecialchars($post['username']) . " on " . htmlspecialchars($post['created_at']) . "</em></p>";
    }
}

// Get a single post by ID
function getPostById($db, $post_id)
{
    try {
        $stmt = $db->prepare("SELECT * FROM clothingpost WHERE post_id = :id");
        $stmt->execute([':id' => $post_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error retrieving post: " . $e->getMessage();
        return null;
    }
}

// Load all users from the database
function loadUsersFromDatabase($db)
{
    try {
        $stmt = $db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error loading users: " . $e->getMessage();
        return [];
    }
}

// Save a new user to the database
function saveUserToDatabase($db, $user)
{
    try {
        $stmt = $db->prepare("INSERT INTO users (username, password, created_at) VALUES (:username, :password, NOW())");
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => password_hash($user['password'], PASSWORD_BCRYPT)
        ]);
    } catch (PDOException $e) {
        echo "Error saving user: " . $e->getMessage();
    }
}

// Update a user's details in the database
function updateUserInDatabase($db, $user)
{
    try {
        $stmt = $db->prepare("UPDATE users SET username = :username, password = :password WHERE id = :id");
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => password_hash($user['password'], PASSWORD_BCRYPT),
            ':id' => $user['id']
        ]);
    } catch (PDOException $e) {
        echo "Error updating user: " . $e->getMessage();
    }
}

// Delete a user from the database
function deleteUserFromDatabase($db, $user_id)
{
    try {
        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);

        // Delete user's posts
        $stmt = $db->prepare("DELETE FROM clothingpost WHERE username = :username");
        $stmt->execute([':username' => $_SESSION['username']]);
    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
}

// Fetch user by username
function getUserByUsername($username)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    return $stmt->fetch();
}

// Get all posts by a user
function getPostsByAuthor($username)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE author = :author');
    $stmt->execute(['author' => $username]);
    return $stmt->fetchAll();
}

// Check if username exists
function usernameExists($username)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
}

// Update user details
function updateUser($old_username, $new_username, $new_password)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE users SET username = :new_username, password = :new_password WHERE username = :old_username');
    $stmt->execute([
        'new_username' => $new_username,
        'new_password' => $new_password ? $new_password : "",
        'old_username' => $old_username
    ]);
}

// Update posts author
function updatePostsAuthor($old_username, $new_username)
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE posts SET author = :new_username WHERE author = :old_username');
    $stmt->execute(['new_username' => $new_username, 'old_username' => $old_username]);
}

// Delete user
function deleteUser($username)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
}

// Delete posts by author
function deletePostsByAuthor($username)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM posts WHERE author = :author');
    $stmt->execute(['author' => $username]);
}
function getUserRole($db, $userName)
{
    $stmt = $db->prepare("SELECT role_id FROM user WHERE username = :username");
    $stmt->execute([':username' => $userName]);
    $user = $stmt->fetch();
    return $user['role_id'];
}
?>