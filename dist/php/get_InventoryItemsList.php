<?php
require "connectDatabase.php";
require_once 'startSession.php';

$curr_inv = $_POST['currentInventory'];

$sql = "SELECT `itemId`, `itemName`, `itemAmount`, `itemBarcode`, Category.categoryName, Status.statusName, Inventory.inventoryName `itemDate` FROM `Item` 
INNER JOIN Category ON Item.categoryId = Category.categoryId 
INNER JOIN Status ON Item.statusId = Status.statusId 
INNER JOIN Inventory ON Item.inventoryId = Inventory.inventoryId 
WHERE Item.inventoryId = $curr_inv;";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo
    '<h1 class=" display-3">Inventory</h1>
    <div class="overflow-scroll">
        <form action="" method="post">
            <table class="table table-hover" style="min-width: 600px;" id="itemsTable">
                <thead>
                    <tr>
                        <th></th>
                        <th scope="col" style="width: 30%;">Item</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Status</th>
                        <th scope="col">Category</th>
                        <th></th>
                        <th scope="col">Barcode</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>';

    while ($row = mysqli_fetch_array($result)) {
        echo
        '<tr>
            <td>
                <a href="#" data-bs-toggle="tooltip" data-bs-title="remove Item" id="deleteBtn" onclick="removeItem()">
                    <img src="../assets/trash-solid.svg" width="16px" alt="">
                </a>
            </td>
            <td>
                <a href="#" class="text-decoration-none link-dark" data-bs-toggle="modal" data-bs-target="#itemHistoryModal" onclick="">
                    <span data-bs-toggle="tooltip" data-bs-title="show Item history">
                        ' . $row['itemName'] . '
                    </span>
                </a>
            </td>
            <td>' . $row['itemAmount'] . '</td>
            <td>' . $row['statusName'] . '</td>
            <td>' . $row['categoryName'] . '</td>
            <td>
                <a href="#" data-bs-toggle="tooltip" data-bs-title="download barcode" onclick="downloadBarcode()">
                    <img src="../assets/download-solid.svg" width="16px" alt="">
                </a>
            </td>
            <td>' . $row['itemBarcode'] . '</td>
            <td>
                <a href="#" data-bs-toggle="modal" data-bs-target="#editItemModal" onclick="currentItem(1)">
                    <img src="../assets/pen-to-square-solid.svg" data-bs-toggle="tooltip" data-bs-title="edit Item" width="16px" alt="">
                </a>
            </td>
        </tr>';
    }
    echo '</tbody>
            </table>
        </form>
    </div>';
} else {
    echo '<h1 class=" text-secondary text-center">No Item added</h1>';
}