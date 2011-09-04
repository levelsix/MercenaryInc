<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

session_start();

$userID = $_SESSION['userID'];
$amount = $_GET['amount'];

if (!is_numeric($amount) || strrchr($amount, '.')) {
	$_SESSION['notValid'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$db = ConnectionFactory::getFactory()->getConnection();

$user = User::getUser($userID);

if ($amount > $user->getBankBalance()) {
	$_SESSION['notEnoughBalance'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}


if (!$user->withdrawBankGainCash($amount)) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$_SESSION['withdrew'] = $amount;
header("Location: $serverRoot/bank.php");
exit;
?>