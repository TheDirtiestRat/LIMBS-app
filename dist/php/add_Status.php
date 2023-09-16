<?php
require "connectDatabase.php";
require_once 'startSession.php';


$sta_name = $_POST['statusName'];
$sta_inv = $_POST['selectInventory'];


$sta_name = ucwords($sta_name);

$sql = "INSERT INTO `Status`(`statusId`, `statusName`, `inventoryId`) 
VALUES (NULL,'$sta_name','$sta_inv');";

if (mysqli_query($conn, $sql)) {
    header("Location: fileMaintenancePage.php?alert=Status " . $sta_name . " is added!");
}else {
    $err = mysqli_error($conn);
    header("Location: fileMaintenancePage.php?error=Failed to add Status! " . $err);
}