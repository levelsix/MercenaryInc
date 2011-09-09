<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");

function redirect($location) {
	header("Location: $location");
	exit;
}

function agencyIsLargeEnough($missionResult, $userResult) {
	$minAgencySize = $missionResult["min_agency_size"];
	$playerAgencySize = $userResult["agency_size"];
	
	$playerHasEnoughAgency = ($playerAgencySize >= $minAgencySize);
	if (!$playerHasEnoughAgency) {
		$_SESSION['needMoreAgency'] = $minAgencySize - $playerAgencySize;
	}
	return $playerHasEnoughAgency;
}

function playerHasEnoughEnergy($missionResult, $userResult) {
	$minEnergy = $missionResult["energy_cost"];
	$playerEnergy = $userResult["energy"];
	$playerHasEnoughEnergy = $playerEnergy >= $minEnergy;
	if (!$playerHasEnoughEnergy) {
		$_SESSION['needMoreEnergy'] = $minEnergy - $playerEnergy;
	}
	
	return $playerHasEnoughEnergy;
}

function playerHasRequireditems($db, $itemReqStmt, $userID) {
	$itemsMissing = array();
	$playerHasAllRequiredItems = true;
	
	while ($row = $itemReqStmt->fetch(PDO::FETCH_ASSOC)) {
		$itemID = $row["item_id"];
		$itemQuantity = $row["item_quantity"];
		
		$userItemsStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ? AND item_id = ?");
		$userItemsStmt->execute(array($userID, $itemID));
		
		$userItemsResult = $userItemsStmt->fetch(PDO::FETCH_ASSOC);
		if (!$userItemsResult) {
			redirect($GLOBALS['serverRoot'] . "/errorpage.html");
		}
		
		$numRows = $userItemsStmt->rowCount();
		$quantity = $userItemsResult["quantity"];
		if ($numRows <= 0 || $userItemsResult["quantity"] < $itemQuantity) {
			$playerHasAllRequiredItems = false;
			if ($numRows <= 0) {
				$amountMissing = $itemQuantity;
			} else {
				$amountMissing = $itemQuantity - $quantity;
			}
			$itemsMissing[$itemID] = $amountMissing;
		}
	}
	if (!$playerHasAllRequiredItems) {
		$_SESSION['itemsMissing'] = $itemsMissing;
	}	
	return $playerHasAllRequiredItems;
}

function allMissionsInCityReadyForNextLevel($db, $nextLevel, $cityID, $userID) {
	$missionsInCityStmt = $db->prepare("SELECT * FROM missions WHERE city_id = ?");
	$missionsInCityStmt->execute(array($cityID));
	$numMissionsInCity = $missionsInCityStmt->rowCount();
	
	$allMissionsInCityReadyForUser=true;
	
	while ($row = $missionsInCityStmt->fetch(PDO::FETCH_ASSOC)) {
		$missionID = $row['id'];
		
		$userCheckStmt = $db->prepare("SELECT * FROM users_missions WHERE user_id = ? AND mission_id = ? AND curr_rank = ?");
		$userCheckStmt->execute(array($userID, $missionID, $nextLevel));
		
		if ($userCheckStmt->rowCount() < 1) {
			$allMissionsInCityReadyForUser=false;
			break;
		}
	}
	
	return $allMissionsInCityReadyForUser;	
}

