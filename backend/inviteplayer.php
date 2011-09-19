<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


session_start();

$agencyCode = $_GET['agencyCode'];
$user = User::getUser($_SESSION['userID']);

$result = $user->invitePlayer($agencyCode);

if ($result == "noUserWithAgencyCode") {
	$_SESSION['noUserWithAgencyCode'] = true;
}
if ($result == "success") {
	$_SESSION['successInvite'] = true;
}
if ($result == "fail") {
	header("Location: $serverRoot/errorpage.html");
	exit;
}


header("Location: $serverRoot/recruit.php");
exit;
?>