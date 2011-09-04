<?php

include($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

session_start();

$userID = $_SESSION['userID'];
$amount = $_GET['amount'];

if (!is_numeric($amount) || strrchr($amount, '.')) {
	$_SESSION['notValid'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$user = User::getUser($userID);

if ($amount > $user->getCash()) {
	$_SESSION['notEnoughCash'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$toBeDeposited = round(0.9 * $amount);

if (!$user->withdrawBankGainCash($amount)) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$_SESSION['deposited'] = $toBeDeposited;
header("Location: $serverRoot/bank.php");
exit;
?>