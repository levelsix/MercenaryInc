<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Mission.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/UserMissionData.php");


function redirect($location) {
	header("Location: $location");
	exit;
}

function agencyIsLargeEnough($mission, $user) {
	
	$minAgencySize = $mission->getMinAgencySize();
	$playerAgencySize = $user->getAgencySize();
	
	$playerHasEnoughAgency = ($playerAgencySize >= $minAgencySize);
	if (!$playerHasEnoughAgency) {
		$_SESSION['needMoreAgency'] = $minAgencySize - $playerAgencySize;
	}
	return $playerHasEnoughAgency;
}

function playerHasEnoughEnergy($mission, $user) {
	$minEnergy = $mission->getEnergyCost();
	$playerEnergy = $user->getEnergy();
	$playerHasEnoughEnergy = $playerEnergy >= $minEnergy;
	if (!$playerHasEnoughEnergy) {
		$_SESSION['needMoreEnergy'] = $minEnergy - $playerEnergy;
	}
	
	return $playerHasEnoughEnergy;
}

function playerHasRequireditems($requiredItemIDsToQuantity, $userItemIDsToQuantity) {
	$itemsMissing = array();
	$playerHasAllRequiredItems = true;
		
	foreach ($requiredItemIDsToQuantity as $reqItemID => $quantityReq) {	

		if (array_key_exists($reqItemID, $userItemIDsToQuantity)) {
			$userQuantity = $userItemIDsToQuantity[$reqItemID];
			if ($userQuantity < $quantityReq)  {
				$playerHasAllRequiredItems = false;
				$amountMissing = $quantityReq - $userQuantity;
				$itemsMissing[$itemID] = $amountMissing;
			}
		} else {
			$playerHasAllRequiredItems = false;
			$itemsMissing[$itemID] = $amountMissing;
		}
	}
	if (!$playerHasAllRequiredItems) {
		$_SESSION['itemsMissing'] = $itemsMissing;
	}
	return $playerHasAllRequiredItems;
}

//under this model, cityrank doesnt increase until every missions currRank is ready at new number
//currRank should really be rankMissionIsReadyFor
function handleRanks($user, $mission) {
	$userMissionData = UserMissionData::getUserMissionData($user->getID(), $mission->getID());
	if (!$userMissionData) {
		$userMissionData = UserMissionData::createUserMissionData($user->getID(), $mission->getID());
	} else {
		$userMissionData->completeMission($mission);
	}
}


session_start();

$missionID=$_POST['missionID'];
$userID=$_SESSION['userID'];

$mission = Mission::getMission($missionID);
if (!$mission) {
	redirect("$serverRoot/errorpage.html");
}

$user = User::getUser($userID);
if (!$user) {
	redirect("$serverRoot/errorpage.html");
}


$requiredItemIDsToQuantity = Mission::getMissionRequiredItemsIDsToQuantity($missionID);
$userItemIDsToQuantity = User::getUsersItemsIDsToQuantity($user->getID());


$doMission = true;

if (!agencyIsLargeEnough($mission, $user)) {
	$doMission = false;
}

if (!playerHasEnoughEnergy($mission, $user)) {
	$doMission = false;
}

if (!playerHasRequireditems($requiredItemIDsToQuantity, $userItemIDsToQuantity)) {
	$doMission = false;
}

function associateItemsWithIDs($items) {
	$toreturn = array();
	foreach ($items as $item) {
		$toreturn[$item->getID()] = $item;
	}
	return $toreturn;
}

if ($doMission) {
	$_SESSION['missionsuccess'] = "true";
	
	$itemsLost = array();
	$hasLostItems = false;
	$missionItems = Item::getItemIDsToItems(array_keys($requiredItemIDsToQuantity));
	foreach ($requiredItemIDsToQuantity as $reqItemID => $quantityReq) {
		$random = rand(0, 100);
		$missionItem = $missionItems[$reqItemID];
		$chanceLoss = $missionItem->getChanceOfLoss();		
		if ($random < $chanceLoss*100) {
			if (!$user->decrementUserItem($reqItemID, 1)) {
				redirect("$serverRoot/errorpage.html");
			} else {
				$userItemIDsToQuantity[$reqItemID]--;
				$hasLostItems = true;
				array_push($itemsLost, $reqItemID);
			}
		}
	}
	if ($hasLostItems) {
		$_SESSION['itemsLost'] = $itemsLost;
	}
	
	//energy lost
	$_SESSION['energyLost']=$mission->getEnergyCost();
	
	//loot gained
	$random = rand(0, 100);
	$chanceLoot = $mission->getChanceOfLoot();
	if ($random < $chanceLoot * 100) {
		$lootItemID = $mission->getLootItemID();
		if (!$user->incrementUserItem($lootItemID, 1)) {
			redirect("$serverRoot/errorpage.html");
		} else {
			$userItemIDsToQuantity[$lootItemID]++;
			$_SESSION['gainedLootItemID'] = $lootItemID;
		}		
	}
	
	handleRanks($user, $mission);
	
	// Get multiplier based on whether or not the player completed a mission rank
	$multiplier = 1;
	if (isset($_SESSION['justUnlockedThisMissionRank'])) {
		$multiplier = $_SESSION['justUnlockedThisMissionRank'];
	}
	
	$cashGained = $mission->getRandomCashGained();
	$_SESSION['baseCashGained'] = $cashGained;
	
	$expGained = $mission->getExpGained();
	$_SESSION['baseExpGained'] = $expGained;
	
	if (isset($_SESSION['justUnlockedThisMissionRank'])) {
		$_SESSION['extraCashGained'] = $cashGained * ($multiplier - 1);
		$_SESSION['extraExpGained'] = $expGained * ($multiplier - 1);
	}
	
	if (!$user->updateUserEnergyCashExpCompletedmissions($mission->getEnergyCost(), $cashGained*$multiplier, $expGained*$multiplier)) {
		redirect("$serverRoot/errorpage.html");
	}
	
	
	/*
	// Level up check
	$userLevel = $userResult['level'];
	// The user exp is a value from an old query, so add the experience gained
	$userExp = $userResult['experience'] + ($expGained * $multiplier);
	
	$levelUpArr = userLeveledUp($userLevel, $userExp);
	if ($levelUpArr) {
		$newLevel = $levelUpArr['newLevel'];
		$skillPointsGained = $levelUpArr['skillPointsGained'];
	
		// Update the db
		$updateStmt = $db->prepare("UPDATE users SET level = ?, skill_points = skill_points + ? WHERE id = ?");
		$updateStmt->execute(array($newLevel, $skillPointsGained, $userID));
	
		$_SESSION['levelUp'] = 1;
		$_SESSION['newLevel'] = $newLevel;
		$_SESSION['skillPointsGained'] = $skillPointsGained;
	}*/
	
	
}
else {
	$_SESSION['missionfail'] = "true";
}
$_SESSION['currentMissionCity'] = $_POST['currentMissionCity'];
header("Location: $serverRoot/choosemission.php");

?>