<html>

<head>
</head>

<body>
<?php 
include_once("topmenu.php");
include_once("properties/citynames.php");
include_once('properties/dbproperties.php');


function showFailureNotifications($db) {
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
		foreach($itemsMissingArray as $key=>$value) {
			print "You are missing " . $value . " ";
			
			$itemStmt = $db->prepare("SELECT * FROM items WHERE id = ?");
			$itemStmt->execute(array($key));
			$row = $itemStmt->fetch(PDO::FETCH_ASSOC);
			print $row['name'] . "s<br>";	
			
			unset($itemsMissingArray[$key]);
		}
		
		unset($_SESSION['itemsMissing']);
	}
	print "<br><br>";
	unset($_SESSION['missionfail']);
}

function showSuccessNotifications($db) {
	print "<b>Congrats, you have successfully completed the mission </b><br>";
	print "You gained " . $_SESSION['baseCashGained'] . " cash <br>";
	print "You gained " . $_SESSION['baseExpGained'] . " exp <br>";
	
	if (isset($_SESSION['gainedLootItemID'])) {
		$itemStmt = $db->prepare("SELECT * FROM items WHERE id = ?");
		$itemStmt->execute(array($_SESSION['gainedLootItemID']));
		$row = $itemStmt->fetch(PDO::FETCH_ASSOC);
		print "Lucky you! You received a " . $row['name'] . "<br>";
		unset($_SESSION['gainedLootItemID']);
	}
	
	if (isset($_SESSION['itemsLost'])){
		$itemsLostArray = $_SESSION['itemsLost'];
		foreach($itemsLostArray as $key=>$value) {
			print "You lost a ";
			$itemStmt = $db->prepare("SELECT * FROM items WHERE id = ?");
			$itemStmt->execute(array($value));
			$row = $itemStmt->fetch(PDO::FETCH_ASSOC);
			print $row['name'] . "<br>";
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

function displayMissions($db, $playerLevel) {
	if (isset($_SESSION['currentMissionCity'])) {
		
		$missionStmt = $db->prepare("SELECT * FROM missions WHERE min_level <= ? AND city_id = ? ORDER BY min_level");
		$missionStmt->execute(array($playerLevel+1, $_SESSION['currentMissionCity']));
		$numMissions = $missionStmt->rowCount();
		
		if ($numMissions == 0) {
			echo "No missions available in this city";
		} else {
			$userCitiesStmt = $db->prepare("SELECT * from users_cities WHERE user_id= ? AND city_id=?");
			$userCitiesStmt->execute(array($_SESSION['userID'], $_SESSION['currentMissionCity']));			
			$userCitiesRow = $userCitiesStmt->fetch(PDO::FETCH_ASSOC);
			$cityRank = $userCitiesRow['rank_avail'];
			while ($row = $missionStmt->fetch(PDO::FETCH_ASSOC)) {
				displayMissionInfo($db, $row, $playerLevel, $cityRank);
			}
		}
	} else {
		print "Select a city to do missions in from above";
	}
}

function displayMissionInfo($db, $missionInfoRow, $playerLevel, $cityRank) {
	if ($row['min_level'] == ($playerLevel+1)) {
		print "<b>LOCKED</b> <br>";
	}
	?>

	
	Title: <?php echo $missionInfoRow['name'];?><br>
	City: <?php echo ucfirst(getCityNameFromCityID($missionInfoRow['city_id']));?><br>
	
	<?php 
	
	$userMissionsStmt = $db->prepare("SELECT * from users_missions WHERE user_id=? AND mission_id=?");
	$userMissionsStmt->execute(array($_SESSION['userID'], $missionInfoRow['id']));
	$userMissionsRow = $userMissionsStmt->fetch(PDO::FETCH_ASSOC);
	
	$completionPercent;
	if ($cityRank == 4) {
		$completionPercent=100;
		$cityRank=3;
	} else {
		$userTimesMissionDoneInThisRank;
		$missionTimesToMasterRank;
		if ($cityRank==1){
 			$missionTimesToMasterRank=$missionInfoRow['rank_one_times'];
			$userTimesMissionDoneInThisRank=$userMissionsRow['rank_one_times'];
		}
		if ($cityRank==2){
 			$missionTimesToMasterRank=$missionInfoRow['rank_two_times'];
			$userTimesMissionDoneInThisRank=$userMissionsRow['rank_two_times'];
		}
		if ($cityRank==3){
			$missionTimesToMasterRank=$missionInfoRow['rank_three_times'];
			$userTimesMissionDoneInThisRank=$userMissionsRow['rank_three_times'];
		}
		
		if ($userTimesMissionDoneInThisRank >= $missionTimesToMasterRank) {
			$completionPercent = 100; 
		} else {
			$completionPercent = number_format($userTimesMissionDoneInThisRank/$missionTimesToMasterRank, 2)*100;
		}
	}
?>
	
<?php echo $completionPercent;?>% R<?php echo $cityRank; ?><br>
Description: <?php echo $missionInfoRow['description'];?><br>
Minimum level: <?php echo $missionInfoRow['min_level'];?><br>
Cost: <?php echo $missionInfoRow['energy_cost'];?> energy<br>
Will Gain: <?php echo $missionInfoRow['exp_gained']; ?> exp<br>
Will Gain <?php echo $missionInfoRow['min_cash_gained'];?> - 
<?php echo $missionInfoRow['max_cash_gained'];?><br>
Chance of getting loot: <?php echo $missionInfoRow['chance_of_loot'];?><br>

<?php 		
	$itemID=$missionInfoRow['loot_item_id'];
	$itemStmt = $db->prepare("SELECT * from items WHERE id = ?");
	$itemStmt->execute(array($itemID));
	$itemRow = $itemStmt->fetch(PDO::FETCH_ASSOC);
	?>	

	You're not supposed to know this but the item you might get is the <?php echo $itemRow['name'];?><br>
	didnt put in agency or item requirements too lazy but they work
	
<?php 	
	if (!missionIsLocked($missionInfoRow, $playerLevel)) {
		?>
		<form action='backend/domission.php' method='post'>
		<input type='hidden' name='missionID' value='<?php echo $missionInfoRow['id']?>' />
		<input type='hidden' name='currentMissionCity' value='<?php echo $_SESSION['currentMissionCity'];?>' />
		<input type='submit' value='Do It' />
		</form>
		<?php 
	}
	
	print "<br><br>";
	
	
}

function missionIsLocked($missionInfoRow, $playerLevel) {
	if ($missionInfoRow['min_level'] == ($playerLevel+1)) {
		return true;
	}
	return false;
}

function listCities($db) {
	
	$availCityStmt = $db->prepare("SELECT * FROM users_cities WHERE rank_avail > 0 AND user_id = ?");
	$availCityStmt->execute(array($_SESSION['userID']));
	$numAvailCities = $availCityStmt->rowCount();
	
	while ($row = $availCityStmt->fetch(PDO::FETCH_ASSOC)) {
		$cityID = $row['city_id'];
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


if (isset($_POST['postedCityID'])) {
	$_SESSION['currentMissionCity'] = $_POST['postedCityID'];
}

if (isset($_SESSION['missionfail']) && $_SESSION['missionfail'] == true) {
	showFailureNotifications($db);
}

if (isset($_SESSION['missionsuccess']) && $_SESSION['missionsuccess'] == true) {
	showSuccessNotifications($db);
} 

if (isset($_SESSION['justUnlockedThisMissionRank'])) {
	showJustUnlockedMissionRank();
}

if (isset($_SESSION['justUnlockedThisCityRank'])) {
	showJustUnlockedCityRank();
}


listCities($db);
displayMissions($db, $playerLevel);

?>
</body>

</html>