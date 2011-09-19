<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

session_start();

$accepted = $_POST['accepted'];
$inviterID = $_POST['inviterID'];
$userID = $_SESSION['userID'];

$user = User::getUser($userID);

if ($accepted == 'true') {
	$user->acceptInvite($inviterID);
} else {
	$user->rejectInvite($inviterID);
}

header("Location: $serverRoot/recruit.php");
exit;
?>