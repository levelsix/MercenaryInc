<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbcolumnnames.php");

$SELL_RATIO = .6;

$actionToDo = $_POST['actionToDo'];
$itemID = $_POST['itemID'];
$storePrice = $_POST['storePrice'];

session_start();

$db = ConnectionFactory::getFactory()->getConnection();

$currentStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ? AND item_id = ?");
$currentStmt->execute(array($_SESSION['userID'], $itemID));

$numCurrent = $currentStmt->rowCount();

$currentResult = $currentStmt->fetch(PDO::FETCH_ASSOC);
if (!$currentResult) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

if ($actionToDo == 'buy') {
	if ($numCurrent == 0) {
		$stmt = $db->prepare("INSERT INTO users_items (user_id, item_id, quantity) VALUES (?, ?, 1)");
		$params = array($_SESSION['userID'], $itemID);
	} else {
		$stmt = $db->prepare("UPDATE users_items SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?");
		$params = array($_SESSION['userID'], $itemID);
	}
} else if ($actionToDo = 'sell') {	
	if ($numCurrent > 0) {	//should always be, but just in case
		if ($currentResult['quantity'] == 1) {
			$stmt = $db->prepare("DELETE FROM users_items WHERE user_id = ? AND item_id = ?");
			$params = array($_SESSION['userID'], $itemID);
		} else {
			$stmt = $db->prepare("UPDATE users_items SET quantity = quantity - 1 WHERE user_id = ? AND item_id = ?");
			$params = array($_SESSION['userID'], $itemID);
		}
	}	
}
if (!($stmt->execute($params))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

// Update cash
if ($actionToDo == 'buy') {
	$stmt = $db->prepare("UPDATE users SET cash = cash - ? WHERE id = ?");
	$params = array($storePrice, $_SESSION['userID']);
} else if ($actionToDo = 'sell') {	
	$stmt = $db->prepare("UPDATE users SET cash = cash + ? WHERE id = ?");
	$params = array($storePrice * $SELL_RATIO, $_SESSION['userID']);
}
if (!($stmt->execute($params))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

header("Location: $serverRoot/shoplist.php");
?>