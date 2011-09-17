<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

$itemIDsToQuantity = User::getUsersItemsIDsToQuantity($_SESSION['userID']);
if (!$itemIDsToQuantity || (count($itemIDsToQuantity) <= 0)) {
	print "You don't have any items!";
} else {
	print "You currently have: ";
	print "<br>";
	$itemIDsToItems = Item::getItemIDsToItems(array_keys($itemIDsToQuantity));
	foreach ($itemIDsToQuantity as $key => $value) {
		$item = $itemIDsToItems[$key];
		print $value . "x " . $item->getName() . "<br>";
	}
}		
?>