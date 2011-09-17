<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();
$userID = $_SESSION['userID'];
$user = User::getUser($userID);
if (!$user) {
	// Redirect to error page. this isnt working. b/c theres text above?
	header("Location: $serverRoot/errorpage.html");
	exit;
}

// Skills button
?>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/skills.php'>
<input type='submit' value='Skills'/>
</form>

<?php


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


<!--  Cash flow-->
<br><br>Cash flow <br>
----------------------------------------------------- <br>
<?php 
$userIncome = $user->getIncome();
$userUpkeep = $user->getUpkeep();
?>
Income: <?php echo $userIncome;?><br>
Upkeep: -<?php echo $userUpkeep;?><br>
Net Income: <?php echo $user->getNetIncome();?><br>


<!-- Achievements -->
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