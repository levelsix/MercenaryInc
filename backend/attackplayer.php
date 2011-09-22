<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");

function computeStat($skillPoints, $itemPoints) {
	// Definitely need to fix this formula
	$randomizedStat = rand(round(0.9 * $itemPoints), round(1.1 * $itemPoints));
	return $randomizedStat + $skillPoints;
}

function getItemStats($userID, $agencySize, $statType) {
	$topItems = User::getUsersTopItemsByStatIDsToQuantity($userID, $agencySize, $statType);
	$itemObjs = Item::getItems(array_keys($topItems));
	
	$totalStat = 0;
	foreach ($itemObjs as $item) {
		$itemID = $item->getID();
		if ($statType == "attack") {
			$stat = $item->getAtkBoost();
		} else if ($statType == "defense") {
			$stat = $item->getDefBoost();
		}
		
		$totalStat += $stat * $topItems[$itemID];
	}
	
	return $totalStat;
}

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


$userAttack = computeStat($user->getAttack(), getItemStats($id, $user->getAgencySize(), "attack"));
$otherUserDefense = computeStat($otherUser->getDefense(), getItemStats($otherUserID, $otherUser->getAgencySize(), "defense"));

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

// Level up check
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