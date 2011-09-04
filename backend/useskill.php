<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

$attribute = $_POST['attributeToIncrease'];

session_start();
$db = ConnectionFactory::getFactory()->getConnection();

if ($attribute == 'attack') {
	$skillStmt = $db->prepare("UPDATE users SET skill_points=skill_points-1 WHERE id=?");
	$attributeStmt = $db->prepare("UPDATE users SET attack=attack+1 WHERE id=?");
}
if ($attribute == 'defense') {
	$skillStmt = $db->prepare("UPDATE users SET skill_points=skill_points-1 WHERE id=?");
	$attributeStmt = $db->prepare("UPDATE users SET defense=defense+1 WHERE id=?");
}
if ($attribute == 'energymax') {
	$skillStmt = $db->prepare("UPDATE users SET skill_points=skill_points-1 WHERE id=?");
	$attributeStmt = $db->prepare("UPDATE users SET energy_max=energy_max+1 WHERE id=?");
}
if ($attribute == 'healthmax') {
	$skillStmt = $db->prepare("UPDATE users SET skill_points=skill_points-1 WHERE id=?");
	$attributeStmt = $db->prepare("UPDATE users SET health_max=health_max+10 WHERE id=?");
}
if ($attribute == 'staminamax') {
	$skillStmt = $db->prepare("UPDATE users SET skill_points=skill_points-2 WHERE id=?");
	$attributeStmt = $db->prepare("UPDATE users SET stamina_max=stamina_max+1 WHERE id=?");
}

$skillStmt->execute(array($_SESSION['userID']));
$attributeStmt->execute(array($_SESSION['userID']));



header("Location: $serverRoot/skills.php");
?>