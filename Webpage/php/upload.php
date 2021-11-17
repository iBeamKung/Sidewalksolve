<?php

// require_once 'connect.php';
// define ('SITE_ROOT', realpath(dirname(getcwd())));

if(isset($_POST['submit']) == 'submit' || $_SERVER['REQUEST_METHOD'] == 'POST') {

    $count = count($_FILES['upload']['name']);

    foreach($_FILES['upload']['tmp_name'] as $key => $value) {
        $file_names = $_FILES['upload']['name'];
        $extension = strtolower(pathinfo($file_names[$key], PATHINFO_EXTENSION));
        $video_extension = array('mp4', 'mov', 'wmv', 'flv');
        echo $extension;

        $new_name = $file_names[$key];
        if (in_array($extension, $video_extension)) {
            move_uploaded_file($_FILES['upload']['tmp_name'][$key], "../uploads/WWW/vdo/".$new_name);
        }
        else {
            move_uploaded_file($_FILES['upload']['tmp_name'][$key], "../uploads/WWW/image/".$new_name);
        }
        

    }
}
    
?>
