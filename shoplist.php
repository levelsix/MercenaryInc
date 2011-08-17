<?php 
include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/itemtypeproperties.php");

$stmt = $db->prepare("SELECT * FROM items WHERE min_level <= ? ORDER BY min_level");
$stmt->execute(array($playerLevel + 1));

$num = $stmt->rowCount();

function itemIsLocked($row, $playerLevel) {
	if ($row["min_level"] == ($playerLevel + 1)) {
		return true;
	}
	return false;
}

if ($num == 0) { 
	echo "No items available.";
} else {
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if (itemIsLocked($row, $playerLevel)) {
			print "<b>LOCKED</b> <br>";
		}
		
		print "Item: " . $row['name'] . "<br>";
		
		$type = $row['type'];
		$itemType = "";
		if ($type == 1) {
			$itemType = $itemtype1;
		} else if ($type == 2) {
			$itemType = $itemtype2;
		} else if ($type == 3) {
			$itemType = $itemtype3;
		}
		
		$itemPrice = $row['price'];
		
		$item_id = $row['id'];
		
		$quantityOwnedStmt = $db->prepare("SELECT quantity FROM users_items WHERE user_id = ? AND item_id = ?");
		$quantityOwnedStmt->execute(array($_SESSION['userID'], $item_id));
		
		$quantityOwnedResult = $quantityOwnedStmt->fetch(PDO::FETCH_ASSOC);
		
		$quantity = 0;
		if ($quantityOwnedStmt->rowCount() > 0 && $quantityOwnedResult) {
			$quantity = $quantityOwnedResult['quantity'];
		}
?>		

		Type: <?php echo $itemType;?><br>
		Minimum level: <?php echo $row["min_level"];?><br>
		Price: <?php echo $itemPrice?><br>
		Quantity Owned: <?php echo $quantity?><br>
<?php 	
		if (($playerCash >= $itemPrice) && (!itemIsLocked($row, $playerLevel))){
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shopaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='buy' />
			<input type='hidden' name='storePrice' value='<?php echo $itemPrice;?>' />
			<input type='hidden' name='itemID' value='<?php echo $item_id;?>' />
			<input type='submit' value='Buy' />
			</form>
<?php 
		} else {
			echo "you can't buy this item (you don't have enough cash or it's locked)<br>";
		}
		if ($quantity >= 1 && !itemIsLocked($row, $playerLevel)) {
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shopaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='sell' />
			<input type='hidden' name='storePrice' value='<?php echo $itemPrice;?>' />
			<input type='hidden' name='itemID' value='<?php echo $item_id;?>' />
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