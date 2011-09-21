<?php
/*
 * To include this code in your file, $profileUser must be set
 */


include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/RealEstate.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/playertypeproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/itemtypeproperties.php");


// User information
$profileUserID = $profileUser->getID();
$profileUserName = $profileUser->getName();
$profileUserLevel = $profileUser->getLevel();
$profileUserType = $profileUser->getType();
$profileUserMissionsCompleted = $profileUser->getNumMissionsCompleted();
$profileUserFightsWon = $profileUser->getFightsWon();
$profileUserFightsLost = $profileUser->getFightsLost();
$profileUserKills = $profileUser->getUserKills();
$profileUserDeaths = $profileUser->getUserDeaths();
?>

<?php echo $profileUserName;?><br>
Level <?php echo $profileUserLevel;?> <?php echo ucfirst(getPlayerTypeFromTypeID($profileUserType));?><br>
----------------------------------------------------- <br>
Missions Completed: <?php echo $profileUserMissionsCompleted;?><br>
Fights Won: <?php echo $profileUserFightsWon;?><br>
Fights Lost: <?php echo $profileUserFightsLost;?><br>
Kills: <?php echo $profileUserKills;?><br>
Deaths: <?php echo $profileUserDeaths;?><br>


<!--  Cash flow-->
<br><br>Cash flow <br>
----------------------------------------------------- <br>
<?php 
$profileUserIncome = $profileUser->getIncome();
$profileUserUpkeep = $profileUser->getUpkeep();
?>
Income: <?php echo $profileUserIncome;?><br>
Upkeep: -<?php echo $profileUserUpkeep;?><br>
Net Income: <?php echo $profileUser->getNetIncome();?><br>


<!-- Achievements -->
<br><br>Achievements: <br>
----------------------------------------------------- <br>


<!-- Items -->
<?php 
$itemIDsToQuantity = User::getUsersItemsIDsToQuantity($profileUserID);
$itemIDsToItems = Item::getItemIDsToItems(array_keys($itemIDsToQuantity));

for ($i = 1; $i <= $numItemTypes; $i++) {
	$itemTypeHeaderPrinted = false;
	foreach ($itemIDsToQuantity as $key => $value) {
		$item = $itemIDsToItems[$key];
		if ($item->getType() == $i) {
			if (!$itemTypeHeaderPrinted) {
				print "<br><br>".ucfirst(getItemTypeFromTypeID($i))."s<br>";
				print "----------------------------------------------------- <br>";
				$itemTypeHeaderPrinted = true;
			}
			print $value . "x " . $item->getName() . "<br>";
		}
	}
}
?>


<!-- Real Estate -->
<?php
$reIDsToQuantity = User::getUsersRealEstateIDsToQuantity($profileUserID);
if ($reIDsToQuantity && count($reIDsToQuantity) > 0) {
	print "<br><br>Real Estate: <br>";
	print "----------------------------------------------------- <br>";
	$reIDsToRealEstates = RealEstate::getRealEstateIDsToRealEstates(array_keys($reIDsToQuantity));
	foreach ($reIDsToQuantity as $key => $value) {
		$re = $reIDsToRealEstates[$key];
		print $value . "x " . $re->getName() . "<br>";
	}
}
?>