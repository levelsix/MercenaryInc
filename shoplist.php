<?php 
include("topmenu.php");
include("properties/itemtypeproperties.php");
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
$query="SELECT * FROM items WHERE min_level <= ". ($playerLevel+1) . " ORDER BY min_level;";
$result=mysql_query($query);
$num=mysql_numrows($result);

function itemIsLocked($result, $itemNum, $playerLevel) {
	if (mysql_result($result,$itemNum,"min_level") == ($playerLevel+1)) {
		return true;
	}
	return false;
}

if ($num == 0) { 
	echo "No items available";
} else {
	
	$i = 0;
	while ($i < $num) {
		if (itemIsLocked($result, $i, $playerLevel)) {
			print "<b>LOCKED</b> <br>";
		}
		
		print "Item: " . mysql_result($result,$i,"name") . "<br>";
		if (mysql_result($result,$i, "type") == 1) {
			$itemType = $itemtype1;
		} else if (mysql_result($result,$i, "type") == 2) {
			$itemType = $itemtype2;
		} else if (mysql_result($result,$i, "type") == 3) {
			$itemType = $itemtype3;
		}		
		$itemPrice = mysql_result($result,$i,"price");
		
		$item_id = mysql_result($result,$i, "id");
		
		$quantityOwnedQuery = "SELECT quantity FROM users_items WHERE user_id = " . $_SESSION['userID'] . " AND ";
		$quantityOwnedQuery .= "item_id = " . $item_id . ";";
		$quantityResult = mysql_query($quantityOwnedQuery);
		
		if (mysql_numrows($quantityResult) > 0) 
			$quantity = mysql_result($quantityResult, 0, "quantity");
		else
			$quantity = 0;
?>		

		Type: <?php echo $itemType;?><br>
		Minimum level: <?php echo mysql_result($result,$i,"min_level");?><br>
		Price: <?php echo $itemPrice?><br>
		Quantity Owned: <?php echo $quantity?><br>
<?php 	
		if (($playerCash >= $itemPrice) && (!itemIsLocked($result, $i, $playerLevel))){
?>
			<form action='backend/shopaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='buy' />
			<input type='hidden' name='storePrice' value='<?php echo $itemPrice;?>' />
			<input type='hidden' name='itemID' value='<?php echo $item_id;?>' />
			<input type='submit' value='Buy' />
			</form>
<?php 
		} else {
			echo "you can't buy this item (you don't have enough cash or it's locked)<br>";
		}
		if ($quantity >= 1 && !itemIsLocked($result, $i, $playerLevel)) {
?>
			<form action='backend/shopaction.php' method='post'>
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
		
		$i++;
	}
	
}


mysql_close(); 

?>