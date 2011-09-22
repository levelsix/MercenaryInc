<?php
include_once("topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

function calculateHealCost($user) {
	//dummy value. the value is random, based on level, and can't exceed what's in user bank.
	return 50;
}


$user = User::getUser($_SESSION['userID']);

if ($user->getHealth() >= $user->getHealthMax()) {
	echo "You are already at full health.";
	print "<br>";
} else {
	$healCost = calculateHealCost($user);
	?>
	Pay the doctor to regain all your health. You must pay him from the bank.<br>
	You currently have $<?php echo $user->getBankBalance();?> cash in the bank.
	<form action='backend/healuser.php' method='post'>
		<input type='hidden' name='healCost' value='<?php echo $healCost?>' />
		<input type='submit' value='Heal for $<?php echo $healCost?>' />
	</form>
	<?php	
}
?>