//under this model, cityrank doesnt increase until every missions currRank is ready at new number
//currRank should really be rankMissionIsReadyFor
function handleRanks($db, $userID, $missionID) {
	$userMissionsQueryString = "SELECT * FROM users_missions WHERE user_id = ? AND mission_id = ?";
	$userMissionsStmt = $db->prepare($userMissionsQueryString);
	$userMissionsStmt->execute(array($userID, $missionID));
	
	$userMissionsResult = $userMissionsStmt->fetch(PDO::FETCH_ASSOC);
	
	$missionStmt = $db->prepare("SELECT * FROM missions WHERE id = ?");
	$missionStmt->execute(array($missionID));
	
	$missionResult = $missionStmt->fetch(PDO::FETCH_ASSOC);
	if (!$missionResult) {
		redirect($GLOBALS['serverRoot'] . "/errorpage.html");
	}
	
	$num = $userMissionsStmt->rowCount();
	$currRank = -1;
	
	if ($num == 0) {
		$currRank = 1;
		
		$query = $db->prepare("INSERT INTO users_missions (user_id, mission_id, times_complete, rank_one_times, curr_rank) VALUES (?, ?, 1, 1, 1)");
		$params = array($userID, $missionID);
	} else {
		$currRank = $userMissionsResult["curr_rank"];
		$queryString = "UPDATE users_missions SET times_complete = times_complete + 1";

		$cityRankStmt = $db->prepare("SELECT * FROM users_cities WHERE user_id = ? AND city_id = ?");
		$cityRankStmt->execute(array($userID, $missionResult['city_id']));
		
		$cityRankResult = $cityRankStmt->fetch(PDO::FETCH_ASSOC);
		if (!$cityRankResult) redirect($GLOBALS['serverRoot'] . "/errorpage.html");
		$cityRank = $cityRankResult['rank_avail'];
		
		if ($cityRank == 1) {
			$queryString .= ", rank_one_times = rank_one_times + 1";
		}
		if ($cityRank == 2) {
			$queryString .= ", rank_two_times = rank_two_times + 1";
		}
		if ($cityRank == 3) {
			$queryString .= ", rank_three_times = rank_three_times + 1";
		}
		$queryString .= " WHERE user_id = ? AND mission_id = ?";
		$query = $db->prepare($queryString);
		$params = array($userID, $missionID);
	}
	$query->execute($params);	
	
	$newUserMissionsStmt = $db->prepare($userMissionsQueryString);
	$newUserMissionsStmt->execute(array($userID, $missionID));
	
	$newUserMissionsResult = $newUserMissionsStmt->fetch(PDO::FETCH_ASSOC);
	if (!$newUserMissionsResult) {
		redirect($GLOBALS['serverRoot'] . "/errorpage.html");
	}
	
	
	$userTimesFinishedRankForMission = -1;
	$missionRequirementToFinishRank = -1;
	
	if ($currRank == 1) {
		$indexName = "rank_one_times";
		
		$missionRequirementToFinishRank = mysql_result($missionResult, 0,"rank_one_times");
		$userTimesFinishedRankForMission = mysql_result($newUserMissionsResult, 0,"rank_one_times");
	}
	if ($currRank == 2) {
		$indexName = "rank_two_times";
		
		$missionRequirementToFinishRank = mysql_result($missionResult, 0,"rank_two_times");
		$userTimesFinishedRankForMission = mysql_result($newUserMissionsResult, 0,"rank_two_times");
	}
	if ($currRank == 3) {
		$indexName = "rank_three_times";
		
		$missionRequirementToFinishRank = mysql_result($missionResult, 0,"rank_three_times");
		$userTimesFinishedRankForMission = mysql_result($newUserMissionsResult, 0,"rank_three_times");
	} 
	$missionRequirementToFinishRank = $missionResult[$indexName];
	$userTimesFinishedRankForMission = $newUserMissionsResult[$indexName];
	
	if ($userTimesFinishedRankForMission >= $missionRequirementToFinishRank) {
		if ($userTimesFinishedRankForMission == $missionRequirementToFinishRank) {
			if ($currRank <= 3) {
				$_SESSION['justUnlockedThisMissionRank'] = $currRank + 1;
				
				$updateMissionRankStmt = $db->prepare("UPDATE users_missions SET curr_rank = ? WHERE user_id = ? AND mission_id = ?");
				if (!($updateMissionRankStmt->execute(array($currRank + 1, $userID, $missionID)))) {
					redirect($GLOBALS['serverRoot'] . "/errorpage.html");
				}
			} else {
				return;
			}
		}
		
		$cityID = $missionResult['city_id'];
		
		if (allMissionsInCityReadyForNextLevel($db, $currRank+1, $cityID, $userID)) {
			$updateCityRankStmt = $db->prepare("UPDATE users_cities SET rank_avail = ? WHERE user_id = ? AND city_id = ?");
			if (!($updateCityRankStmt->execute(array($currRank + 1, $userID, $cityID)))) {
				redirect($GLOBALS['serverRoot'] . "/errorpage.html");
			}

			$_SESSION['justUnlockedThisCityRank'] = $currRank+1;
			//session work for rewards
		}
	}
}

$missionID=$_POST['missionID'];
session_start();
$userID=$_SESSION['userID'];

$db = ConnectionFactory::getFactory()->getConnection();

$missionStmt = $db->prepare("SELECT * FROM missions WHERE id = ?");
$missionStmt->execute(array($missionID));

$missionResult = $missionStmt->fetch(PDO::FETCH_ASSOC);
if (!$missionResult) redirect("$serverRoot/errorpage.html");

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute(array($userID));

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$userResult) redirect("$serverRoot/errorpage.html");

$itemReqStmt = $db->prepare("SELECT * FROM missions_itemreqs WHERE mission_id = ?");
$itemReqStmt->execute(array($missionID));

$doMission = true;
if (!agencyIsLargeEnough($missionResult, $userResult)) {
	$doMission = false;
} 

if (!playerHasEnoughEnergy($missionResult, $userResult)) {
	$doMission = false;
}

