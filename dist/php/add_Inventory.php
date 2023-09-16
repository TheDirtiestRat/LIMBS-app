<?php
require "connectDatabase.php";
require_once 'startSession.php';


$inv_name = $_POST['inventoryName'];
$inv_room = $_POST['inventoryRoom'];
$usr_accs = array();
if (isset($_POST['users'])) {
    $usr_accs = $_POST['users'];
}


$u_id = $_SESSION['userId'];

$inv_name = ucwords($inv_name);
$inv_room = ucwords($inv_room);

$sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
WHERE `inventoryName` = '$inv_name';";

if (mysqli_query($conn, $sql)) {
    // return that the item already exist in the inventory
    header("Location: fileMaintenancePage.php?error=Inventory " . $inv_name . " already exist!");
    return;
}

$sql = "INSERT INTO `Inventory`(`inventoryId`, `inventoryName`, `inventoryRoom`) VALUES (NULL, '$inv_name', '$inv_room');";

if (mysqli_query($conn, $sql)) {

    // sets the users to be accessed
    if (isset($_POST['users'])) {
        $last_id = mysqli_insert_id($conn);

        $sql1 = "INSERT INTO `UserAccessList`(`accessListId`, `inventoryId`, `userId`) 
                VALUES (NULL, $last_id, $u_id);";

        foreach ($usr_accs as $user) {
            $sql1 .= "INSERT INTO `UserAccessList`(`accessListId`, `inventoryId`, `userId`) 
                    VALUES (NULL, $last_id, $user);";
        }

        mysqli_multi_query($conn, $sql1);
    } else {
        $last_id = mysqli_insert_id($conn);

        $sql1 = "INSERT INTO `UserAccessList`(`accessListId`, `inventoryId`, `userId`) 
                VALUES (NULL, $last_id, $u_id);";

        mysqli_multi_query($conn, $sql1);
    }

    // adds the inventory
    header("Location: fileMaintenancePage.php?alert=Inventory " . $inv_name . " is added and accessed by " . count($usr_accs) + 1 . " users!");
    return;
} else {
    // if fails to add inventory
    $err = mysqli_error($conn);
    header("Location: fileMaintenancePage.php?error=Failed to add Inventory! " . $err ." " . $inv_name ." ". $inv_room);
}

