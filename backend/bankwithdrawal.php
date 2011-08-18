<?php
include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

session_start();

$userID = $_SESSION['userID'];
$amount = $_GET['amount'];

if (!is_numeric($amount) || strrchr($amount, '.')) {
	$_SESSION['notValid'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$db = ConnectionFactory::getFactory()->getConnection();

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute(array($userID));

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$userResult) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$userBalance = $userResult["bank_balance"];
if ($amount > $userBalance) {
	$_SESSION['notEnoughBalance'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$cashUpdateStmt = $db->prepare("UPDATE users SET cash = cash + ?, bank_balance = bank_balance - ? WHERE id = ?");
if (!($cashUpdateStmt->execute(array($amount, $amount, $userID)))) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$_SESSION['withdrew'] = $amount;
header("Location: $serverRoot/bank.php");
exit;
?>