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
	for ($i = 0; $i < $numReqs; $i++) {
		$random = rand(0, 100);
		$itemID=mysql_result($itemReqResult, $i,"item_id");
		$chanceLossQuery="SELECT * FROM items WHERE id=" . $itemID;
		$chanceLossResult=mysql_query($chanceLossQuery);
		$chanceLoss=mysql_result($chanceLootResult, 0,"chance_of_loss");
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
			//TODO: mark session that item lost
		}		
	}
	
	$query = "UPDATE users SET energy=energy-".	mysql_result($missionResult, 0,"energy_cost") 
		." WHERE id=" . $_SESSION['userID'];
	//TODO: mark session energy lost
	mysql_query($query) or die(mysql_error());

	$minCashGained=mysql_result($missionResult, 0,"min_cash_gained");
	$maxCashGained=mysql_result($missionResult, 0,"max_cash_gained");
	$cashGained=rand($minCashGained, $maxCashGained);
	$query = "UPDATE users SET cash=cash+".	$cashGained . " WHERE id=" . $_SESSION['userID'];
	//TODO: mark session money gained
	mysql_query($query) or die(mysql_error());
	
	$query = "UPDATE users SET experience=experience+".	mysql_result($missionResult, 0,"exp_gained")
	." WHERE id=" . $_SESSION['userID'];
	//TODO: mark session exp gained
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
		
		//TODO: mark loot gained
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "UPDATE users SET missions_completed=missions_completed+1 WHERE id=" . $_SESSION['userID'];
	//TODO: mark session energy lost
	
	mysql_query($query) or die(mysql_error());
} else {
	$_SESSION['fail']="true";	
}
$_SESSION['currentMissionCity']=$_POST['currentMissionCity'];

mysql_close();

header("Location: ../choosemission.php");
?>