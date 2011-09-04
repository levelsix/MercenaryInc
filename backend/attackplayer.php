<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

function computeStat($skillPoints, $itemPoints) {
	$randomizedStat = rand(round(0.9 * $itemPoints), round(1.1 * $itemPoints));
	return $randomizedStat;
}

function getItemStats($db, $userID, $agencySize, $statType) {
	return 1;
}

function updateUserHealthAndFightsRecord($db, $winnerID, $loserID) {
	$healthLoss = 15;	
	
	$updateWinnerStmt = $db->prepare("UPDATE users SET health = health - ?, fights_won = fights_won + 1 WHERE id = ?");	
	$updateWinnerStmt->execute(array($healthLoss, $winnerID));
	
	$updateLoserStmt = $db->prepare("UPDATE users SET health = health - ?, fights_lost = fights_lost + 1 WHERE id = ?");
	$updateLoserStmt->execute(array($healthLoss, $loserID));	
}

function updateUserStamina($db, $userID) {
	$updateStmt = $db->prepare("UPDATE users SET stamina = stamina - 1 WHERE id = ?");
	$updateStmt->execute(array($userID));
}

$maxDamage = 24;

session_start();
$id = $_SESSION['userID'];
$otherUserID = $_POST['userID'];

$db = ConnectionFactory::getFactory()->getConnection();

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute(array($id));

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);

$userHealth = $userResult['health'];
if ($userHealth < $maxDamage + 1) {
	$_SESSION['notEnoughHealth'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}
$userStamina = $userResult['stamina'];
if ($userStamina <= 0) {
	$_SESSION['notEnoughStamina'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}

$otherUserStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$otherUserStmt->execute(array($otherUserID));

$otherUserResult = $otherUserStmt->fetch(PDO::FETCH_ASSOC);

$otherUserHealth = $otherUserResult['health'];
if ($otherUserHealth < $maxDamage + 1) {
	$_SESSION['otherNotEnoughHealth'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}

$userAttack = computeStat($userResult['attack'], getItemStats($db, $id, $userResult['agency_size'], "attack"));
$otherUserDefense = computeStat($otherUserResult['defense'], getItemStats($db, $otherUserID, $otherUserResult['agency_size'], "defense"));

if ($userAttack > $otherUserDefense) {
	$_SESSION['won'] = 'true';
	$winner = $id;
	$loser = $otherUserID;
} else {
	$_SESSION['won'] = 'false';
	$winner = $otherUserID;
	$loser = $id;
}

updateUserHealthAndFightsRecord($db, $winner, $loser);
updateUserStamina($db, $id);

header("Location: $serverRoot/battle.php");
exit;
?>