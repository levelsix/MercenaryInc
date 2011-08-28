<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$userID = $_SESSION['userID'];
$targetID = $_GET['targetID'];
// need to do a check on whether or not the user even has that much money
$payment = $_GET['bountyAmount'];

$db = ConnectionFactory::getFactory()->getConnection();

$bountyStmt = ConnectionFactory::insertIntoBounties($userID, $targetID, $payment);
if ($bountyStmt == NULL) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$cashUpdateStmt = ConnectionFactory::updateUserCash($payment*-1, $userID);
if ($cashUpdateStmt == NULL) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$_SESSION['battleTab'] = 'bounty';
header("Location: $serverRoot/battle.php");
exit;
?>