<?php 
include($_SERVER['DOCUMENT_ROOT'] . "/properties/playertypeproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/playerinitproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");

session_start();
$db = ConnectionFactory::getFactory()->getConnection();

$charname = $_POST['charname'];

/*
$playertype = $_POST['playertype'];
if (strcmp($playertype, $playertype1) == 0) {
	$attack = $playertype1atk;
	$defense = $playertype1def;
}
else if (strcmp($playertype, $playertype2) == 0) {
	$attack = $playertype2atk;
	$defense = $playertype2def;
}
else if (strcmp($playertype, $playertype3) == 0) {
	$attack = $playertype3atk;
	$defense = $playertype3def;
}
*/

$userStmt = $db->prepare("INSERT INTO users (name) VALUES (?)");
$userStmt->execute(array($charname));
$justAddedID = $db->lastInsertId();  

$userCitiesStmt = $db->prepare("INSERT INTO users_cities(user_id, city_id, rank_avail) VALUES (?, 1, 1)");
$userCitiesStmt->execute(array($justAddedID));

$_SESSION['userID']=$justAddedID;
header("Location: $serverRoot/choosemission.php");
exit;
?>