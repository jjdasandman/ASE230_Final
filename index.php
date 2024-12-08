<?php
session_start();
include 'utils.php';

// Redirect to the blog index page
header('Location: auth/login.php');
exit();
?>
