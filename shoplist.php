<?php 
include("topmenu.php");
include("properties/itemtypeproperties.php");
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
$query="SELECT * FROM items WHERE min_level <= ". ($playerLevel+1) . " ORDER BY min_level;";
$result=mysql_query($query);
$num=mysql_numrows($result);


if ($num == 0) { 
	echo "No items available";
} else {
	
	$i = 0;
	while ($i < $num) {
		if (mysql_result($result,$i,"min_level") == ($playerLevel+1)) {
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
		print "Type: " . $itemType . "<br>";
		print "Minimum level: " . mysql_result($result,$i,"min_level") . "<br>";
		print "Price: " . mysql_result($result,$i,"price") . "<br>";

		$quantityOwnedQuery = "SELECT quantity FROM users_items WHERE user_id = " . $_SESSION['userID'] . " AND ";
		$quantityOwnedQuery .= "item_id = " . mysql_result($result,$i, "id") . ";";
		$quantityResult = mysql_query($quantityOwnedQuery);
		
		if (mysql_numrows($quantityResult) > 0) 
			$quantity = mysql_result($quantityResult, 0, "quantity");
		else
			$quantity = 0;
		
		print "Quantity Owned: " . $quantity . "<br>";
		
		/*
		print "<form action='backend/useskill.php' method='post'>";
		print "<input type='hidden' name='attributeToIncrease' value='energymax' />";
		print "<input type='submit' value='Buy' />";
		print "</form>";
		*/
		
		print "<br><br>";
		
		$i++;
	}
	
}


mysql_close(); 

?>