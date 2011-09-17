<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/itemtypeproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

function itemIsLocked($item, $playerLevel) {
	if ($item->getMinLevel() == ($playerLevel + 1)) {
		return true;
	}
	return false;
}

$itemIDsToItems = Item::getItemIDsToItemsVisibleInShop($playerLevel);
$num = count($itemIDsToItems);

if ($num == 0) { 
	echo "No items available.";
} else {
	$userItemIDsToQuantity = User::getUsersItemsIDsToQuantity($_SESSION['userID']);
	foreach ($itemIDsToItems as $itemID => $item) {
		if (itemIsLocked($item, $playerLevel)) {
			print "<b>LOCKED</b> <br>";
		}
		
		print "Item: " . $item->getName() . "<br>";
		
		$itemType = getItemTypeFromTypeID($item->getType());
		$itemPrice = $item->getPrice();
			
		$quantity = 0;
		
		if ($userItemIDsToQuantity && array_key_exists($itemID, $userItemIDsToQuantity)) {
			$quantity = $userItemIDsToQuantity[$item->getID()];
		}
		?>

		Type: <?php echo $itemType;?><br>
		Minimum level: <?php echo $item->getMinLevel();?><br>
		Price: <?php echo $itemPrice?><br>
		Quantity Owned: <?php echo $quantity?><br>
<?php 	
		if (($playerCash >= $itemPrice) && (!itemIsLocked($item, $playerLevel))){
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shopaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='buy' />
			<input type='hidden' name='storePrice' value='<?php echo $itemPrice;?>' />
			<input type='hidden' name='itemID' value='<?php echo $itemID;?>' />
			<input type='submit' value='Buy' />
			</form>
<?php 
		} else {
			echo "you can't buy this item (you don't have enough cash or it's locked)<br>";
		}
		if ($quantity >= 1 && !itemIsLocked($item, $playerLevel)) {
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shopaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='sell' />
			<input type='hidden' name='storePrice' value='<?php echo $itemPrice;?>' />
			<input type='hidden' name='itemID' value='<?php echo $itemID;?>' />
			<input type='submit' value='Sell' />
			</form>
<?php 
		} else {
			print "you can't sell this item (don't have any or it's locked)<br>";
		}

		
		print "<br><br>";
	}	
}
?>