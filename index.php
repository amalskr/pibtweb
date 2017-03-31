<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include('db_config.php');
if (empty($_SESSION)) {
    // if the session not yet started 
    session_start();
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>eSupervisor | Login</title>
        <style>
            .demo-layout-transparent {
                background: url('https://www.gre.ac.uk/__data/assets/image/0018/1138113/gr-domes-night-01.jpg') center / cover;
            }
            .demo-layout-transparent .mdl-layout__header,
            .demo-layout-transparent .mdl-layout__drawer-button {
                /* This background is dark, so we set text to white. Use 87% black instead if
                   your background is light. */
                color: white;
            }

            <!-- card -->
            .demo-card-wide.mdl-card {
                width: 512px;
                margin: 0 auto;
            }
            .demo-card-wide > .mdl-card__title {
                color: #000000;
                height: 150px;
                background: url('https://www.hi-techmedical.org/aboutimg/courses/admini.png') center / cover;
            }
            .demo-card-wide > .mdl-card__menu {
                color: #fff;
            }

            body {
                padding: 0 0 0;
                background: #fafafa;
                position: relative;
            }
            <!-- card end -->
        </style>

        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css"/>
        <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

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
                    <!-- Wide card with share menu button -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-wide">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">eSupervisor</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <form action = "" method = "post">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" id="username" name="username"/>
                                    <label class="mdl-textfield__label" for="username">Username</label>
                                </div>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="password" id="password" name="password"/>
                                    <label class="mdl-textfield__label" for="password">Password</label>
                                </div>
                                <input type="submit" value="Login" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" />
                            </form>
                        </div>
                        <div class="mdl-card__actions mdl-card--border">

                            <p> </p>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                                $con = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
                                $db = mysql_select_db($DB_DATABASE);

                                if (mysqli_connect_errno()) {
                                    echo "Connot connect to the server!";
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
                                                    echo $_SESSION['user_name'];
                                                    echo"<script>window.open('student.php','_self')</script>";  
                                                    //exit;
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
                                }
                            }
                            ?><br/><br/>
                            <font color="#AD1457" size="2"> eSupervisor authorized users only. </font><br/>
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                Forgot Password
                            </a>
                        </div>
                    </div>
                </center>

            </main>
        </div>
    </body>
</html>
<?php
mysqli_close($db);
?>