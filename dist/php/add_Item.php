<?php
require "connectDatabase.php";
require_once 'startSession.php';


$itm_name = $_POST['itemName'];
$itm_spec = $_POST['itemSpecify'];
$itm_inv = $_POST['selectInventory'];
$itm_cat = $_POST['selectCategory'];
$itm_sta = $_POST['selectStatus'];
$itm_amo = $_POST['inputAmount'];

$u_id = $_SESSION['userId'];

// if no selected inventory an error will be thrown
if ($itm_inv == "") {
    header("Location: fileMaintenancePage.php?error=Failed to add Inventory! " . "No selected selected inventory");
    return;
}elseif ($itm_name == "") {
    header("Location: fileMaintenancePage.php?error=Failed to add Inventory! " . "Item name is not set");
    return;
}

$itm_name = ucwords($itm_name);

$sql = "SELECT `itemId`, `itemName` FROM `Item` 
WHERE `itemName` = '$itm_name';";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // return that the item already exist in the inventory
    header("Location: fileMaintenancePage.php?error=Item " . $itm_name . " already exist!");
    return;
}

$currDate = date("M/d/Y");
$currTime = date("h:i a");
$barcode = abs(crc32(uniqid()));

$sql = "INSERT INTO `Item`(`itemId`, `itemName`, `itemSpecification`, `itemAmount`, `itemBarcode`, `itemDate`, `categoryId`, `statusId`, `inventoryId`) 
        VALUES (NULL,'$itm_name','$itm_spec','$itm_amo', RPAD($barcode, 12, '0'), CURRENT_DATE(), '$itm_cat', '$itm_sta', '$itm_inv');
        INSERT INTO `ItemHistoryLog`(`historyId`, `itemId`, `historyAction`, `historyInput`, `historyDate`, `historyTime`) 
        VALUES (NULL, (SELECT LAST_INSERT_ID()), 'CREATED', 'added in the inventory', '$currDate', '$currTime');";

// $res = mysqli_multi_query($conn, $sql);

// adds new item
if (mysqli_multi_query($conn, $sql)) {
    header("Location: fileMaintenancePage.php?alert=Item " . $itm_name . " is added in the Inventory!");
} else {
    // if fails to add inventory
    $err = mysqli_error($conn);
    header("Location: fileMaintenancePage.php?error=Failed to add Inventory! " . $err);
}

