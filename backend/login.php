<?php
include("../properties/dbproperties.php"); 

// Set the ID in the session
$id = $_POST['id'];
session_start();
$_SESSION['userID'] = $id;

// Update last_login in database
mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$timestampUpdate = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = " . $id . ";";
mysql_query($timestampUpdate) or die(mysql_error());

header("Location: ../charhome.php");
exit;
?>