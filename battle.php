<html>
<head>
</head>
<body>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php"); 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Bounty.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");


function displayNormalAttack($user) {
	$opponents = $user->getPotentialOpponents();
	
	// treat each value as a row received from a PDO fetch
	foreach ($opponents as $opponent) {
		$id = $opponent->getID();
		$name = $opponent->getName();
		$opposingLevel = $opponent->getLevel();
		$opposingAgencySize = $opponent->getAgencySize();
		?>
		
		Mercenary: <a href='<?php $_SERVER['DOCUMENT_ROOT'] ?>/externalplayerprofile.php?userID=<?php echo $id;?>'><?php echo $name; ?></a>
		Level: <?php echo $opposingLevel; ?>
		Agency Size: <?php echo $opposingAgencySize; ?>
		<!-- Implement the attacking button and functionality -->
		<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/attackplayer.php" method="POST">
		<input type='hidden' name='userID' value='<?php echo $id; ?>' />
		<input type='submit' value='Attack'/>
		</form> 
		
		<?php
	}
}

function displayBountyAttack($user) {
	$bounties = Bounty::getBountiesForUser($user->getID());
	$numBounties = count($bounties);
	
	if ($numBounties <= 0) {
		print "There are currently no bounties. Sorry!";
	} else {
		foreach($bounties as $bounty) {
			
			$id = $bounty->getTargetID();
			//is this safe..
			$userName = $bounty->name;
			$bountyAmount = $bounty->getPayment();
?>
			
			Mercenary: <a href='<?php $_SERVER['DOCUMENT_ROOT'] ?>/externalplayerprofile.php?userID=<?php echo $id;?>'><?php echo $userName; ?></a> Bounty: <?php echo $bountyAmount;?>
			<!-- Implement the attacking button and functionality -->
			<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/attackplayer.php" method="POST">
			<input type='hidden' name='userID' value='<?php echo $id; ?>' />
			<input type='submit' value='Attack'/>
			</form>
			<?php 
		}
	}
}
?>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/battle.php' method='POST'>
<input type='hidden' name='battleTab' value='normal' />
<input type='submit' value='Attack an Enemy Agency'/>
</form>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/battle.php' method='POST'>
<input type='hidden' name='battleTab' value='bounty' />
<input type='submit' value='Check the Bounty List'/>
</form>

<?php 
/*
 * TODO: if first time here, have them choose their class/type
	dont make this db call tho..
 */

// Level up
if (isset($_SESSION['levelUp'])) {
	$newLevel = $_SESSION['newLevel'];
	$skillPointsGained = $_SESSION['skillPointsGained'];
	include_once($_SERVER['DOCUMENT_ROOT'] . "/levelupnotice.php");
	unset($_SESSION['newLevel']);
	unset($_SESSION['skillPointsGained']);
	unset($_SESSION['levelUp']);
}

// Session checks
if (isset($_SESSION['notEnoughHealth'])) {
	echo "You don't have enough health to battle! Please go heal yourself first. <br>";
	unset($_SESSION['notEnoughHealth']);
}
if (isset($_SESSION['otherNotEnoughHealth'])) {
	echo "Sorry, their health is too low to battle. Please choose a different target. <br>";
	unset($_SESSION['otherNotEnoughHealth']);
}
if (isset($_SESSION['notEnoughStamina'])) {
	echo "You don't have enough stamina to battle! Please try again later. <br>";
	unset($_SESSION['notEnoughStamina']);
}
if (isset($_SESSION['won'])) {
	if ($_SESSION['won'] == 'true') {
		echo "Congratulations! You won! <br>";
		echo "You gained " . $_SESSION['expGained'] . " experience! <br>";
	} else {
		echo "Sorry, you lost. <br>";
	}
	unset($_SESSION['won']);
}

$user = User::getUser($_SESSION['userID']);

if (isset($_POST['battleTab'])) {
	if ($_POST['battleTab'] == 'normal') {
		displayNormalAttack($user);
	}
	if ($_POST['battleTab'] == 'bounty') {
		displayBountyAttack($user);
	}
} else {
	if (isset($_SESSION['battleTab'])) {
		if ($_SESSION['battleTab'] == 'bounty') {
			displayBountyAttack($user);
		}
		unset($_SESSION['battleTab']);
	} else {
		displayNormalAttack($user);
	}
}
?>

</body>
</html>