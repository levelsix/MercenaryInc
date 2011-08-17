<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");
session_start();

$agencyCode = $_GET['agencyCode'];
$userId = $_SESSION['userID'];

$userQuery = "SELECT * FROM users WHERE agency_code = " . $agencyCode . ";";
$userResult = mysql_query($userQuery);
$inviteeId = mysql_result($userResult, 0, "id");

$insertInvitation = "INSERT IGNORE INTO agencies (user_one_id, user_two_id, accepted) VALUES "
. "(" . $userId . ", " . $inviteeId . ", 0);";

mysql_query($insertInvitation) or die(mysql_error());
mysql_close();

header("Location: $serverRoot/recruit.php");
exit;
?>