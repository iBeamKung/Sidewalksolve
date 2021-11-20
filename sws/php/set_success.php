<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connect = mysqli_connect("Host", "Username Database", "Password Database", "Database name");
    
    $sql = "UPDATE sws_data SET success=1 WHERE id=".$_POST['Id'];
    
    $result = mysqli_query($connect, $sql);
    
    mysqli_close($connect);
}


?>
