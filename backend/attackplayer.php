<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

function computeStat($skillPoints, $itemPoints) {
	// Definitely need to fix this formula
	$randomizedStat = rand(round(0.9 * $itemPoints), round(1.1 * $itemPoints));
	return $randomizedStat + $skillPoints;
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

function updateUserExp($db, $userID, $exp) {
	$updateStmt = $db->prepare("UPDATE users SET experience = experience + ? WHERE id = ?");
	$updateStmt->execute(array($exp, $userID));
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
	
	$expGained = rand(1, 5);
	$_SESSION['expGained'] = $expGained;
	
	updateUserExp($db, $id, $expGained);
} else {
	$_SESSION['won'] = 'false';
	$winner = $otherUserID;
	$loser = $id;
	
	$expGained = rand(1, 3);
	// Need to put exp gained in battle history for other player	
	updateUserExp($db, $otherUserID, $expGained);
}

updateUserHealthAndFightsRecord($db, $winner, $loser);
updateUserStamina($db, $id);

$userLevel = $userResult['level'];
// The user exp is a value from an old query, so add the experience gained
$userExp = $userResult['experience'] + $expGained;

$levelUpArr = userLeveledUp($userLevel, $userExp);
if ($levelUpArr) {
	$newLevel = $levelUpArr['newLevel'];
	$skillPointsGained = $levelUpArr['skillPointsGained'];
	
	// Update the db
	$updateStmt = $db->prepare("UPDATE users SET level = ?, skill_points = skill_points + ? WHERE id = ?");
	$updateStmt->execute(array($newLevel, $skillPointsGained, $id));

	$_SESSION['levelUp'] = 1;
	$_SESSION['newLevel'] = $newLevel;
	$_SESSION['skillPointsGained'] = $skillPointsGained;
}

header("Location: $serverRoot/battle.php");
exit;
?>