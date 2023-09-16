<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "School_Inventory_Monitoring_DB";

$conn = mysqli_connect($host, $user, $password, $database);

if(mysqli_connect_errno()) {
    echo "Failed to connect to MySQL:" . mysqli_connect_error();
}

?>