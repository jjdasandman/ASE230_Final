<?php
include '../utils.php';

session_destroy();
header("Location: login.php");
exit();
?>
