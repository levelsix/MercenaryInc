<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");
session_start();
$accepted = $_POST['accepted'];
$inviterID = $_POST['inviterID'];
$userID = $_SESSION['userID'];
if ($accepted == 'true') {
	$updateQuery = "UPDATE agencies SET accepted = 1 WHERE user_one_id = "
		. $inviterID . " AND user_two_id = " . $userID . ";";
	mysql_query($updateQuery) or die(mysql_error());
	$updateAgencySizeQuery = "UPDATE users SET agency_size = agency_size + 1 WHERE id = "
		. $userID . ";";
	mysql_query($updateAgencySizeQuery) or die(mysql_error());
	$updateAgencySizeQuery = "UPDATE users SET agency_size = agency_size + 1 WHERE id = "
		. $inviterID . ";";
	mysql_query($updateAgencySizeQuery) or die(mysql_error());
} else {
	$deleteQuery = "DELETE FROM agencies WHERE user_one_id = " . $inviterID
		. " AND user_two_id = " . $userID . ";";
	mysql_query($deleteQuery) or die(mysql_error());
}
mysql_close();
header("Location: $serverRoot/recruit.php");
exit;
?>