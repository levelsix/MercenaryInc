<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$userID = $_GET['userID'];
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


<!--  Action buttons
Give option to attack, add to bounty list -->
<form action='attackplayer.php' method='POST'>
<input type='hidden' name='userID' value='<?php $userID?>'/>
<input type='submit' value='Attack'/>
</form>

<form action='addplayertobounty.php' method='POST'>
<input type='hidden' name='targetID' value='<?php $userID?>'/>
<input type='submit' value='Add to Bounty List'/>
</form>





<!-- Achievements (implement + query)-->
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