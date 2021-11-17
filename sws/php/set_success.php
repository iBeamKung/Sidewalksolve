<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connect = mysqli_connect("sidewalksolve.xyz", "u722950798_admin", "Rsp010123131", "u722950798_sidewalksolve");
    
    $sql = "UPDATE sws_data SET success=1 WHERE id=".$_POST['Id'];
    
    $result = mysqli_query($connect, $sql);
    
    mysqli_close($connect);
}


?>