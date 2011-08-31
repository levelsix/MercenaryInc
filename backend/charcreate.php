<?php 
include($_SERVER['DOCUMENT_ROOT'] . "/properties/playertypeproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/playerinitproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


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

$user = new User($charname);
$_SESSION['userID']=$user->createUser();
header("Location: $serverRoot/choosemission.php");
exit;
?>