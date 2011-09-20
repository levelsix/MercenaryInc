<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


session_start();

// Session checks
if (isset($_SESSION['notValid'])) {
	print "The value you entered was not a valid amount. Note: no decimal values allowed. <br>";	
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/bankdeposit.php' onsubmit='return validateDeposit();' method='GET'>
<input type='text' name='amount' id='depositAmount'/>
<input type='submit' value='Deposit'/>
</form>
Note that there is a 10% fee on every deposit.

<!--  Withdrawal-->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/bankwithdrawal.php' onsubmit='return validateWithdraw();' method='GET'>
<input type='text' name='amount' id='withdrawAmount'/>
<input type='submit' value='Withdraw'/>
</form>


<script>
 function validateDeposit() {
   var depositAmount = document.getElementById('depositAmount').value;

  if (depositAmount.trim() == '') {
    alert('You did not enter a deposit amount.');
    return false;
	}

  if (isNaN(depositAmount.trim())) {
	    alert('That deposit amount is not a number.');
	    return false;
	  }
  if (depositAmount.trim() < 0) {
	    alert('You need to deposit a positive number.');
	    return false;
	  }

   return true;
 }

 function validateWithdraw() {
	   var withdrawAmount = document.getElementById('withdrawAmount').value;

	  if (withdrawAmount.trim() == '') {
	    alert('You did not enter a withdraw amount.');
	    return false;
		}

	  if (isNaN(withdrawAmount.trim())) {
		    alert('That withdraw amount is not a number.');
		    return false;
		  }
	  if (withdrawAmount.trim() < 0) {
		    alert('You need to withdraw a positive number.');
		    return false;
		  }

	   return true;
	 }
 </script>