<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/RealEstate.php");

$SELL_RATIO = .6;


$actionToDo = $_POST['actionToDo'];
$reID = $_POST['realEstateID'];
$realEstate = RealEstate::getRealEstate($reID);
$incomeChange = $realEstate->getIncome();

session_start();
$user = User::getUser($_SESSION['userID']);

if ($actionToDo == 'buy') {
	$purchasePrice = $_POST['purchasePrice'];
	$user->incrementUserRealEstate($reID, 1);
	$user->updateUserCashAndIncome($purchasePrice*-1, $incomeChange);
} else if ($actionToDo = 'sell') {	
	$sellBasePrice = $_POST['sellBasePrice'];
	$user->decrementUserRealEstate($reID, 1);
	$user->updateUserCashAndIncome($sellBasePrice*$SELL_RATIO, $incomeChange*-1);
}

header("Location: $serverRoot/shoprealestatelist.php");


?>