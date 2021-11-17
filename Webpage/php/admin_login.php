<?php 

if (isset($_POST['signin'])) {
    $connect = mysqli_connect("sidewalksolve.xyz", "u722950798_admin", "Rsp010123131", "u722950798_sidewalksolve");

    $query = "SELECT * FROM `admin_login` WHERE `admin_username`='$_POST[admin_username]' AND `admin_password`='$_POST[admin_password]'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) == 1) {
        session_start();

        while ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['admin_name'] = $row['name'];
        }
        $_SESSION['auth'] = true;
        $_SESSION['start'] = time();
        $_SESSION['expire'] = $_SESSION['start'] + (40 * 60);
        header('location: ../admin.html');
    }
    else {
        if (empty($_POST['admin_username']) && empty($_POST['admin_password'])) {
            header('location: ../index.html?loginError=usernameandpassword&error=Username and password fields are required.');
        }
        else if (empty($_POST['admin_username'])){
            header('location: ../index.html?loginError=username&error=Username field is required.');
        }
        else if (empty($_POST['admin_password'])) {
            header('location: ../index.html?loginError=password&error=Password field is required.');
        }
        else {
            header('location: ../index.html?loginError=invalid&error=Username and password you entered is not a permitted account. Please double-check and try again.');
        }
    
    }
}
else {
    header('location: ../index.html');
}

?>