<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");

$itemStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ?");
$itemStmt->execute(array($_SESSION['userID']));

$numRows = $itemStmt->rowCount();
if ($numRows <= 0) {
	print "You don't have any items!";
} else {
	print "You currently have: ";
	print "<br>";
	while ($row = $itemStmt->fetch(PDO::FETCH_ASSOC)) {
		$itemID = $row["item_id"];

		$itemNameStmt = $db->prepare("SELECT name FROM items WHERE id = ?");
		$itemNameStmt->execute(array($itemID));
		
		$itemNameResult = $itemNameStmt->fetch(PDO::FETCH_ASSOC);
		if (!$itemNameResult) continue;
		
		$itemName = $itemNameResult["name"];
		$itemQuantity = $row["quantity"];
		
		print $itemQuantity . "x " . $itemName;
		print "<br>";
	}
}
?>