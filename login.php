<?php
session_start();
$response = array();
include('db_config.php');

$con = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db($DB_DATABASE);

/* check connection */
if (mysqli_connect_errno()) {

    $response["success"] = 0;
    $response["message"] = "Connot connect to the server!";
    echo json_encode($response);
} else {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $un = $_POST['username'];
        $pw = $_POST['password'];
        //$pw = md5($password, TRUE);

        $resultAva = mysql_query("SELECT * FROM User WHERE UserName = '$un'");
        $no_of_rows = mysql_num_rows($resultAva);

        if ($no_of_rows > 0) {

            $resultAva = mysql_query("SELECT * FROM User WHERE UserName = '$un' AND Password = '$pw'");
            $no_of_rows = mysql_num_rows($resultAva);

            if ($no_of_rows > 0) {

                //SEND USER DATA
                $rowNoti = mysql_fetch_array($resultAva);
                $userType = $rowNoti["UserType"];
                $firstName = $rowNoti["Fname"];
                $lastName = $rowNoti["Surname"];
                $name = $firstName . " " . $lastName;

                $_SESSION['user_name'] = $name;
                $_SESSION['user_id'] = $rowNoti["UserID"];
                
                if ($userType == "admin") {
                    
                } else if ($userType == "coordinator") {
                    
                } else if ($userType == "manager") {
                    
                } else if ($userType == "student") {
                    header('location: student.php');
                }
            } else {
                echo "Incorrect Password";
            }
        } else {
            echo "Invalid Username";
        }
    } else {
        echo "Required field(s) is missing.";
    }

    /* close connection */
    mysqli_close($db);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>eSupervisor | Login</title>
        <style>
            .demo-layout-transparent {
                height: 100%;
                background: url('https://www.gre.ac.uk/__data/assets/image/0018/1138113/gr-domes-night-01.jpg') center / cover;
            }
            .demo-layout-transparent .mdl-layout__header,
            .demo-layout-transparent .mdl-layout__drawer-button {
                /* This background is dark, so we set text to white. Use 87% black instead if
                   your background is light. */
                color: white;
            }

        </style>

    </head>
    <body>

        <div class="demo-layout-transparent mdl-layout mdl-js-layout">
            <header class="mdl-layout__header mdl-layout__header--transparent">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title"></span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>

                </div>
            </header>

            <main class="mdl-layout__content">

                <center>



                </center>
            </main>
        </div>
    </body>
</html>
