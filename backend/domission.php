<?php
include("../properties/dbproperties.php");

function agencyIsLargeEnough($missionResult, $userResult) {
	$minAgencySize=mysql_result($missionResult, 0,"min_agency_size");
	$playerAgencySize=mysql_result($userResult, 0,"agency_size");
	
	$playerHasEnoughAgency = ($playerAgencySize >= $minAgencySize);
	if (!$playerHasEnoughAgency) {
		$_SESSION['needMoreAgency']=$minAgencySize-$playerAgencySize;
	}
	return $playerHasEnoughAgency;
}

function playerHasEnoughEnergy($missionResult, $userResult) {
	$minEnergy=mysql_result($missionResult, 0,"energy_cost");
	$playerEnergy=mysql_result($userResult, 0,"energy");
	$playerHasEnoughEnergy = $playerEnergy >= $minEnergy;
	if (!$playerHasEnoughEnergy) {
		$_SESSION['needMoreEnergy']=$minEnergy-$playerEnergy;
	}
	
	return $playerHasEnoughEnergy;
}

function playerHasRequireditems($itemReqResult, $userID) {
	$numReqs=mysql_numrows($itemReqResult);
	$itemsMissing = array();
	$playerHasAllRequiredItems = true;
	
	for ($i = 0; $i < $numReqs; $i++) {
		$itemID=mysql_result($itemReqResult, $i,"item_id");
		$itemQuantity=mysql_result($itemReqResult, $i,"item_quantity");
		
		$userItemsQuery="SELECT * FROM users_items WHERE user_id=" . $userID;
		$userItemsQuery.=" AND item_id=".$itemID;
		$userItemsResult=mysql_query($userItemsQuery);		
		
		if (mysql_numrows($userItemsResult) <= 0 || mysql_result($userItemsResult, 0,"quantity") < $itemQuantity) {
			$playerHasAllRequiredItems = false;
			if (mysql_numrows($userItemsResult) <= 0) {
				$amountMissing = $itemQuantity;
			} else {
				$amountMissing = $itemQuantity - mysql_result($userItemsResult, 0,"quantity");
			}
			$itemsMissing[$itemID] = $amountMissing;
		}
	}
	if (!$playerHasAllRequiredItems) {
		$_SESSION['itemsMissing'] = $itemsMissing;
	}	
	return $playerHasAllRequiredItems;
}

function allMissionsInCityReadyForNextLevel($nextLevel, $cityID, $userID) {
	$missionsInCityQuery="SELECT * from missions WHERE city_id=".$cityID;
	$missionsInCityResult=mysql_query($missionsInCityQuery);
	$numMissionsInCity=mysql_numrows($missionsInCityResult);
	
	$allMissionsInCityReadyForUser=true;
	
	for ($i = 0; $i < $numMissionsInCity; $i++) {
		$missionID = mysql_result($missionsInCityResult, $i,"id");
		$userCheckQuery = "SELECT * from users_missions WHERE user_id=".$userID;
		$userCheckQuery.= " AND mission_id=".$missionID." AND curr_rank=".$nextLevel.";";
		$userCheckResult=mysql_query($userCheckQuery);
		
		if (mysql_numrows($userCheckResult) < 1) {
			$allMissionsInCityReadyForUser=false;
			break;
		}
		
	}
	return $allMissionsInCityReadyForUser;	
}

