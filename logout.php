<?php
session_start();
$_SESSION["logged_in"]=false;
session_unset();
header("location:index.php");
#echo '<script type="text/javascript">window.location="home.php";</script>';
?>