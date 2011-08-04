<html>

<head>
</head>

<body>
<?php 
include("topmenu.php");
include("properties/citynames.php");


mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

function missionIsLocked($result, $missionNum, $playerLevel) {
	if (mysql_result($result,$missionNum,"min_level") == ($playerLevel+1)) {
		return true;
	}
	return false;
}

session_start();

if (isset($_POST['postedCityID'])) {
	$_SESSION['currentMissionCity'] = $_POST['postedCityID'];
}

if (isset($_SESSION['missionfail']) && $_SESSION['missionfail'] == true) {
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

if (isset($_SESSION['missionsuccess']) && $_SESSION['missionsuccess'] == true) {
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


$query="SELECT * from users_cities WHERE rank_avail > 0;";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while ($i < $num) {
	$cityID=mysql_result($result,$i,"city_id");
	$cityName = getCityNameFromCityID($cityID);
	print "<form action='choosemission.php' method='post'>";
	print "<input type='hidden' name='postedCityID' value='".$cityID."' />";
	print "<input type='submit' value='Missions in ".ucfirst($cityName)."' />";
	print "</form>";
	$i++;
}

if (isset($_SESSION['currentMissionCity'])) {
	$query="SELECT * FROM missions WHERE min_level <= ". ($playerLevel+1);
	$query.=" AND city_id=".$_SESSION['currentMissionCity'];
	$query.=" ORDER BY min_level;";
	$result=mysql_query($query);
	$num=mysql_numrows($result);

	
	
	if ($num == 0) { 
		echo "No missions available in this city";
	} else {
		
		$i = 0;
		while ($i < $num) {
			if (mysql_result($result,$i,"min_level") == ($playerLevel+1)) {
				print "<b>LOCKED</b> <br>";
			}
			
			print "Title: " . mysql_result($result,$i,"name") . "<br>";
			print "City: " . ucfirst(getCityNameFromCityID(mysql_result($result,$i,"city_id"))) . "<br>";
			print "Description: " . mysql_result($result,$i,"description") . "<br>";
			print "Minimum level: " . mysql_result($result,$i,"min_level") . "<br>";
			print "Cost: " . mysql_result($result,$i,"energy_cost") . " energy<br>";
			print "Will Gain: " . mysql_result($result,$i,"exp_gained") . " exp<br>";
			print "Will Gain " . mysql_result($result,$i,"min_cash_gained") . " - ";
			print mysql_result($result, $i, "max_cash_gained") . "<br>";
			print "Chance of getting loot: " . mysql_result($result,$i,"chance_of_loot") . "<br>";
			
			$item_id = mysql_result($result,$i,"loot_item_id");
			$query="SELECT * FROM items WHERE id = ". $item_id . ";";
			$itemresult=mysql_query($query);
			print "You're not supposed to know this but the item you might get is the ";
			print mysql_result($itemresult, 0, "name") . "<br>";
			
			print "didnt put in agency or item requirements too lazy but they work";
								
			if (!missionIsLocked($result, $i, $playerLevel)) {
				print "<form action='backend/domission.php' method='post'>";
				print "<input type='hidden' name='missionID' value='".mysql_result($result,$i,"id")."' />";
				print "<input type='hidden' name='currentMissionCity' value='".$_SESSION['currentMissionCity']."' />";
				print "<input type='submit' value='Do It' />";
				print "</form>";
			}
						
			print "<br><br>";
			
			$i++;
		}
		
	}	
	mysql_close(); 
} else {
	print "Select a city to do missions in from above";
}
?>
</body>

</html>