//under this model, cityrank doesnt increase until every missions currRank is ready at new number
function handleRanks($userID, $missionID) {
	$userMissionsQuery="SELECT * FROM users_missions WHERE user_id=" . $userID;
	$userMissionsQuery.=" AND mission_id=".$missionID;
	$userMissionsResult=mysql_query($userMissionsQuery);
		
	$num=mysql_numrows($userMissionsResult);
	$currRank = -1;
	if ($num == 0) {
		$currRank = 1;
		$query = "INSERT INTO users_missions (user_id, mission_id, times_complete, rank_one_times, curr_rank) VALUES
							(".$userID.", ". $missionID .", 1, 1, 1);"; 
	} else {
		$currRank = mysql_result($userMissionsResult, 0,"curr_rank");
		$query = "UPDATE users_missions SET times_complete=times_complete+1";
		
		$cityRankQuery = "SELECT * from users_cities WHERE user_id=".$userID." AND mission_id=".$missionID.";";
		$cityRankResult = mysql_query($cityRankQuery);
		$cityRank = mysql_result($cityRankResult, 0,"rank_avail");
		if ($cityRank==1) {
			$query.=", rank_one_times=rank_one_times+1";
		}
		if ($cityRank==2) {
			$query.=", rank_two_times=rank_two_times+1";
		}
		if ($cityRank==3) {
			$query.=", rank_three_times=rank_three_times+1";
		}
		$query.="  WHERE user_id=" . $userID ." AND mission_id = ".$missionID.";";
	}
	mysql_query($query) or die(mysql_error());
	
	$missionQuery="SELECT * from missions WHERE id=".$missionID.";";
	$missionResult=mysql_query($missionQuery);	
	
	$newUserMissionsResult=mysql_query($userMissionsQuery);	
	
	
	$userTimesFinishedRankForMission=-1;
	$missionRequirementToFinishRank=-1;
	
	if ($currRank==1) {
		$missionRequirementToFinishRank=mysql_result($missionResult, 0,"rank_one_times");
		$userTimesFinishedRankForMission=mysql_result($newUserMissionsResult, 0,"rank_one_times");
	}
	if ($currRank==2) {
		$missionRequirementToFinishRank=mysql_result($missionResult, 0,"rank_two_times");
		$userTimesFinishedRankForMission=mysql_result($newUserMissionsResult, 0,"rank_two_times");
	}
	if ($currRank==3) {
		$missionRequirementToFinishRank=mysql_result($missionResult, 0,"rank_three_times");
		$userTimesFinishedRankForMission=mysql_result($newUserMissionsResult, 0,"rank_three_times");
	}
	
	if ($userTimesFinishedRankForMission >= $missionRequirementToFinishRank) {
		if ($userTimesFinishedRankForMission == $missionRequirementToFinishRank) {
			$_SESSION['justUnlockedThisMissionRank'] = $currRank+1;
			$upMissionRankQuery = "UPDATE users_missions SET curr_rank=".($currRank+1);
			$upMissionRankQuery.="  WHERE user_id=" . $userID ." AND mission_id = ".$missionID.";";
			mysql_query($upMissionRankQuery) or die(mysql_error());
				
			//other session work
		}
		
		$cityID=mysql_result($missionResult, 0,"city_id");

		
		
		if (allMissionsInCityReadyForNextLevel($currRank+1, $cityID, $userID)) {
			$upCityRankQuery = "UPDATE users_cities SET rank_avail=".($currRank+1);
			$upCityRankQuery.= " WHERE user_id=" . $userID . " AND city_id=" . $cityID . ";";
			
			mysql_query($upCityRankQuery) or die(mysql_error());
			$_SESSION['justUnlockedThisCityRank'] = $currRank+1;
		}
	}
}


$missionID=$_POST['missionID'];
session_start();
$userID=$_SESSION['userID'];

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$missionQuery="SELECT * FROM missions WHERE id=" . $missionID;
$missionResult=mysql_query($missionQuery);

$userQuery="SELECT * FROM users WHERE id=" . $userID;
$userResult=mysql_query($userQuery);

$itemReqQuery="SELECT * FROM missions_itemreqs WHERE mission_id=" . $missionID;
$itemReqResult=mysql_query($itemReqQuery);

