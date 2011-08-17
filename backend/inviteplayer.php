<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$agencyCode = $_GET['agencyCode'];
$userId = $_SESSION['userID'];

$db = ConnectionFactory::getFactory()->getConnection();

$userStmt = $db->prepare("SELECT id FROM users WHERE agency_code = ?");
$userStmt->execute(array($agencyCode));

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$userResult) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$inviteeId = $userResult["id"];

$insertInvitationStmt = $db->prepare("INSERT IGNORE INTO agencies (user_one_id, user_two_id, accepted) VALUES (?, ?, 0)");
if (!($insertInvitationStmt->execute(array($userId, $inviteeId)))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

header("Location: $serverRoot/recruit.php");
exit;
?>