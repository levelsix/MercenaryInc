<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$userID = $_SESSION['userID'];
$targetID = $_GET['targetID'];
// need to do a check on whether or not the user even has that much money
$payment = $_GET['bountyAmount'];

$db = ConnectionFactory::getFactory()->getConnection();

$bountyInsertStmt = $db->prepare("INSERT INTO bounties (requester_id, target_id, payment) VALUES (?, ?, ?)");
if (!($bountyInsertStmt->execute(array($userID, $targetID, $payment)))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$cashUpdateStmt = $db->prepare("UPDATE users SET cash = cash - ? WHERE id = ?");
if (!($cashUpdateStmt->execute(array($payment, $userID)))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$_SESSION['battleTab'] = 'bounty';
header("Location: $serverRoot/battle.php");
exit;
?>