<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


$attribute = $_POST['attributeToIncrease'];

session_start();

$user = User::getUser($_SESSION['userID']);
$user->useSkillPoint($attribute);

header("Location: $serverRoot/skills.php");
?>