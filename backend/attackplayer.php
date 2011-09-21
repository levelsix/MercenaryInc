<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


function computeStat($skillPoints, $itemPoints) {
	// Definitely need to fix this formula
	$randomizedStat = rand(round(0.9 * $itemPoints), round(1.1 * $itemPoints));
	return $randomizedStat + $skillPoints;
}

function getItemStats($db, $userID, $agencySize, $statType) {
	return 1;
}
/*
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
*/
session_start();
$maxDamage = 24;
$id = $_SESSION['userID'];
$otherUserID = $_POST['userID'];

$user = User::getUser($id);

// Stamina check
$userStamina = $user->getStamina();
if ($userStamina <= 0) {
	$_SESSION['notEnoughStamina'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}

// Health checks
$userHealth = $user->getHealth();
if ($userHealth < $maxDamage + 1) {
	$_SESSION['notEnoughHealth'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}

$otherUser = User::getUser($otherUserID);
$otherUserHealth = $otherUser->getHealth();
if ($otherUserHealth < $maxDamage + 1) {
	$_SESSION['otherNotEnoughHealth'] = 1;
	header("Location: $serverRoot/battle.php");
	exit;
}


$userAttack = computeStat($user->getAttack(), getItemStats($db, $id, $user->getAgencySize(), "attack"));
$otherUserDefense = computeStat($otherUser->getDefense(), getItemStats($db, $otherUserID, $otherUser->getAgencySize(), "defense"));

$healthLoss = -15;

if ($userAttack > $otherUserDefense) { // user wins
	$_SESSION['won'] = 'true';
	$winner = $id;
	$loser = $otherUserID;
	
	$expGained = rand(1, 5);
	$_SESSION['expGained'] = $expGained;
	
	$user->updateHealthStaminaFightsExperience($healthLoss, -1, 1, 0, $expGained);
	$otherUser->updateHealthStaminaFightsExperience($healthLoss, 0, 0, 1, 0);
} else { // user loses
	$_SESSION['won'] = 'false';
	$winner = $otherUserID;
	$loser = $id;
	
	$expGained = rand(1, 3);
	// TODO Need to put exp gained in battle history for other player	

	$user->updateHealthStaminaFightsExperience($healthLoss, -1, 0, 1, 0);
	$otherUser->updateHealthStaminaFightsExperience($healthLoss, 0, 1, 0, $expGained);
}

$userLevel = $user->getLevel();
// The user's exp attribute in the user object should be updated to reflect this battle
$userExp = $user->getExperience();

$skillPointsGained = checkLevelUp($userLevel, $userExp);
if ($skillPointsGained > 0) {	
	$_SESSION['levelUp'] = 1;
	$_SESSION['newLevel'] = $user->getLevel();
	$_SESSION['skillPointsGained'] = $skillPointsGained;
}

header("Location: $serverRoot/battle.php");
exit;
?>