<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


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
$user = User::getUser($userID);
$bankBalance = $user->getBankBalance();

?>

Your current bank balance is <?php echo $bankBalance; ?>.

<!--  Deposit-->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/bankdeposit.php' method='GET'>
<input type='text' name='amount'/>
<input type='submit' value='Deposit'/>
</form>
Note that there is a 10% fee on every deposit.

<!--  Withdrawal-->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/bankwithdrawal.php' method='GET'>
<input type='text' name='amount'/>
<input type='submit' value='Withdraw'/>
</form>