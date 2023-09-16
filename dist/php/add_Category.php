<?php
require "connectDatabase.php";
require_once 'startSession.php';


$cat_name = $_POST['categoryName'];
$cat_inv = $_POST['selectInventory'];


$cat_name = ucwords($cat_name);

$sql = "INSERT INTO `Category`(`categoryId`, `categoryName`, `inventoryId`) 
VALUES (NULL,'$cat_name','$cat_inv');";

if (mysqli_query($conn, $sql)) {
    header("Location: fileMaintenancePage.php?alert=Category " . $cat_name . " is added!");
    
}else {
    $err = mysqli_error($conn);
    header("Location: fileMaintenancePage.php?error=Failed to add Category! " . $err);
}