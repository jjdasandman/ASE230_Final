<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['username']);
}

function getCurrentUser() {
    return $_SESSION['username'];
}

function loadPostsFromJSON($filename) {
    return json_decode(file_get_contents($filename), true) ?: [];
}

function savePostsToJSON($filename, $posts) {
    file_put_contents($filename, json_encode($posts, JSON_PRETTY_PRINT));
}

function loadUsers() {
    return json_decode(file_get_contents('../users.json'), true) ?: [];
}

function saveUsers($users) {
    file_put_contents('../users.json', json_encode($users, JSON_PRETTY_PRINT));
}

function displayPosts($posts) {
    foreach ($posts as $index => $post) {
        echo "<h3><a href='detail.php?post_id={$index}'>{$post['title']}</a></h3>";
        echo "<p><em>By {$post['author']} on {$post['date']}</em></p>";
    }
}

function getPost($posts, $post_id) {
    return $posts[$post_id] ?? null;
}

// Load users from JSON file
function loadUsersFromJSON($filename) {
    $json_data = file_get_contents($filename);
    return json_decode($json_data, true);
}

// Save users to JSON file
function saveUsersToJSON($filename, $users) {
    $json_data = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json_data);
}

?>