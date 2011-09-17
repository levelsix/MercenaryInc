<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");

$userID = $_GET['userID'];
$user = User::getUser($userID);
if (!$user) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

// User information
$userName = $user->getName();
$userLevel = $user->getLevel();
$userType = $user->getType();
$userMissionsCompleted = $user->getNumMissionsCompleted();
$userFightsWon = $user->getFightsWon();
$userFightsLost = $user->getFightsLost();
$userKills = $user->getUserKills();
$userDeaths = $user->getUserDeaths();
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/attackplayer.php' method='POST'>
<input type='hidden' name='userID' value='<?php echo $userID;?>'/>
<input type='submit' value='Attack'/>
</form>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/addplayertobounty.php' method='POST'>
<input type='hidden' name='targetID' value='<?php echo $userID;?>'/>
<input type='submit' value='Add to Bounty List'/>
</form>





<!-- Achievements (implement + query)-->
<br><br>Achievements: <br>
----------------------------------------------------- <br>


<!-- Items -->
<br><br>Items: <br>
----------------------------------------------------- <br>
<?php 
$itemIDsToQuantity = User::getUsersItemsIDsToQuantity($userID);
$itemIDsToItems = Item::getItemIDsToItems(array_keys($itemIDsToQuantity));
foreach ($itemIDsToQuantity as $key => $value) {
	$item = $itemIDsToItems[$key];
	print $value . "x " . $item->getName() . "<br>";
}
?>