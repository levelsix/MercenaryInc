<html>

<head>
</head>

<body>
<?php 
include("topmenu.php");
include("properties/citynames.php");

mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

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
		foreach($itemsMissingArray as $key=>$value) {
			print "You are missing " . $value . " ";
			$query="SELECT * FROM items WHERE id = ". $key . ";";
			$itemresult=mysql_query($query);
			print mysql_result($itemresult, 0, "name") . "s<br>";
			unset($itemsMissingArray[$key]);
		}
		unset($_SESSION['itemsMissing']);
	}
	print "<br><br>";
	unset($_SESSION['missionfail']);
}

function showSuccessNotifications() {
	print "<b>Congrats, you have successfully completed the mission </b><br>";
	print "You gained " . $_SESSION['cashGained'] . " cash <br>";
	print "You gained " . $_SESSION['expGained'] . " exp <br>";
	
	if (isset($_SESSION['gainedLootItemID'])) {
		$query="SELECT * FROM items WHERE id = ". $_SESSION['gainedLootItemID'] . ";";
		$itemresult=mysql_query($query);
		print "Lucky you! You received a " . mysql_result($itemresult, 0, "name") . "<br>";
		unset($_SESSION['gainedLootItemID']);
	}
	
	if (isset($_SESSION['itemsLost'])){
		$itemsLostArray = $_SESSION['itemsLost'];
		foreach($itemsLostArray as $key=>$value) {
			print "You lost a ";
			$query="SELECT * FROM items WHERE id = ". $value . ";";
			$itemresult=mysql_query($query);
			print mysql_result($itemresult, 0, "name") . "<br>";
			unset($itemsLostArray[$key]);
		}
		unset($_SESSION['itemsLost']);
	}
	
	
	print "You used " . $_SESSION['energyLost'] . " energy <br>";
	print "<br><br>";
	
	unset($_SESSION['cashGained']);
	unset($_SESSION['expGained']);
	unset($_SESSION['energyLost']);
	unset($_SESSION['missionsuccess']);
}

function displayMissions($playerLevel) {
	if (isset($_SESSION['currentMissionCity'])) {
		$query="SELECT * FROM missions WHERE min_level <= ". ($playerLevel+1);
		$query.=" AND city_id=".$_SESSION['currentMissionCity'];
		$query.=" ORDER BY min_level;";
		$result=mysql_query($query);
		$num=mysql_numrows($result);

		if ($num == 0) {
			echo "No missions available in this city";
		} else {
			for ($i = 0; $i < $num; $i++) {
				displayMissionInfo($result, $i, $playerLevel);
			}
		}
		mysql_close();
	} else {
		print "Select a city to do missions in from above";
	}
}

function displayMissionInfo($missionInfoResult, $i, $playerLevel) {
	if (mysql_result($missionInfoResult,$i,"min_level") == ($playerLevel+1)) {
		print "<b>LOCKED</b> <br>";
	}

	$query="SELECT * from users_missions WHERE user_id=".$_SESSION['userID'];
	$query.=" AND mission_id=".mysql_result($missionInfoResult,$i,"id").";";
	$userMissionsResult=mysql_query($query);
	
	if (mysql_numrows($userMissionsResult) > 0 && mysql_result($userMissionsResult,0,"times_complete")>0) {
		print "You have done this mission ".mysql_result($userMissionsResult,0,"times_complete") ." times<br>";
	} else {
		print "You have never done this mission <br>";
	}
	
	print "Title: " . mysql_result($missionInfoResult,$i,"name") . "<br>";
	print "City: " . ucfirst(getCityNameFromCityID(mysql_result($missionInfoResult,$i,"city_id"))) . "<br>";
	print "Description: " . mysql_result($missionInfoResult,$i,"description") . "<br>";
	print "Minimum level: " . mysql_result($missionInfoResult,$i,"min_level") . "<br>";
	print "Cost: " . mysql_result($missionInfoResult,$i,"energy_cost") . " energy<br>";
	print "Will Gain: " . mysql_result($missionInfoResult,$i,"exp_gained") . " exp<br>";
	print "Will Gain " . mysql_result($missionInfoResult,$i,"min_cash_gained") . " - ";
	print mysql_result($missionInfoResult, $i, "max_cash_gained") . "<br>";
	print "Chance of getting loot: " . mysql_result($missionInfoResult,$i,"chance_of_loot") . "<br>";
		
	$item_id = mysql_result($missionInfoResult,$i,"loot_item_id");
	$query="SELECT * FROM items WHERE id = ". $item_id . ";";
	$itemresult=mysql_query($query);
	print "You're not supposed to know this but the item you might get is the ";
	print mysql_result($itemresult, 0, "name") . "<br>";
		
	print "didnt put in agency or item requirements too lazy but they work";
	
	if (!missionIsLocked($missionInfoResult, $i, $playerLevel)) {
		print "<form action='backend/domission.php' method='post'>";
		print "<input type='hidden' name='missionID' value='".mysql_result($missionInfoResult,$i,"id")."' />";
		print "<input type='hidden' name='currentMissionCity' value='".$_SESSION['currentMissionCity']."' />";
		print "<input type='submit' value='Do It' />";
		print "</form>";
	}
	
	print "<br><br>";
}

function missionIsLocked($missionInfoResult, $missionNum, $playerLevel) {
	if (mysql_result($result,$missionInfoResult,"min_level") == ($playerLevel+1)) {
		return true;
	}
	return false;
}

function listCities($userID) {
	$query="SELECT * from users_cities WHERE rank_avail > 0 AND user_id=".$_SESSION['userID'].";";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for ($i=0; $i<$num; $i++) {
		$cityID=mysql_result($result,$i,"city_id");
		$cityName = getCityNameFromCityID($cityID);
		print "<form action='choosemission.php' method='post'>";
		print "<input type='hidden' name='postedCityID' value='".$cityID."' />";
		print "<input type='submit' value='Missions in ".ucfirst($cityName)."' />";
		print "</form>";
	}
}

function showJustUnlockedMissionRank() {
	$justUnlockedMissionRank = $_SESSION['justUnlockedThisMissionRank'];
	echo "just unlocked the rank " . $justUnlockedMissionRank . " for mission"; 
	unset($_SESSION['justUnlockedThisMissionRank']);
}

function showJustUnlockedCityRank() {
	$justUnlockedCityRank = $_SESSION['justUnlockedThisCityRank'];
	echo "just unlocked the rank " . $justUnlockedCityRank . " for city";
	unset($_SESSION['justUnlockedThisCityRank']);
}


session_start();


if (isset($_POST['postedCityID'])) {
	$_SESSION['currentMissionCity'] = $_POST['postedCityID'];
}

if (isset($_SESSION['justUnlockedThisMissionRank'])) {
	showJustUnlockedMissionRank();
}

if (isset($_SESSION['justUnlockedThisCityRank'])) {
	showJustUnlockedCityRank();
}

if (isset($_SESSION['missionfail']) && $_SESSION['missionfail'] == true) {
	showFailureNotifications();
}

if (isset($_SESSION['missionsuccess']) && $_SESSION['missionsuccess'] == true) {
	showSuccessNotifications();
} 

listCities();
displayMissions($playerLevel);

?>
</body>

</html>