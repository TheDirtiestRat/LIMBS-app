<?php
require "connectDatabase.php";
require_once 'startSession.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,
            shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>File Maintenance</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/styles.css" rel="stylesheet" />
    <!-- html2canvas for downloading the barcode as an img -->
    <script src="../js/html2canvas.min.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="border-end bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light">Inventory List</div>
            <div class="list-group  p-2">
                <?php
                $currentInventory = 0;
                $u_id = $_SESSION['userId'];

                $sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
                WHERE `inventoryId` IN (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id)";

                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {

                        $invId = $row['inventoryId'];

                        $catSql = "SELECT `categoryId`, `categoryName`, `inventoryId` FROM `Category` WHERE `inventoryId` = $invId";
                        $catResult = mysqli_query($conn, $catSql);

                        echo
                        '<a class="list-group-item list-group-item-action p-3" data-bs-toggle="collapse" href="#collapseList' . $invId . '" role="button" aria-expanded="false" aria-controls="collapseExample" onclick="currentInventory(' . $row['inventoryId'] . ')">
                            ' . $row['inventoryName'] . '
                        </a>
                        <div class="collapse" id="collapseList' . $invId . '">
                            <ul class="list-group list-group-flush w-100">';
                        while ($catRow = mysqli_fetch_array($catResult)) {
                            echo '<li class="list-group-item" onclick="searchByCategory(' . "'" . $catRow['categoryName'] . "'" . ')">' . $catRow['categoryName'] . '</li>';
                        }
                        echo '</ul>
                        </div>';
                    }
                } else {
                    echo '<h3 class="text-secondary text-center">No Inventories added yet</h3>';
                }
                ?>

                <button class="btn btn-light mb-1 m-2" type="button" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <img src="../assets/circle-plus-solid.svg" class="me-1" width="16px" alt="">New Inventory
                </button>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light
                    border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-dark" id="sidebarToggle">Toggle
                        Menu</button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle
                            navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item active"><a class="nav-link" href="dashboardPage.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link active" href="fileMaintenancePage.php">File Maintenance</a></li>
                            <li class="nav-item"><a class="nav-link" href="transactionPage.php">Transaction</a></li>
                            <li class="nav-item"><a class="nav-link" href="reportsPage.php">Reports</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#!">Profile</a>
                                    <a class="dropdown-item" href="#!">Add
                                        new Users</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page content-->
            <div class="container-fluid p-3">
                <div class="row mb-3">
                    <div class="col">
                        <button class="btn btn-danger mb-1 float-end me-1" type="button">Remove Inventory</button>
                        <button class="btn btn-secondary mb-1 float-end me-1" type="button" data-bs-toggle="modal" data-bs-target="#editInventoryModal">Edit Inventory</button>

                        <button class="btn btn-dark mb-1 float-end me-1" type="button" data-bs-toggle="modal" data-bs-target="#addStatusModal">Add Status</button>
                        <button class="btn btn-dark mb-1 float-end me-1" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                        <button class="btn btn-dark mb-1 float-end me-1" type="button" data-bs-toggle="modal" data-bs-target="#addItemModal">+ Add Item</button>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-lg-8">
                        <div class="d-flex">
                            <input id="searchBar" class="form-control me-2" type="search" placeholder="Search Item Name..." onkeyup="searchByName()">
                            <button class="btn btn-dark" type="submit" onclick="searchByName()">Search</button>
                        </div>
                    </div>
                    <div class="col-lg">
                        <select class="form-select" aria-label="Default
                                select example" id="slcSort">
                            <option selected>Sort By:</option>
                            <option value="itemDate">Date</option>
                            <option value="itemName">Item name</option>
                            <option value="itemId">ID</option>
                        </select>
                    </div>
                </div>
                <hr>

                <div class="p-md-4 pt-md-0">
                    <div id="inventoryItemsTable">
                        <?php

                        $sql = "SELECT `itemId`, `itemName`, `itemAmount`, `itemBarcode`, Category.categoryName, Status.statusName, Inventory.inventoryName `itemDate` FROM `Item` 
                        INNER JOIN Category ON Item.categoryId = Category.categoryId 
                        INNER JOIN Status ON Item.statusId = Status.statusId 
                        INNER JOIN Inventory ON Item.inventoryId = Inventory.inventoryId 
                        WHERE Item.inventoryId = (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id LIMIT 1);";

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

                        ?>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed bottom-0 start-0 p-3">
        <?php
        if (isset($_GET['error'])) {
            echo
            '<div id="AlertResult" class="alert alert-danger alert-dismissible m-0" role="alert">
                    <div>' . $_GET['error'] . '</div>
                </div>';
        }
        if (isset($_GET['alert'])) {
            echo
            '<div id="AlertResult" class="alert alert-success alert-dismissible m-0" role="alert">
                    <div>' . $_GET['alert'] . '</div>
                </div>';
        }
        ?>
    </div>

    <!-- this is where the barcode will appear and be downloaded -->
    <div id="box">

    </div>

    <!-- Modals -->
    <!-- add Item Modal -->
    <form action="add_Item.php" method="post">
        <div class="modal fade" tabindex="-1" id="addItemModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Item in Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item name</label>
                            <input name="itemName" type="text" class="form-control" id="itemName" placeholder="Item Name" maxlength="32">
                        </div>
                        <div class="mb-3">
                            <label for="itemSpecify" class="form-label">Specification</label>
                            <input name="itemSpecify" type="text" class="form-control" id="itemSpecify" placeholder="Item Specification" maxlength="32">
                        </div>

                        <div class="mb-3">
                            <div class="col" id="slctInventory">
                                <label for="selectInventory" class="col-auto col-form-label">Inventory</label>
                                <select class="form-select" aria-label="Defaultselectexample" name="selectInventory" id="selectInventoryforItem" onchange="getStatusAndCatetorySelected()">
                                    <option value="">- Select an Inventory -</option>
                                    <!-- Gets the list inventory to be selected -->
                                    <?php

                                    $sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
                                    WHERE `inventoryId` IN (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id)";

                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="' . $row['inventoryId'] . '">' . $row['inventoryName'] . '</option>';
                                        }
                                    } else {
                                        echo '<option selected>No Inventory</option>';
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row g-3" id="selectStat&Cat">
                                <div class="col" id="slctCategory">
                                    <label for="" class=" col-auto col-form-label">Category</label>
                                    <select class="form-select" aria-label="Defaultselectexample" name="selectCategory" id="selectCategory">

                                    </select>
                                </div>
                                <div class="col" id="slctStatus">
                                    <label for="" class=" col-auto col-form-label">Status</label>
                                    <select class="form-select" aria-label="Defaultselectexample" name="selectStatus" id="selectStatus">

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row float-end">
                            <label for="inputAmount" class=" col-auto col-form-label">Quantity</label>
                            <div class="col-auto">
                                <input type="number" name="inputAmount" style="width: 6rem;" class="form-control" id="inputAmount" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Add Item</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- add Status Modal -->
    <form action="add_Status.php" method="post">
        <div class="modal fade" tabindex="-1" id="addStatusModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Status name</label>
                            <input name="statusName" type="text" class="form-control" id="statusName" placeholder="input name of the Status" maxlength="24">
                        </div>

                        <div class="mb-3">
                            <div class="col" id="slctInventory">
                                <label for="selectInventory" class="col-auto col-form-label">Inventory</label>
                                <select class="form-select" aria-label="Defaultselectexample" name="selectInventory" id="selectInventory">
                                    <option value="">- Select an Inventory -</option>
                                    <!-- Gets the list inventory to be selected -->
                                    <?php

                                    $sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
                                    WHERE `inventoryId` IN (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id)";

                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="' . $row['inventoryId'] . '">' . $row['inventoryName'] . '</option>';
                                        }
                                    } else {
                                        echo '<option selected>No Inventory</option>';
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Add Status</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- add Category Modal -->
    <form action="add_Category.php" method="post">
        <div class="modal fade" tabindex="-1" id="addCategoryModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Category name</label>
                            <input name="categoryName" type="text" class="form-control" id="categoryName" placeholder="input name of the Category" maxlength="24">
                        </div>

                        <div class="mb-3">
                            <div class="col" id="slctInventory">
                                <label for="selectInventory" class="col-auto col-form-label">Inventory</label>
                                <select class="form-select" aria-label="Defaultselectexample" name="selectInventory" id="selectInventory">
                                    <option value="">- Select an Inventory -</option>
                                    <!-- Gets the list inventory to be selected -->
                                    <?php

                                    $sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
                                    WHERE `inventoryId` IN (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id)";

                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="' . $row['inventoryId'] . '">' . $row['inventoryName'] . '</option>';
                                        }
                                    } else {
                                        echo '<option selected>No Inventory</option>';
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Add Category</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- add Inventory Modal -->
    <form action="add_Inventory.php" method="post">
        <div class="modal fade" tabindex="-1" id="addInventoryModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="inventoryName" class="form-label">Inventory name</label>
                            <input name="inventoryName" type="text" class="form-control" id="inventoryName" placeholder="Inventory Name" maxlength="32">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Room</label>
                            <input name="inventoryRoom" type="text" class="form-control" id="inventoryRoom" placeholder="Inventory Room" maxlength="32">
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#userList" aria-expanded="false" aria-controls="collapseExample">
                                Give Access
                            </button>
                        </div>
                        <div class="collapse" id="userList">
                            <ul class="list-group">
                                <?php
                                $userSql = "SELECT `userId`, `userName` FROM `User` WHERE `userId` != $u_id;";

                                $userResult = mysqli_query($conn, $userSql);

                                if (mysqli_num_rows($userResult) > 0) {
                                    while ($row = mysqli_fetch_array($userResult)) {
                                        echo
                                        '<li class="list-group-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="' . $row['userId'] . '" id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault">
                                                    ' . $row['userName'] . '
                                                </label>
                                            </div>
                                        </li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item text-secondary">No Users</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Add Inventory</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- edit Item Modal -->
    <form action="" method="post">
        <div class="modal fade" tabindex="-1" id="editItemModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Item name</label>
                            <input type="text" class="form-control" id="newItemNameInput" placeholder="New Item Name" maxlength="32">
                        </div>

                        <div class="mb-3">
                            <div class="col" id="slctInventory">
                                <label for="selectInventory" class="col-auto col-form-label">Inventory</label>
                                <select class="form-select" aria-label="Defaultselectexample" name="selectInventory" id="selectInventory">

                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row g-3" id="selectStat&Cat">
                                <div class="col" id="slctCategory">
                                    <label for="" class=" col-auto col-form-label">Category</label>
                                    <select class="form-select" aria-label="Defaultselectexample" name="selectCategory" id="selectCategory">

                                    </select>
                                </div>
                                <div class="col" id="slctStatus">
                                    <label for="" class=" col-auto col-form-label">Status</label>
                                    <select class="form-select" aria-label="Defaultselectexample" name="selectStatus" id="selectStatus">

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row float-end">
                            <label for="inputAmount" class=" col-auto col-form-label">Quantity</label>
                            <div class="col-auto">
                                <input type="number" name="inputAmount" style="width: 6rem;" class="form-control" id="inputAmount" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark">Edit Item</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- edit Inventory Modal -->
    <form action="" method="post">
        <div class="modal fade" tabindex="-1" id="editInventoryModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="col" id="slctInventory">
                                <label for="selectInventory" class="col-auto col-form-label">Inventory to Edit</label>
                                <select class="form-select" aria-label="Defaultselectexample" name="selectInventory" id="selectInventorytoEdit" onchange="getStatusAndCatetoryList()">
                                    <option value="">- Select an Inventory to edit -</option>
                                    <!-- Gets the list inventory to be selected -->
                                    <?php

                                    $sql = "SELECT `inventoryId`, `inventoryName`, `inventoryRoom` FROM `Inventory` 
                                    WHERE `inventoryId` IN (SELECT inventoryId FROM UserAccessList WHERE userId = $u_id)";

                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="' . $row['inventoryId'] . '">' . $row['inventoryName'] . '</option>';
                                        }
                                    } else {
                                        echo '<option selected>No Inventory</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">New Inventory Name</label>
                            <input type="text" class="form-control" id="newInvNameInput" placeholder="New Inventory Name..">
                        </div>

                        <div class="mb-3">
                            <div class="row g-3">
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Category list</label>
                                    <ul class="list-group overflow-scroll border border-radius" style="max-height: 200px;" id="InvCatList">

                                    </ul>
                                </div>
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Status list</label>
                                    <ul class="list-group overflow-scroll border border-radius" style="max-height: 200px;" id="InvStatList">

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#userList" aria-expanded="false" aria-controls="collapseExample">
                                Update Access
                            </button>
                        </div>
                        <div class="collapse" id="userList">
                            <ul class="list-group">
                                <?php
                                $userSql = "SELECT `userId`, `userName` FROM `User` WHERE `userId` != $u_id;";

                                $userResult = mysqli_query($conn, $userSql);

                                if (mysqli_num_rows($userResult) > 0) {
                                    while ($row = mysqli_fetch_array($userResult)) {
                                        echo
                                        '<li class="list-group-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="users[]" value="' . $row['userId'] . '" id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        ' . $row['userName'] . '
                                                    </label>
                                                </div>
                                            </li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item text-secondary">No Users</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark">Edit Inventory</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- item history modal -->
    <div class="modal fade" tabindex="-1" id="itemHistoryModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="overflow-scroll">
                        <table class="table" style="width: 100%;" id="itmHistoryTable">
                            <!-- <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Item</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Input</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Item name</td>
                                    <td>CREATED</td>
                                    <td>name, quantity, etc</td>
                                    <td>0/0/0000</td>
                                    <td>00:00</td>
                                </tr>
                            </tbody> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="../js/scripts.js"></script>
    <!-- Ajax call script -->
    <script src="../js/ajaxScript.js"></script>
    <script>
        // for initializing function on load
        // the current inventory and item initial values
        var currItem = 0;
        var currInventory = 0;

        // getStatusAndCatetoryList();
        // getStatusAndCatetorySelected();


        // for initializing the bootstrap tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        // for opening the AlertResult toast
        const toastLive = document.getElementById('AlertResult')
        const toast = new bootstrap.Toast(toastLive)
        if (toastLive != null) {
            toast.show()
        }


        // gets the current selected inventory to show the proper status and category list
        var slctInv = document.getElementById("selectInventoryforItem");
        var selectedInvtoEdit = document.getElementById("selectInventorytoEdit");
        // slctInv.addEventListener("change", getStatusAndCatetorySelected(slctInv.value));

        function getStatusAndCatetorySelected() {
            var parameters = {
                "isBedingEdited": 0,
                "currentInventory": slctInv.value
            };

            requestResponseOutputAjax("selectCategory", "get_InventoryStatusList.php", createParameterString(parameters));
            requestResponseOutputAjax("selectStatus", "get_InventoryCategoryList.php", createParameterString(parameters));

        }

        // gets the current inventory and item value
        function currentItem(currItm) {
            currItem = currItm
            console.log(currItem)

            // call the function of get item history log
            getItemHistoryLog();
        }

        function currentInventory(currInv) {
            currInventory = currInv
            console.log(currInventory)

            // call the function to put in the values for the list
            // getStatusAndCatetoryList();
            // call the function to get the items inside the specific inventory
            getItemsList();
        }

        // Get the Inventory Status and Category List and put it in the Edit Inventory Modal
        function getStatusAndCatetoryList() {
            var parameters = {
                "isBedingEdited": 1,
                "currentInventory": selectedInvtoEdit.value
            };

            requestResponseOutputAjax("InvStatList", "get_InventoryStatusList.php", createParameterString(parameters));
            requestResponseOutputAjax("InvCatList", "get_InventoryCategoryList.php", createParameterString(parameters));
        }

        // Get the Inventory Items List
        function getItemsList() {
            var parameters = {
                "currentInventory": currInventory
            };
            requestResponseOutputAjax("inventoryItemsTable", "get_InventoryItemsList.php", createParameterString(parameters));
        }
        // Gets the Items history log
        function getItemHistoryLog() {
            var parameters = {
                "currentItem": currItem
            };
            requestResponseOutputAjax("itmHistoryTable", "get_ItemHistoryLog.php", createParameterString(parameters));
        }

        // get the item by name
        function searchByName() {
            var input = document.getElementById("searchBar").value;
            SearchItem(input, 1);
        }
        // get the item by category
        function searchByCategory(input) {
            SearchItem(input, 4);
            console.log(input);
        }

        // Search the item in the table
        function SearchItem(inp, ind) {
            // Declare variables
            var input, filter, table, tr, td, i, txtValue;
            // input = document.getElementById("searchBar");
            input = inp;
            // filter = input.value.toUpperCase();
            filter = input.toUpperCase();
            table = document.getElementById("itemsTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[ind];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // for downloading the barcode as an img
        function downloadBarcodeImg(itemName, barcode) {
            var itmName = itemName;
            var bcCode = barcode;
            const box = `
            <div class="card" data-bs-toggle="tooltip" data-bs-title="download Barcode" id="${itemName}" style="width: 16rem;" >
                <div class="card-body text-center">
                    <svg id="barcodeimg" class="barcode"></svg>
                    <p class="card-title m-0">${itemName}</p>
                </div>
            </div>`;
            document.getElementById("box").innerHTML = box;

            JsBarcode("#barcodeimg", bcCode, {
                format: "ITF",
                width: 1.5,
                height: 80,
            });
            console.log(itemName + " " + barcode);

            html2canvas(document.getElementById(itemName)).then(canvas => {
                // document.body.appendChild(canvas);
                var img = canvas.toDataURL();
                downloadURI(img, itemName + ".png");

            });
        }

        function downloadURI(uri, name) {
            var link = document.createElement("a");

            link.download = name;
            link.href = uri;
            document.body.appendChild(link);
            link.click();
            link.remove();
            document.getElementById("box").innerHTML = ""
        }
    </script>
</body>

</html>