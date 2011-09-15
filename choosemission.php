<html>

<head>
</head>

<body>
<?php 
include_once("topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Mission.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/UserMissionData.php");


function getCityNameFromCityID($cityID) {
	switch ($cityID)
	{
		case 1:
			return "kalm";
		case 2:
			return "zanarkand";
		case 3:
			return "midgar";
		default:;
	}
}

function showFailureNotifications() {
	print "<b>You have not met the requirements for the mission. </b><br>";
	if (isset($_SESSION['needMoreAgency'])) {
		print "You need " . $_SESSION['needMoreAgency'] . " more members in your agency. <br>";
		unset($_SESSION['needMoreAgency']);
	}
	if (isset($_SESSION['needMoreEnergy'])) {
		print "You need " . $_SESSION['needMoreEnergy'] . " more energy. <br>";
		unset($_SESSION['needMoreEnergy']);
	}
	if (isset($_SESSION['itemsMissing'])) {
		$itemsMissingArray = $_SESSION['itemsMissing'];
		$items = Item::getItems(array_keys($itemsMissingArray));
		foreach($items as $item) {
			print "You are missing " . $itemsMissingArray[$item->getID()] . " ";	
			print $item->getName() . "s<br>";
		}
		unset($_SESSION['itemsMissing']);
	}
	print "<br><br>";
	unset($_SESSION['missionfail']);
}

function showSuccessNotifications() {
	print "<b>Congrats, you have successfully completed the mission </b><br>";
	print "You gained " . $_SESSION['baseCashGained'] . " cash <br>";
	print "You gained " . $_SESSION['baseExpGained'] . " exp <br>";
	
	if (isset($_SESSION['gainedLootItemID'])) {
		$item = Item::getItem($_SESSION['gainedLootItemID']);
		print "Lucky you! You received a " . $item->getName() . "<br>";
		unset($_SESSION['gainedLootItemID']);
	}
	
	if (isset($_SESSION['itemsLost'])){
		$itemsLostArray = $_SESSION['itemsLost'];
		
		$itemsLost = Item::getItems($itemsLostArray);
		foreach($itemsLost as $item) {
			print "You lost a " . $item->getName() . "<br>";
			unset($itemsLostArray[$key]);
		}
		unset($_SESSION['itemsLost']);
	}
	
	print "You used " . $_SESSION['energyLost'] . " energy <br>";
	print "<br><br>";
	
	unset($_SESSION['baseCashGained']);
	unset($_SESSION['baseExpGained']);
	unset($_SESSION['energyLost']);
	unset($_SESSION['missionsuccess']);
}

function displayMissions($playerLevel, $cityIDsToRankAvail) {
	if (isset($_SESSION['currentMissionCity'])) {
		$visibleMissions = Mission::getMissionsInCityGivenPlayerLevel($playerLevel+1, $_SESSION['currentMissionCity']);
		
		if (count($visibleMissions) == 0) {
			echo "No missions available in this city";
		} else {
			$cityRank = -1;
			foreach($cityIDsToRankAvail as $cityID=>$rankAvail) {
				if ($cityID==$_SESSION['currentMissionCity']) {
					$cityRank = $cityIDsToRankAvail[$cityID];
				}
			}			
			foreach($visibleMissions as $visibleMission) {
				displayMissionInfo($visibleMission, $playerLevel, $cityRank);
			}			
		}
	} else {
		print "Select a city to do missions in from above";
	}
}

