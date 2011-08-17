<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbcolumnnames.php");

$SELL_RATIO = .6;

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$actionToDo = $_POST['actionToDo'];
$itemID = $_POST['itemID'];
$storePrice = $_POST['storePrice'];

session_start();

$currentQuery="SELECT * FROM users_items WHERE user_id = ". $_SESSION['userID'];
$currentQuery.=" AND item_id = ".$itemID.";";
$currentResult=mysql_query($currentQuery);
$numCurrent=mysql_numrows($currentResult);


if (strcmp($actionToDo, 'buy') == 0) {
	if ($numCurrent == 0) {
		$query = "INSERT INTO users_items (user_id, item_id, quantity) VALUES
			(".$_SESSION['userID'].", ". $itemID .", 1);"; 

	} else {
		$query = "UPDATE users_items SET quantity=quantity+1 WHERE user_id=" . $_SESSION['userID'];
		$query.=" AND item_id = ".$itemID.";";
	}
} else if (strcmp($actionToDo, 'sell') == 0) {	
	if ($numCurrent > 0) {	//should always be, but just in case
		if (mysql_result($currentResult, 0,"quantity") == 1) {
			$query = "DELETE FROM users_items WHERE user_id=" . $_SESSION['userID'];
			$query.=" AND item_id = ".$itemID.";";
		} else {
			$query = "UPDATE users_items SET quantity=quantity-1 WHERE user_id=" . $_SESSION['userID'];
			$query.=" AND item_id = ".$itemID.";";
		}
	}	
}
mysql_query($query) or die(mysql_error());

if (strcmp($actionToDo, 'buy') == 0) {
	$query = "UPDATE users SET cash=cash-".$storePrice." WHERE id=" . $_SESSION['userID'] .";";
} else if (strcmp($actionToDo, 'sell') == 0) {	
	$query = "UPDATE users SET cash=cash+".($storePrice*$SELL_RATIO)." WHERE id=" . $_SESSION['userID'] .";";
}
mysql_query($query) or die(mysql_error());

mysql_close();

header("Location: $serverRoot/shoplist.php");
?>