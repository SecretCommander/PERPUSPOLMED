<?php
session_start();
unset($_SESSION['petugas_id']);
unset($_SESSION['adminloggedin']);
session_destroy();

header("location: admin.php");
?>