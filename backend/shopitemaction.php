<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/dbcolumnnames.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

$SELL_RATIO = .6;

$actionToDo = $_POST['actionToDo'];
$itemID = $_POST['itemID'];
$storePrice = $_POST['storePrice'];

session_start();
$user = User::getUser($_SESSION['userID']);

if ($actionToDo == 'buy') {
	$user->incrementUserItem($itemID, 1);
	$user->updateUserCash($storePrice*-1);
} else if ($actionToDo = 'sell') {	
	$user->decrementUserItem($itemID, 1);
	$user->updateUserCash($storePrice*$SELL_RATIO);
}

header("Location: $serverRoot/shopitemlist.php");
?>