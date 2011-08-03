<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database.");
session_start();

// Session checks
if (isset($_SESSION['notValid'])) {
	print "The value you entered was not a valid amount. <br>";
	unset($_SESSION['notValid']);
}
if (isset($_SESSION['notEnoughCash'])) {
	print "You think you have that much cash? <br>";
	unset($_SESSION['notEnoughCash']);
}
if (isset($_SESSION['deposited'])) {
	print "You successfully deposited " . $_SESSION['deposited'] . " cash. <br>";
	unset($_SESSION['deposited']);
}
if (isset($_SESSION['notEnoughBalance'])) {
	print "You think your bank balance is that big? <br>";
	unset($_SESSION['notEnoughBalance']);
}
if (isset($_SESSION['withdrew'])) {
	print "You successfully withdrew " . $_SESSION['withdrew'] . " cash. <br>";
	unset($_SESSION['withdrew']);
}

$userID = $_SESSION['userID'];
$bankQuery = "SELECT * FROM users WHERE id = " . $userID . ";";
$bankResult = mysql_query($bankQuery);

$bankBalance = mysql_result($bankResult, 0, "bank_balance");
print "Your current bank balance is " . $bankBalance . ".<br>";

// Deposit
print "<form action='backend/bankdeposit.php' method='GET'>";
print "<input type='text' name='amount'/>";
print "<input type='submit' value='Deposit'/>";
print "</form>";
print "Note that there is a 10% fee on every deposit.";

// Withdrawal
print "<form action='backend/bankwithdrawal.php' method='GET'>";
print "<input type='text' name='amount'/>";
print "<input type='submit' value='Withdraw'/>";
print "</form>";

mysql_close();
?>