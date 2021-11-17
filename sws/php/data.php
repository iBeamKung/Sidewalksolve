<?php

$connect = mysqli_connect("Host", "Username Database", "Password Database", "Name Database");

$result = mysqli_query($connect, "SELECT * FROM sws_data");
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);

?>