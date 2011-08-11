<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database.");
session_start();

// Session checks
if (isset($_SESSION['notValid'])) {
	print "The value you entered was not a valid amount. <
r>";	unset($_SESSION['notValid']);
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
?>

Your current bank balance is <?php echo $bankBalance; ?>.

<!--  Deposit-->
<form action='backend/bankdeposit.php' method='GET'>
<input type='text' name='amount'/>
<input type='submit' value='Deposit'/>
</form>
Note that there is a 10% fee on every deposit.

<!--  Withdrawal-->
<form action='backend/bankwithdrawal.php' method='GET'>
<input type='text' name='amount'/>
<input type='submit' value='Withdraw'/>
</form>

<?php 
mysql_close();
?>