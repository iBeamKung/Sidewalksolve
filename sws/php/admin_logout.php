<?php 
    
    if ($_POST['action'] == 'Logout') {
        session_start();
        
        session_unset();
        session_destroy();
        
        header('location: ../index.html');
    }
    else {
        header('location: ../index.html');
    }

?>