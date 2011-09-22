<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

session_start();
$healCost = $_POST['healCost'];
$user = User::getUser($_SESSION['userID']);

$user->healAtHospital($healCost);

header("Location: $serverRoot/hospital.php");
?>