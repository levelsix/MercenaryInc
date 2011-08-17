<?php
include($_SERVER['DOCUMENT_ROOT'] . "/properties/dbproperties.php");
include($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database.");
session_start();

$userID = $_SESSION['userID'];
$amount = $_GET['amount'];

if (!is_numeric($amount) || strrchr($amount, '.')) {
	$_SESSION['notValid'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$userQuery = "SELECT * FROM users WHERE id = " . $userID . ";";
$userResult = mysql_query($userQuery);

$userBalance = mysql_result($userResult, 0, "bank_balance");
if ($amount > $userBalance) {
	$_SESSION['notEnoughBalance'] = 'true';
	header("Location: $serverRoot/bank.php");
	exit;
}

$cashUpdate = "UPDATE users SET cash = cash + " . $amount . ", bank_balance = bank_balance - "
. $amount . " WHERE id = " . $userID . ";";
mysql_query($cashUpdate) or die(mysql_error);

mysql_close();
$_SESSION['withdrew'] = $amount;
header("Location: $serverRoot/bank.php");
exit;
?>