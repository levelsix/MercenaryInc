<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$userID = $_SESSION['userID'];
$targetID = $_GET['targetID'];
// need to do a check on whether or not the user even has that much money
$payment = $_GET['bountyAmount'];

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$bountyInsert = "INSERT INTO bounties (requester_id, target_id, payment) VALUES ("
	. $userID . ", " . $targetID . ", " . $payment . ");";
mysql_query($bountyInsert) or die(mysql_error());

$cashUpdate = "UPDATE users SET cash = cash - " . $payment . " WHERE id = " . $userID . ";";
mysql_query($cashUpdate) or die(mysql_error());

mysql_close();
$_SESSION['battleTab'] = 'bounty';
header("Location: $serverRoot/battle.php");
exit;
?>