function displayMissionInfo($mission, $playerLevel, $cityRank) {
	if (missionIsLocked($mission, $playerLevel)) {
		print "<b>LOCKED</b> <br>";
	}
	$missionID = $mission->getID();
	?>	
		Title: <?php echo $mission->getName();?><br>
		City: <?php echo ucfirst(getCityNameFromCityID($mission->getCityID()));?><br>
		
		<?php 
		
		$userMissionData = UserMissionData::getUserMissionData($_SESSION['userID'], $mission->getID());
		$completionPercent;
		if ($cityRank == 4) {
			$completionPercent=100;
			$cityRank=3;
		} else {
			$userTimesMissionDoneInThisRank = 0;
			if ($userMissionData) $userMissionData->getRankTimes($cityRank);
			$missionTimesToMasterRank = $mission->getRankReqTimes($cityRank);
			if ($userTimesMissionDoneInThisRank >= $missionTimesToMasterRank) {
				$completionPercent = 100; 
			} else {
				$completionPercent = number_format($userTimesMissionDoneInThisRank/$missionTimesToMasterRank, 2)*100;
			}
		}
	?>
		
	
	<?php echo $completionPercent;?>% R<?php echo $cityRank; ?><br>
	Description: <?php echo $mission->getDescription();?><br>
	Minimum level: <?php echo $mission->getMinLevel();?><br>
	Cost: <?php echo $mission->getEnergyCost();?> energy<br>
	Will Gain: <?php echo $mission->getExpGained(); ?> exp<br>
	Will Gain <?php echo $mission->getMinCashGained();?> - 
	<?php echo $mission->getMaxCashGained();?> cash<br>
	Chance of getting loot: <?php echo $mission->getChanceOfLoot();?><br>
	
	<?php 		
	
		$lootItemID = $mission->getLootItemID();
		if ($lootItemID) {
			$lootItem = Item::getItem($lootItemID);
			?>
			You're not supposed to know this but the item you might get is the <?php echo $lootItem->getName();?><br>
			didnt put in agency or item requirements too lazy but they work<br>
	<?php 	
		}
	print "Item Requirements:<br>";
	$itemIDsToQuantity = Mission::getMissionRequiredItemsIDsToQuantity($missionID);
	foreach ($itemIDsToQuantity as $key => $value) {
		$item = Item::getItem($key);
		print $value . "x " . $item->getName() . "<br>";
	}
	
		if (!missionIsLocked($mission, $playerLevel)) {
			?>
			<form action='backend/domission.php' method='post'>
			<input type='hidden' name='missionID' value='<?php echo $mission->getID()?>' />
			<input type='hidden' name='currentMissionCity' value='<?php echo $_SESSION['currentMissionCity'];?>' />
			<input type='submit' value='Do It' />
			</form>
			<?php 
		}
		print "<br><br>";
}


function missionIsLocked($mission, $playerLevel) {
	if ($mission->getMinLevel() == ($playerLevel+1)) {
		return true;
	}
	return false;
}

function listCities($cityIDsToRankAvail) {
	foreach ($cityIDsToRankAvail as $cityID=>$rankAvail) {
		$cityName = getCityNameFromCityID($cityID);
?>
		<form action='choosemission.php' method='post'>
		<input type='hidden' name='postedCityID' value='<?php echo $cityID;?>' />
		<input type='submit' value='Missions in <?php echo ucfirst($cityName);?>' />
		</form>
<?php
	}

}

function showJustUnlockedMissionRank() {
	$justUnlockedMissionRank = $_SESSION['justUnlockedThisMissionRank'];
?>
	Not just that, you just...<br>
	mastered rank <?php echo ($justUnlockedMissionRank-1);?> for that mission<br>
	just gained <?php echo $_SESSION['extraCashGained'];?> extra cash bonus<br>
	just gained <?php echo $_SESSION['extraExpGained'];?> extra exp<br>
	<br><br>
<?php	
	unset($_SESSION['extraCashGained']);
	unset($_SESSION['extraExpGained']);
	unset($_SESSION['justUnlockedThisMissionRank']);
}

function showJustUnlockedCityRank() {
	$justUnlockedCityRank = $_SESSION['justUnlockedThisCityRank'];	
?>
	Not just that, you just...<br>
	mastered rank <?php echo ($justUnlockedCityRank-1);?> for city<br>
	<br><br>
<?php 
	unset($_SESSION['justUnlockedThisCityRank']);
}


session_start();

// Level up
if (isset($_SESSION['levelUp'])) {
	$newLevel = $_SESSION['newLevel'];
	$skillPointsGained = $_SESSION['skillPointsGained'];
	include_once($_SERVER['DOCUMENT_ROOT'] . "/levelupnotice.php");
	unset($_SESSION['newLevel']);
	unset($_SESSION['skillPointsGained']);
	unset($_SESSION['levelUp']);
}

if (isset($_POST['postedCityID'])) {
	$_SESSION['currentMissionCity'] = $_POST['postedCityID'];
}

if (isset($_SESSION['missionfail']) && $_SESSION['missionfail'] == true) {
	showFailureNotifications();
}

if (isset($_SESSION['missionsuccess']) && $_SESSION['missionsuccess'] == true) {
	showSuccessNotifications();
} 

if (isset($_SESSION['justUnlockedThisMissionRank'])) {
	showJustUnlockedMissionRank();
}

if (isset($_SESSION['justUnlockedThisCityRank'])) {
	showJustUnlockedCityRank();
}

$cityIDsToRankAvail = User::getAvailableCityIDsToRankAvail($_SESSION['userID']);
listCities($cityIDsToRankAvail);
displayMissions($playerLevel, $cityIDsToRankAvail);

?>
</body>

</html>