if (!playerHasRequireditems($db, $itemReqStmt, $userID)) {
	$doMission = false;	
}

if ($doMission) {
	$itemsLost = array();
	$hasLostItems = false;
	
	// Loop over every item requirement to see whether or not the user loses it
	while ($row = $itemReqStmt->fetch(PDO::FETCH_ASSOC)) {
		$random = rand(0, 100);
						
		$itemID = $row["item_id"];
		
		$chanceLossStmt = $db->prepare("SELECT chance_of_loss FROM items WHERE id = ?");
		$chanceLossStmt->execute(array($itemID));

		$chanceLossResult = $chanceLossStmt->fetch(PDO::FETCH_ASSOC);
		if (!$chanceLossResult) redirect("$serverRoot/errorpage.html");
		
		$chanceLoss = $chanceLossResult["chance_of_loss"];
				
		if ($random < $chanceLoss * 100) {
			$userItemsStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ? AND item_id = ?");
			$userItemsStmt->execute(array($userID, $itemID));
			
			$userItemsResult = $userItemsStmt->fetch(PDO::FETCH_ASSOC);
			if (!$userItemsResult) redirect("$serverRoot/errorpage.html");

			if ($userItemsResult["quantity"] == 1) {
				$query = $db->prepare("DELETE FROM users_items WHERE user_id = ? AND item_id = ?");
				$params = array($_SESSION['userID'], $itemID);
			} else {
				$query = $db->prepare("UPDATE users_items SET quantity = quantity - 1 WHERE user_id = ? AND item_id = ?");
				$params = array($_SESSION['userID'], $itemID);
			}
			
			if (!($query->execute($params))) {
				redirect("$serverRoot/errorpage.html");
			}
			
			$hasLostItems = true;
			array_push($itemsLost, $itemID);
		}		
	}
		
	if ($hasLostItems) {
		$_SESSION['itemsLost'] = $itemsLost;
	}
	
	// Loot
	$random = rand(0, 100);
			
	$chanceLoot = $missionResult['chance_of_loot'];
	
	if ($random < $chanceLoot * 100) {
		$lootItemID = $missionResult['loot_item_id'];
				
		$userItemsStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ? AND item_id = ?");		
		$userItemsStmt->execute(array($userID, $lootItemID));
				
		$num = $userItemsStmt->rowCount();
				
		if ($num == 0) {
			$query = $db->prepare("INSERT INTO users_items (user_id, item_id, quantity) VALUES (?, ?, 1)");
			$params = array($_SESSION['userID'], $lootItemID);		
		} else {
			$query = $db->prepare("UPDATE users_items SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?");
			$params = array($_SESSION['userID'], $lootItemID);
		}
		if (!($query->execute($params))) {
			redirect("$serverRoot/errorpage.html");
		}
		
		$_SESSION['gainedLootItemID'] = $lootItemID;
	}
			
	$_SESSION['missionsuccess'] = "true";
	
	handleRanks($db, $userID, $missionID);
	
	// Get multiplier based on whether or not the player completed a mission rank
	$multiplier = 1;
	if (isset($_SESSION['justUnlockedThisMissionRank'])) {
		$multiplier = $_SESSION['justUnlockedThisMissionRank'];
	}
	
	// Compute the cash gained
	$minCashGained = $missionResult["min_cash_gained"];
	$maxCashGained = $missionResult["max_cash_gained"];
	$cashGained = rand($minCashGained, $maxCashGained);
	
	$_SESSION['baseCashGained'] = $cashGained;
		
	if (isset($_SESSION['justUnlockedThisMissionRank'])) {
		$_SESSION['extraCashGained'] = $cashGained * ($multiplier - 1);
	}
	
	// Compute experience gained
	$expGained = $missionResult["exp_gained"];

	$_SESSION['baseExpGained'] = $expGained;
	if (isset($_SESSION['justUnlockedThisMissionRank'])) {
		$_SESSION['extraExpGained'] = $expGained * ($multiplier - 1);
	}
		
	// Update energy, missions completed, cash, experience
	$updateStmt = $db->prepare("UPDATE users SET energy = energy - ?, missions_completed = missions_completed + 1, cash = cash + ?, experience = experience + ? WHERE id = ?");
	if (!($updateStmt->execute(array($missionResult['energy_cost'], $cashGained * $multiplier, $expGained * $multiplier, $_SESSION['userID']))))
		redirect("$serverRoot/errorpage.html");
	else $_SESSION['energyLost'] = $missionResult['energy_cost'];
	
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
	}
	
} else {
	$_SESSION['missionfail'] = "true";	
}
$_SESSION['currentMissionCity'] = $_POST['currentMissionCity'];

header("Location: $serverRoot/choosemission.php");
?>