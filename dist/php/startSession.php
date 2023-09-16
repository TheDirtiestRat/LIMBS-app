<?php

session_start();

$user;

if (!isset($_SESSION['userId']) && !isset($_SESSION['userName'])) {
    header("Location: loginPage.php");
    exit();
}else {
    $user = $_SESSION['userName'];
}

?>