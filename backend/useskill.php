<?php
include("../properties/dbproperties.php");
include("../properties/dbcolumnnames.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$attribute = $_POST['attributeToIncrease'];

if (strcmp($attribute, 'attack') == 0) {
	$columnname = $userTableAttackCol;
}
if (strcmp($attribute, 'defense') == 0) {
	$columnname = $userTableDefenseCol;
}
if (strcmp($attribute, 'energymax') == 0) {
	$columnname = $userTableEnergyMaxCol;
}
if (strcmp($attribute, 'healthmax') == 0) {
	$columnname = $userTableHealthMaxCol;
}
if (strcmp($attribute, 'staminamax') == 0) {
	$columnname = $userTableStaminaMaxCol;
}

session_start();
$query = "UPDATE users SET skill_points=skill_points-1 WHERE id=" . $_SESSION['userID'] . ";";
mysql_query($query) or die(mysql_error());

$query = "UPDATE users SET ".$columnname."=".$columnname."+1 WHERE id=" . $_SESSION['userID'] . ";";
mysql_query($query) or die(mysql_error());

mysql_close();

header("Location: ../skills.php");
?>