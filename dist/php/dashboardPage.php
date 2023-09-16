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
    <title>Dashboard</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/styles.css" rel="stylesheet" />
    <!-- chart js -->
    <script src="../../node_modules/chart.js/dist/chart.umd.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light
                    border-bottom">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle
                            navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item active"><a class="nav-link active" href="dashboardPage.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="fileMaintenancePage.php">File Maintenance</a></li>
                            <li class="nav-item"><a class="nav-link" href="transactionPage.php">Transaction</a></li>
                            <li class="nav-item"><a class="nav-link" href="reportsPage.php">Reports</a></li>
                            <!-- <li class="nav-item"><a class="nav-link" href="barcode.php">Barcodes</a></li> -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#!">Profile</a>
                                    <a class="dropdown-item" href="#!">Add new Users</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="loginPage.php">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page content-->
            <div class="container-fluid p-3">
                <h1 class=" display-3"><?php echo ucfirst($user); ?> Dashboard</h1>
                <hr>

                <div class="p-md-3">
                    <h3>Overview</h3>
                    <div class="row g-3 text-center">
                        <div class="col">
                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">
                                            Items total
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            $u_id = $_SESSION['userId'];

                                            if ($u_id == 1) {
                                                $sql = "SELECT COUNT(Item.itemId) as total_items FROM `Inventory` 
                                                LEFT JOIN Item ON Inventory.inventoryId = Item.inventoryId";
                                            } else {
                                                $sql = "SELECT COUNT(Item.itemId) as total_items FROM `Inventory` 
                                                LEFT JOIN Item ON Inventory.inventoryId = Item.inventoryId 
                                                WHERE userId = $u_id";
                                            }


                                            $result = mysqli_query($conn, $sql);

                                            $row = mysqli_fetch_assoc($result);
                                            $total_itms = $row['total_items'];
                                            ?>
                                            <h5 class="card-title"><?php echo $row['total_items']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">
                                            On stock
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            if ($u_id == 1) {
                                                $sql = "SELECT COUNT(`itemId`) AS remainingItems FROM `Item` WHERE `itemId` NOT IN (SELECT itemId FROM Transaction) AND `inventoryId` IN (SELECT inventoryId FROM Inventory);";
                                            } else {
                                                $sql = "SELECT COUNT(`itemId`) AS remainingItems FROM `Item` WHERE `itemId` NOT IN (SELECT itemId FROM Transaction) AND `inventoryId` IN (SELECT inventoryId FROM Inventory WHERE userId = $u_id);";
                                            }

                                            $result = mysqli_query($conn, $sql);

                                            $row = mysqli_fetch_assoc($result);
                                            ?>
                                            <h5 class="card-title"><?php echo $row['remainingItems']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-header">
                                        Items chart
                                    </div>
                                    <div class="card-body">
                                        <canvas id="barChart" style="height:
                                                200px;"></canvas>
                                    </div>
                                    <div class="card-footer text-muted">
                                        Last update <?php echo date("d/m/Y"); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-header">
                                    Inventory chart
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChart" style="height:
                                            200px;"></canvas>
                                </div>
                                <div class="card-footer text-muted">
                                    Last update <?php echo date("d/m/Y"); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-header">
                                    Transaction chart
                                </div>
                                <div class="card-body">
                                    <canvas id="lineChart" style="height:
                                            350px;"></canvas>
                                </div>
                                <div class="card-footer text-muted">
                                    Last update <?php echo date("d/m/Y"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- for the charts -->
    <?php
    //query for the transaction
    if ($u_id == 1) {
        $sql = "SELECT COUNT(transactionId) AS num_transactions, transactionDate  FROM `Transaction` GROUP BY transactionDate";
    } else {
        $sql = "SELECT COUNT(transactionId) AS num_transactions, transactionDate  FROM `Transaction` WHERE userId = $u_id GROUP BY transactionDate";
    }

    $result = mysqli_query($conn, $sql);

    $trns_date = array();
    $trns_values = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            array_push($trns_date, $row['transactionDate']);
            array_push($trns_values, $row['num_transactions']);
        }
    }

    $trns_json = json_encode($trns_date);
    $trns_v_json = json_encode($trns_values);


    //query for number of items total borrow
    if ($u_id == 1) {
        $sql = "SELECT Item.itemName, COUNT(transactionId) AS total FROM `Transaction` LEFT JOIN Item ON Transaction.itemId = Item.itemId GROUP BY transactionName;";
    } else {
        $sql = "SELECT Item.itemName, COUNT(transactionId) AS total FROM `Transaction` INNER JOIN Item ON Transaction.itemId = Item.itemId WHERE userId = $u_id GROUP BY transactionName;";
    }

    $result = mysqli_query($conn, $sql);

    $itm_names = array();
    $itm_values = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            array_push($itm_names, $row['itemName']);
            array_push($itm_values, $row['total']);
        }
    }

    $itm_n_json = json_encode($itm_names);
    $itm_v_json = json_encode($itm_values);

    if ($u_id == 1) {
        $sql = "SELECT inventoryName, COUNT(Item.inventoryId) as total FROM `Inventory` LEFT JOIN Item ON Inventory.inventoryId = Item.inventoryId GROUP BY inventoryName;";
    } else {
        $sql = "SELECT inventoryName, COUNT(Item.inventoryId) as total FROM `Inventory` LEFT JOIN Item ON Inventory.inventoryId = Item.inventoryId WHERE userId = $u_id GROUP BY inventoryName;";
    }

    $result = mysqli_query($conn, $sql);

    $inv_names = array();
    $inv_values = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            array_push($inv_names, $row['inventoryName']);
            array_push($inv_values, $row['total']);
        }
    }

    $inv_n_json = json_encode($inv_names);
    $inv_v_json = json_encode($inv_values);
    ?>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="../js/scripts.js"></script>

    <!-- Script for the chart JS -->
    <script>
        const ctx = document.getElementById('lineChart');
        const brc = document.getElementById('barChart');
        const pie = document.getElementById('pieChart');



        var transacValuesX = <?php echo $trns_json ?>;
        var transacValuesY = <?php echo $trns_v_json ?>;

        var itemsX = <?php echo $itm_n_json ?>;
        var itemsValueY = <?php echo $itm_v_json ?>;

        var inventoriesX = <?php echo $inv_n_json ?>;
        var inventoyrValuesY = <?php echo $inv_v_json ?>;


        var xValues = ["Mar", "Aprl", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
        var yValues = [13, 24, 33, 12, 32, 6, 71, 15, 22, 22];

        var barColors = ['rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
        ];
        var boarderColors = ['rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
        ];

        new Chart(ctx, {
            type: "line",
            data: {
                labels: transacValuesX,
                datasets: [{
                    label: 'Transaction Items Date:',
                    data: transacValuesY,
                    backgroundColor: barColors,
                    borderColor: boarderColors,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                maintainAspectRatio: false
            }
        });


        new Chart(brc, {
            type: 'bar',
            data: {
                labels: itemsX,
                datasets: [{
                    label: 'Item',
                    data: itemsValueY,
                    backgroundColor: barColors,
                    borderColor: boarderColors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            display: false
                        }
                    }
                },
                maintainAspectRatio: false
            }
        });



        new Chart(pie, {
            type: "doughnut",
            data: {
                labels: inventoriesX,
                datasets: [{
                    backgroundColor: barColors,
                    borderColor: boarderColors,
                    data: inventoyrValuesY
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "World Wide Wine Production"
                },
                maintainAspectRatio: false

            }
        });
    </script>
</body>

</html>