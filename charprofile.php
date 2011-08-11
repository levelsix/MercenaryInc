<?php 
include("topmenu.php");
// Skills button
?>
<form action='skills.php'>
<input type='submit' value='Skills'/>
</form>

<?php
mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

session_start();
$userID = $_SESSION['userID'];
$userQuery = "SELECT * FROM users WHERE id = " . $userID . ";";
$userResult = mysql_query($userQuery);
$numRows = mysql_numrows($userResult);

// Error: redirect
if ($numRows != 1) {
	header("Location: charhome.php");
	exit;
}

// User information
$userName = mysql_result($userResult, 0, "name");
$userLevel = mysql_result($userResult, 0, "level");
$userType = mysql_result($userResult, 0, "type");
$userMissionsCompleted = mysql_result($userResult, 0, "missions_completed");
$userFightsWon = mysql_result($userResult, 0, "fights_won");
$userFightsLost = mysql_result($userResult, 0, "fights_lost");
$userKills = mysql_result($userResult, 0, "kills");
$userDeaths = mysql_result($userResult, 0, "deaths");
?>

<?php echo $userName;?><br>
Level <?php echo $userLevel;?> <?php echo ucfirst($userType);?><br>
----------------------------------------------------- <br>
Missions Completed: <?php echo $userMissionsCompleted;?><br>
Fights Won: <?php echo $userFightsWon;?><br>
Fights Lost: <?php echo $userFightsLost;?><br>
Kills: <?php echo $userKills;?><br>
Deaths: <?php echo $userDeaths;?><br>


<!--  Cash flow-->
<br><br>Cash flow <br>
----------------------------------------------------- <br>
<?php 
$userIncome = mysql_result($userResult, 0, "income");
$userUpkeep = mysql_result($userResult, 0, "upkeep");
?>
Income: <?php echo $userIncome;?><br>
Upkeep: -<?php echo $userUpkeep;?><br>
Net Income: <?php echo $userIncome - $userUpkeep;?><br>


<!-- Achievements -->
<br><br>Achievements: <br>
----------------------------------------------------- <br>


<!-- Items -->
<br><br>Items: <br>
----------------------------------------------------- <br>
<?php 
$itemsQuery = "SELECT * FROM users_items JOIN items ON (users_items.item_id = items.id) WHERE users_items.user_id = "
. $userID . ";";
$itemsResult = mysql_query($itemsQuery);
$numItems = mysql_numrows($itemsResult);

for ($i = 0; $i < $numItems; $i++) {
	// Need to filter by type and display in different categories
	// Temporary code - doesn't filter type
	$quantity = mysql_result($itemsResult, $i, "quantity");
	$itemName = mysql_result($itemsResult, $i, "name");
	print $quantity . "x " . $itemName . "<br>";
}
mysql_close();
?>