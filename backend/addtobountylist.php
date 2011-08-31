<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/Bounty.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


session_start();

$userID = $_SESSION['userID'];
$targetID = $_GET['targetID'];
// need to do a check on whether or not the user even has that much money
$payment = $_GET['bountyAmount'];

$bounty = new Bounty($userID, $targetID, $payment);
if (!$bounty->createBounty()) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$user = User::getUser($userID);
if (!$user->updateUserCash($payment*-1)){
	header("Location: $serverRoot/errorpage.html");
	exit;
}



/*

$cashUpdateStmt = ConnectionFactory::updateUserCash($payment*-1, $userID);
if ($cashUpdateStmt == NULL) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}*/


$_SESSION['battleTab'] = 'bounty';
header("Location: $serverRoot/battle.php");
exit;
?>