$doMission=true;
if (!agencyIsLargeEnough($missionResult, $userResult)) {
	$doMission=false;
} 
if (!playerHasEnoughEnergy($missionResult, $userResult)) {
	$doMission=false;
}
if (!playerHasRequireditems($itemReqResult, $userID)) {
	$doMission=false;	
}
if ($doMission) {
	$numReqs=mysql_numrows($itemReqResult);
	$itemsLost=array();
	$hasLostItems=false;
	for ($i = 0; $i < $numReqs; $i++) {
		$random = rand(0, 100);
		$itemID=mysql_result($itemReqResult, $i,"item_id");
		$chanceLossQuery="SELECT * FROM items WHERE id=" . $itemID;
		$chanceLossResult=mysql_query($chanceLossQuery);
		$chanceLoss=mysql_result($chanceLossResult, 0,"chance_of_loss");
		if ($random < $chanceLoss*100) {
			$userItemsQuery="SELECT * FROM users_items WHERE user_id=" . $userID;
			$userItemsQuery.=" AND item_id=".$itemID;
			$userItemsResult=mysql_query($userItemsQuery);
			if (mysql_result($userItemsResult, 0,"quantity") == 1) {
				$query = "DELETE FROM users_items WHERE user_id=" . $_SESSION['userID'];
				$query.=" AND item_id = ".$itemID.";";
			} else {
				$query = "UPDATE users_items SET quantity=quantity-1 WHERE user_id=" . $_SESSION['userID'];
				$query.=" AND item_id = ".$itemID.";";
			}
			mysql_query($query) or die(mysql_error());
			$hasLostItems=true;
			array_push($itemsLost, $itemID);
			//TODO: mark session that item lost. array should just have itemIDs of lost items
		}		
	}
	if ($hasLostItems) {
		$_SESSION['itemsLost']=$itemsLost;
	}
	
	
	$query = "UPDATE users SET energy=energy-".	mysql_result($missionResult, 0,"energy_cost") 
		." WHERE id=" . $_SESSION['userID'];
	$_SESSION['energyLost']=mysql_result($missionResult, 0,"energy_cost");
	mysql_query($query) or die(mysql_error());

	$minCashGained=mysql_result($missionResult, 0,"min_cash_gained");
	$maxCashGained=mysql_result($missionResult, 0,"max_cash_gained");
	$cashGained=rand($minCashGained, $maxCashGained);
	$query = "UPDATE users SET cash=cash+".	$cashGained . " WHERE id=" . $_SESSION['userID'];
	$_SESSION['cashGained']=$cashGained;
	mysql_query($query) or die(mysql_error());
	
	$query = "UPDATE users SET experience=experience+".	mysql_result($missionResult, 0,"exp_gained")
	." WHERE id=" . $_SESSION['userID'];
	$_SESSION['expGained']=mysql_result($missionResult, 0,"exp_gained");
	mysql_query($query) or die(mysql_error());
	
	$random = rand(0, 100);
	$chanceLoot=mysql_result($missionResult, 0,"chance_of_loot");
	if ($random < $chanceLoot*100) {
		$lootItemID=mysql_result($missionResult, 0,"loot_item_id");
		$userItemsQuery="SELECT * FROM users_items WHERE user_id=" . $userID;
		$userItemsQuery.=" AND item_id=".$lootItemID;
		$userItemsResult=mysql_query($userItemsQuery);
		$num=mysql_numrows($userItemsResult);
		
		if ($num == 0) {
			$query = "INSERT INTO users_items (user_id, item_id, quantity) VALUES
					(".$_SESSION['userID'].", ". $lootItemID .", 1);"; 
		
		} else {
			$query = "UPDATE users_items SET quantity=quantity+1 WHERE user_id=" . $_SESSION['userID'];
			$query.=" AND item_id = ".$lootItemID.";";
		}
		
		$_SESSION['gainedLootItemID']=$lootItemID;
		
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "UPDATE users SET missions_completed=missions_completed+1 WHERE id=" . $_SESSION['userID'];
	mysql_query($query) or die(mysql_error());
		
	$_SESSION['missionsuccess']="true";
	handleRanks($userID, $missionID);
} else {
	$_SESSION['missionfail']="true";	
}
$_SESSION['currentMissionCity']=$_POST['currentMissionCity'];

mysql_close();

header("Location: ../choosemission.php");
?>