<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();

$userName = $_SESSION['user_name'];
$studId = $_SESSION['user_id'];

if (!isset($_SESSION['user_name'])) { //if not yet logged in
    header("Location: index.php"); // send to login page
    exit;
} else {
    include('db_config.php');
    $con = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
    mysql_select_db($DB_DATABASE);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>eSupervisor | Student</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css"/>
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.brown-orange.min.css" />
        <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
        <style>
            <!-- card -->
            .demo-card-wide.mdl-card {
                width: 550px;
            }
            .demo-card-wide > .mdl-card__title {
                color: #0000;
                height: 176px;
                background: url('http://en.finance.sia-partners.com/sites/default/files/styles/700x400/public/post/visuels/istock_000002313673_medium.jpg?itok=WmLosyIy') center / cover;
            }
            .demo-card-wide > .mdl-card__menu {
                color: #fff;
            }
            <!-- card end -->
        </style>
    </head>

    <body>

        <!-- Simple header with scrollable tabs. -->
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
            <header class="mdl-layout__header">
                <div class="mdl-layout__header-row"> 
                    <!-- Title --> 
                    <span class="mdl-layout-title">eSupervisor | Student Dashboard</span> </div>
                <div class="mdl-card__menu">
                    <a href = "logout.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"> Logout </a>
                </div>

                <!-- Tabs -->
                <div class="mdl-layout__tab-bar mdl-js-ripple-effect"> 
                    <a href="#scroll-tab-1" class="mdl-layout__tab is-active">My Claims</a> 
                    <a href="#scroll-tab-2" class="mdl-layout__tab">Guildlines</a> </div>
            </header>

            <main class="mdl-layout__content">
                <section class="mdl-layout__tab-panel is-active" id="scroll-tab-1">
                    <div class="page-content"> 
                        <!-- main -->

                        <div class="mdl-grid">
                            <div class="mdl-cell mdl-cell--4-col"> 
                                <!-- add claim card -->
                                <div class="demo-card-wide mdl-card mdl-shadow--2dp">
                                    <div class="mdl-card__title">
                                        <h2 class="mdl-card__title-text">Hi <?php echo $userName; ?>,</h2>
                                    </div>
                                    <div class="mdl-card__supporting-text"> You can add one or many claim. But you will have to upload evidence (documents or image) with your claim. </div>
                                    <div class="mdl-card__actions mdl-card--border">
                                        <button id="show-dialog" type="button" class="mdl-button">Report Now!</button><br/>

                                        <!-- submit claim on -->
                                        <?php
                                        if ($_POST['upload']) {
                                            if (isset($_FILES['image'])) {

                                                date_default_timezone_set('Asia/Colombo');
                                                $reg_date = date('Ymd');
                                                $reg_time = date('His');
                                                $reg_datetime = $reg_date . $reg_time . "_";

                                                $errors = array();

                                                $file_name = $_FILES['image']['name'];
                                                $file_size = $_FILES['image']['size'];
                                                $file_tmp = $_FILES['image']['tmp_name'];
                                                $file_type = $_FILES['image']['type'];
                                                $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                                                $expensions = array("jpeg", "jpg", "png", "pdf", "doc", "docx", "xsl");

                                                if (in_array($file_ext, $expensions) === false) {
                                                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                                }

                                                if ($file_size > 2097152) {
                                                    $errors[] = 'File size must be excately 2 MB';
                                                }

                                                if (empty($errors) == true) {
                                                    $file_url = $reg_datetime . $file_name;
                                                    move_uploaded_file($file_tmp, "evidence/" . $file_url);

                                                    $now_date = date('Y-m-d');
                                                    $now_time = date('H:i:s');
                                                    $submit_date = $now_date . " " . $now_time;
                                                    $end_date = date('Y-m-d H:i:s', strtotime("+14 days"));

                                                    $facId = $_POST['facultyId'];
                                                    $coorId = $_POST['coordinatorId'];
                                                    $clamName = $_POST['claimName'];
                                                    $location = $_POST['location'];
                                                    $reason = $_POST['reason'];

                                                    if ($clamName != "" && $location != "" && $reason != "") {
                                                        $result = mysql_query("INSERT INTO Claim(Student_StudID, Faculty_FacID, "
                                                                . "ECcoordinator_ECcoordID, NameOfClaim, Reason, Location, ClaimDate, EndDate) "
                                                                . "VALUES('$studId', '$facId', '$coorId', '$clamName', '$reason', '$location', '$submit_date', '$end_date')");

                                                        if ($result) {
                                                            $last_id = mysql_insert_id();

                                                            $result = mysql_query("INSERT INTO Evidence(EvidenceName, Claim_ClaimNo) "
                                                                    . "VALUES('$file_url', '$last_id')");

                                                            if ($result) {
                                                                echo "<div style ='font:8px; color:#B71C1C'> Successfully submited your claim. </div>";
                                                            } else {
                                                                echo "<div style ='font:8px; color:#B71C1C'> Upload Error try again! </div>";
                                                            }
                                                        } else {
                                                            echo "<div style ='font:8px; color:#B71C1C'> Error try again! </div>";
                                                        }
                                                    } else {
                                                        echo "<div style ='font:8px; color:#B71C1C'> All fields are required! </div>";
                                                    }
                                                } else {
                                                    if ($file_size == 0) {
                                                        echo "<div style ='font:8px; color:#B71C1C'> Please select an evidence. (PDF or Image) </div>";
                                                    } else {
                                                        echo "<div style ='font:8px; color:#B71C1C'> . $errors . </div>";
                                                    }
                                                }
                                            }
                                        } else if ($_POST['upload_more']) {

                                            if (isset($_FILES['image'])) {

                                                $errors = array();

                                                $file_name = $_FILES['image']['name'];
                                                $file_size = $_FILES['image']['size'];
                                                $file_tmp = $_FILES['image']['tmp_name'];
                                                $file_type = $_FILES['image']['type'];
                                                $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                                                $expensions = array("jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx", "xls");

                                                if (in_array($file_ext, $expensions) === false) {
                                                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                                }

                                                if ($file_size > 2097152) {
                                                    $errors[] = 'File size must be excately 2 MB';
                                                }

                                                if (empty($errors) == true) {
                                                    date_default_timezone_set('Asia/Colombo');
                                                    $reg_date = date('Ymd');
                                                    $reg_time = date('His');
                                                    $reg_datetime = $reg_date . $reg_time . "_";

                                                    $file_url = $reg_datetime . $file_name;
                                                    move_uploaded_file($file_tmp, "evidence/" . $file_url);

                                                    $claimId = $_POST['claim_id'];

                                                    $result = mysql_query("INSERT INTO Evidence(EvidenceName, Claim_ClaimNo) "
                                                            . "VALUES('$file_url', '$claimId')");
                                                } else {
                                                    if ($file_size == 0) {
                                                        echo "<div style ='font:8px; color:#B71C1C'> Please select an evidence. (PDF or Image)</div>";
                                                    } else {
                                                        echo "<div style ='font:8px; color:#B71C1C'> . $errors . </div>";
                                                    }
                                                }
                                            }
                                        } else if ($_POST['delete']) {

                                            $imgId = $_POST['img_id'];
                                            $result_delete = mysql_query("UPDATE Evidence SET is_visible = '0' WHERE EvidenceID = '$imgId'");
                                        }
                                        ?>
                                        <!-- sumbit claim end -->

                                    </div>
                                    <div class="mdl-card__menu">
                                        <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect"> <i class="material-icons">email</i> </button>
                                    </div>
                                </div>
                                <!-- end add claim --> 
                            </div>

                            <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet"> 

                                <!-- Calims list -->
                                <?php
                                $result_all = mysql_query("SELECT * FROM Claim WHERE Student_StudID = '$studId' ORDER BY ClaimDate DESC");

                                if (!empty($result_all)) {
                                    if (mysql_num_rows($result_all) > 0) {
                                        ?>
                                        <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                                            <thead>
                                                <tr>
                                                    <th class="mdl-data-table__cell--non-numeric">Description</th>
                                                    <th>Location</th>
                                                    <th>Decision</th>
                                                    <th>Decision Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $id = 1;
                                                while ($row = mysql_fetch_array($result_all)) {
                                                    ?> 
                                                    <tr>
                                                        <td class="mdl-data-table__cell--non-numeric">
                                                            <h5>(<?php echo $id++ . ") " . $row["NameOfClaim"]; ?></h5>
                                                            <span class="mdl-chip mdl-chip--contact">
                                                                <span class="mdl-chip__contact mdl-color--teal mdl-color-text--white">F</span>
                                                                <span class="mdl-chip__text">
                                                                    <?php
                                                                    $facId = $row["Faculty_FacID"];

                                                                    $result_faculty = mysql_query("SELECT * FROM Faculty WHERE FacID = '$facId'");
                                                                    $fac_row = mysql_fetch_array($result_faculty);
                                                                    echo $fac_row["FacName"];
                                                                    ?>
                                                                </span>
                                                            </span><br/>
                                                            <span class="mdl-chip mdl-chip--contact mdl-chip--deletable">
                                                                <img class="mdl-chip__contact" src="http://pngimages.net/sites/default/files/user-png-image-15189.png"></img>
                                                                <span class="mdl-chip__text">
                                                                    <?php
                                                                    $cordId = $row["ECcoordinator_ECcoordID"];

                                                                    $result_cordi = mysql_query("SELECT * FROM ECcoordinator WHERE ECcoordID = '$cordId'");
                                                                    $cor_row = mysql_fetch_array($result_cordi);
                                                                    $user_id = $cor_row["User_UserID"];

                                                                    $result_user = mysql_query("SELECT * FROM User WHERE UserID = '$user_id'");
                                                                    $user_row = mysql_fetch_array($result_user);

                                                                    echo $user_row["Fname"] . " " . $user_row["Surname"];
                                                                    ?>
                                                                </span>
                                                            </span><br/>
                                                            <span class="mdl-chip mdl-chip--contact">
                                                                <span class="mdl-chip__text">Claim Date :- </span>
                                                                <span class="mdl-chip__text">
                                                                    <?php echo $row["ClaimDate"]; ?>
                                                                </span>
                                                            </span><br/>
                                                            <span class="mdl-chip mdl-chip--contact">
                                                                <span class="mdl-chip__text">End Date :- </span>
                                                                <span class="mdl-chip__text">
                                                                    <?php echo $row["EndDate"]; ?>
                                                                </span>
                                                            </span><br/>
                                                        </td>
                                                        <td align="center" valign="top"><?php echo $row["Location"]; ?></td>
                                                        <td align="center" valign="top"><?php echo $row["Dicision"]; ?></td>
                                                        <td align="right" valign="top"><?php echo $row["DicisionDate"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div align="left">
                                                                <span class="mdl-chip mdl-chip--contact">
                                                                    <span class="mdl-chip__contact mdl-color--red mdl-color-text--white">R</span>
                                                                    <span class="mdl-chip__text">
                                                                        <?php echo $row["Reason"]; ?>
                                                                    </span>
                                                                </span>
                                                                <?php
                                                                $claimNo = $row["ClaimID"];
                                                                $result_img = mysql_query("SELECT * FROM Evidence WHERE Claim_ClaimNo = '$claimNo' AND is_visible = '1'");

                                                                if (!empty($result_img)) {
                                                                    if (mysql_num_rows($result_img) > 0) {
                                                                        ?>
                                                                        <table width="100%" align="center" border="0">
                                                                            <tr align="center">
                                                                                <?php
                                                                                while ($rowImg = mysql_fetch_array($result_img)) {
                                                                                    ?>
                                                                                    <td align="center">
                                                                                        <div align="left">
                                                                                            <form action = "" method = "POST">
                                                                                                <?php
                                                                                                $evidanceId = $rowImg["EvidenceID"];
                                                                                                $url = "http://ceylonapz.com/esupervisor/evidence/" . $rowImg["EvidenceName"];

                                                                                                $info = new SplFileInfo($url);
                                                                                                $fileType = $info->getExtension();

                                                                                                if ($fileType == "jpg" || $fileType == "jpeg" || $fileType == "png") {
                                                                                                    //image
                                                                                                    echo '<a href="' . $url . '"> <img align="center" width="50" height="50" src="' . $url . '" /> </a>';
                                                                                                } else if ($fileType == "pdf") {
                                                                                                    //pdf
                                                                                                    echo '<a href="' . $url . '"> <img align="center" width="50" height="50" src="https://cdn4.iconfinder.com/data/icons/CS5/256/ACP_PDF%202_file_document.png" /> </a>';
                                                                                                } else if ($fileType == "doc" || $fileType == "docx") {
                                                                                                    //word
                                                                                                    echo '<a href="' . $url . '"> <img align="center" width="50" height="50" src="https://windowsfileviewer.com/images/types/docx.png" /> </a>';
                                                                                                } else if ($fileType == "xlsx" || $fileType == "xls") {
                                                                                                    //excel
                                                                                                    echo '<a href="' . $url . '"> <img align="center" width="50" height="50" src="https://windowsfileviewer.com/images/types/xlsx.png" /> </a>';
                                                                                                } else {
                                                                                                    //other
                                                                                                    echo '<a href="' . $url . '"> <img align="center" width="50" height="50" src="https://www.waltons.co.za/images/products/400x400/57818504_400x400_72.jpg" /> </a>';
                                                                                                }
                                                                                                ?>
                                                                                                <br/>
                                                                                                <input type = "hidden" value="<?php echo $evidanceId; ?>" name="img_id"/>
                                                                                                <input type = "submit" value="Delete" name="delete" class="mdl-button mdl-js-button mdl-js-ripple-effect"/>
                                                                                            </form>
                                                                                        </div>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </tr>

                                                                        </table>
                                                                        <?php
                                                                    } else {
                                                                        echo '<br/>';
                                                                        echo 'No any evidence yet';
                                                                        echo '<br/>';
                                                                        echo '<br/>';
                                                                    }
                                                                } else {
                                                                    echo '<br/>';
                                                                    echo 'Server Problem...!';
                                                                    echo '<br/>';
                                                                    echo '<br/>';
                                                                }
                                                                ?>

                                                                <p> Upload More </p>
                                                                <form action = "" method = "POST" enctype = "multipart/form-data">
                                                                    <input type = "file" name = "image" /><br/>
                                                                    <input type = "submit" value="Upload" name="upload_more" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"/>
                                                                    <input type = "hidden" value="<?php echo $claimNo; ?>" name="claim_id"/>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    } else {
                                        echo 'No any Claims yet';
                                    }
                                } else {
                                    echo 'Problem...!';
                                }
                                ?>
                                <!-- claim list end --> 

                            </div>
                        </div>


                        <!-- dialog on-->
                        <dialog class="mdl-dialog">
                            <form action = "" method = "POST" enctype = "multipart/form-data">
                                <h4 class="mdl-dialog__title">Add Claims</h4>
                                <div class="mdl-dialog__content">

                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <label>Faculty</label>
                                        <select name="facultyId">
                                            <?php
                                            $result_faculty_data = mysql_query("SELECT * FROM Faculty");

                                            while ($fac_row_data = mysql_fetch_array($result_faculty_data)) {
                                                $id = $fac_row_data["FacID"];
                                                $name = $fac_row_data["FacName"];
                                                ?>
                                                <option value="<?php echo $id; ?>"> <?php echo $name; ?> </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <label> Coordinator </label> 
                                        <select name="coordinatorId">
                                            <?php
                                            $result_cordi_data = mysql_query("SELECT * FROM ECcoordinator");

                                            while ($cor_row_data = mysql_fetch_array($result_cordi_data)) {
                                                $ccoordID = $cor_row_data["ECcoordID"];
                                                $user_id_data = $cor_row_data["User_UserID"];

                                                $result_user_data = mysql_query("SELECT * FROM User WHERE UserID = '$user_id_data'");
                                                $user_row_data = mysql_fetch_array($result_user_data);
                                                ?>
                                                <option value="<?php echo $ccoordID; ?>"> <?php echo $user_row_data["Fname"] . " " . $user_row_data["Surname"]; ?> </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" id="claimName" name="claimName">
                                            <label class="mdl-textfield__label" for="claimName">Name of Claim</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" id="location" name="location">
                                            <label class="mdl-textfield__label" for="location">Location</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" id="location" name="reason">
                                            <label class="mdl-textfield__label" for="reason">Reason</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="file" id="location" name="image">
                                            <label class="mdl-textfield__label" for="image">Upload Evidence</label>
                                    </div>
                                </div>
                                <div class="mdl-dialog__actions">
                                    <input type = "submit" class="mdl-button" value="Add Now" name="upload"/>
                                    <button type="button" class="mdl-button close">Cancel</button>
                                </div>
                                <div align="center" style = "font-size:11px; color:#cc0000;">
                                    <?php echo $message; ?>
                                </div>
                            </form>
                        </dialog>
                        <script>
                            var dialog = document.querySelector('dialog');
                            var showDialogButton = document.querySelector('#show-dialog');
                            if (!dialog.showModal) {
                                dialogPolyfill.registerDialog(dialog);
                            }
                            showDialogButton.addEventListener('click', function () {
                                dialog.showModal();
                            });
                            dialog.querySelector('.close').addEventListener('click', function () {
                                dialog.close();
                            });
                        </script> 
                        <!-- dialog end --> 

                        <!-- main end --> 
                    </div>
                </section>
                <section class="mdl-layout__tab-panel" id="scroll-tab-2">
                    <div class="page-content">

                        <!-- tab 2 -->

                        <!-- Predict List End -->


                        <!-- tab 2 end -->
                    </div>
                </section>
            </main>

        </div>
    </body>
</html>
<?php
mysql_close($con);
?>