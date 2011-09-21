<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

session_start();
$userID = $_SESSION['userID'];
$user = User::getUser($userID);
$playerType = $_POST['playertype'];
$user->setType($playerType);
header("Location: $serverRoot/battle.php");

exit;
?>