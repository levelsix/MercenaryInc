<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$accepted = $_POST['accepted'];
$inviterID = $_POST['inviterID'];
$userID = $_SESSION['userID'];

$db = ConnectionFactory::getFactory()->getConnection();

function incrementAgencySize($db, $id) {
	$updateAgencySizeStmt = $db->prepare("UPDATE users SET agency_size = agency_size + 1 WHERE id = ?");
	if (!($updateAgencySizeStmt->execute(array($id)))) {
		header("Location: $serverRoot/errorpage.html");
		exit;
	}
}

if ($accepted == 'true') {
	$updateStmt = $db->prepare("UPDATE agencies SET accepted = 1 WHERE user_one_id = ? AND user_two_id = ?");
	if (!($updateStmt->execute(array($inviterID, $userID)))) {
		header("Location: $serverRoot/errorpage.html");
		exit;
	}
	incrementAgencySize($db, $userID);
	incrementAgencySize($db, $inviterID);
} else {
	$deleteStmt = $db->prepare("DELETE FROM agencies WHERE user_one_id = ? AND user_two_id = ?");
	if (!($deleteStmt->execute(array($inviterID, $userID)))) {
		header("Location: $serverRoot/errorpage.html");
		exit;
	}
}

header("Location: $serverRoot/recruit.php");
exit;
?>