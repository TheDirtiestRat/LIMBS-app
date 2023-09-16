<?php
require "connectDatabase.php";
require_once 'startSession.php';

$curr_item = $_POST['currentItem'];

$sql = "SELECT `historyId`, Item.itemName, `historyAction`, `historyInput`, `historyDate`, `historyTime` FROM `ItemHistoryLog` 
LEFT JOIN Item ON ItemHistoryLog.itemId = Item.itemId WHERE ItemHistoryLog.itemId = $curr_item;";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo 
    '<thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Item</th>
            <th scope="col">Action</th>
            <th scope="col">Input</th>
            <th scope="col">Date</th>
            <th scope="col">Time</th>
        </tr>
    </thead>';
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        $i++;
        echo
        '<tbody>
            <tr>
                <th scope="row">'. $i .'</th>
                <td>' . $row['itemName'] . '</td>
                <td>' . $row['historyAction'] . '</td>
                <td>' . $row['historyInput'] . '</td>
                <td>' . $row['historyDate'] . '</td>
                <td>' . $row['historyTime'] . '</td>
            </tr>
        </tbody>';
    }
} else {
    echo '<h1 class=" text-secondary text-center">No History</h1>';
}