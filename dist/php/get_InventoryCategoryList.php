<?php
require "connectDatabase.php";
require_once 'startSession.php';

$isedit = $_POST['isBedingEdited'];
$curr_inv = $_POST['currentInventory'];


$sql = "SELECT categoryId, categoryName FROM `Category` WHERE inventoryId = $curr_inv;";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        if ($isedit == 1) {
            // for the edit inventory modal
            echo
            '<li class="list-group-item">
                ' . $row['categoryName'] . '
                <a class="float-end" href="#" data-bs-toggle="" data-bs-target="" id="dltStatBtn" onclick="deleteCategory(' . $row['categoryId'] . ')">
                    <img src="../assets/trash-solid.svg" alt="" width="16px" class="">
                </a>
            </li>';
        } else {
            // for the add item modal
            echo '<option value="' . $row['categoryId'] . '">' . $row['categoryName'] . '</option>';
        }
    }
} else {
    if ($isedit == 1) {
        echo '<li class="list-group-item text-secondary">No Categories</li>';
    } else {
        echo '<option selected>No Category</option>';
    }
}
