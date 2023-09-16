<?php
require "connectDatabase.php";
require_once 'startSession.php';

$isedit = $_POST['isBedingEdited'];
$curr_inv = $_POST['currentInventory'];


$sql = "SELECT statusId, statusName FROM `Status` WHERE inventoryId = $curr_inv;";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        if ($isedit == 1) {
            // for the edit inventory modal
            echo
            '<li class="list-group-item">
                ' . $row['statusName'] . '
                <a class="float-end" href="#" data-bs-toggle="" data-bs-target="" id="dltStatBtn" onclick="deleteStatus(' . $row['statusId'] . ')">
                    <img src="../assets/trash-solid.svg" alt="" width="16px" class="">
                </a>
            </li>';
        } else {
            // for the add item modal
            echo '<option value="' . $row['statusId'] . '">' . $row['statusName'] . '</option>';
        }
    }
} else {
    if ($isedit == 1) {
        echo '<li class="list-group-item text-secondary">No Status</li>';
    } else {
        echo '<option selected>No Status</option>';
    }
}
