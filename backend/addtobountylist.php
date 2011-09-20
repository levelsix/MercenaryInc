<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Bounty.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


session_start();
$userID = $_SESSION['userID'];
$user = User::getUser($userID);

$payment = $_GET['bountyAmount'];

if ($payment > $user->getCash()) {
	$_SESSION['notEnoughCashForBounty'] = true;
	header("Location: $serverRoot/addplayertobounty.php");
} else {
	$targetID = $_GET['targetID'];
	
	$bounty = Bounty::createBounty($userID, $targetID, $payment);
	if (!$bounty) {
		header("Location: $serverRoot/errorpage.html");
		exit;
	}
	
	if (!$user->updateUserCash($payment*-1)){
		header("Location: $serverRoot/errorpage.html");
		exit;
	}
	$_SESSION['battleTab'] = 'bounty';
	header("Location: $serverRoot/battle.php");
}
exit;
?>