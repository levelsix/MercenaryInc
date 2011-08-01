<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$userID = $_GET['userID'];
?>