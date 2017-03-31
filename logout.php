<?php
   session_start();
   $_SESSION['user_name'] = null;
   if(session_destroy()) {
      header("location: index.php");
   }
?>