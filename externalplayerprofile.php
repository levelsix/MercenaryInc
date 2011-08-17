<?php
include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");

$userID = $_GET['userID'];

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute(array($userID));

$numRows = $userStmt->rowCount();

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$userResult) {
	// Redirect to error page
	header("Location: $serverRoot/errorpage.html");
	exit;
}

// Error: redirect
if ($numRows != 1) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

// User information
$userName = $userResult["name"];
$userLevel = $userResult["level"];
$userType = $userResult["type"];
$userMissionsCompleted = $userResult["missions_completed"];
$userFightsWon = $userResult["fights_won"];
$userFightsLost = $userResult["fights_lost"];
$userKills = $userResult["kills"];
$userDeaths = $userResult["deaths"];
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/attackplayer.php' method='POST'>
<input type='hidden' name='userID' value='<?php $userID?>'/>
<input type='submit' value='Attack'/>
</form>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/addplayertobounty.php' method='POST'>
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
$itemsStmt = $db->prepare("SELECT * FROM users_items JOIN items ON (users_items.item_id = items.id) WHERE users_items.user_id = ?");
$itemsStmt->execute(array($userID));

$numItems = $itemsStmt->rowCount();

while ($row = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
	// Need to filter by type and display in different categories
	// Temporary code - doesn't filter type
	$quantity = $row["quantity"];
	$itemName = $row["name"];
	print $quantity . "x " . $itemName . "<br>";
}
?>