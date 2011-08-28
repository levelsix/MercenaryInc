<html>
<head>
</head>
<body>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php"); 

function getRandomIntegers($n, $max) {
	$randomIntegers = array();
	while (count($randomIntegers) < $n) {
		$randomInt = rand(0, $max - 1);
		if (!isset($randomIntegers[$randomInt])) {
			$randomIntegers[$randomInt] = 1;
		}
	}
	return $randomIntegers;
}

function getPotentialOpponents($db, $userID, $level, $agencySize) {
	// get up to 10 people to list on the attack list
	$attackListSize = 10;
		
	$maxAgencySize = $agencySize + 5;
	$minAgencySize = max(array(1, $agencySize - 5));
	
	$usersStmt = $db->prepare("SELECT * FROM users WHERE level = ? AND agency_size <= ? AND agency_size >= ? AND id != ?");
	$usersStmt->execute(array($level, $maxAgencySize, $minAgencySize, $userID));
	$numUsers = $usersStmt->rowCount();
		
	// TODO: execute further queries with higher level or agency size ranges if too few users
	$randomIntegers = array();
	$allEligibleUsers = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
	
	if ($numUsers < $attackListSize) return $allEligibleUsers;
	
	// get random indices
	$randomIntegers = getRandomIntegers($attackListSize, $numUsers);
	// populate the array with these indices
	$opponents = array();
	foreach ($randomIntegers as $key=>$value) {
		array_push($opponents, $allEligibleUsers[$key]);
	}
	
	return $opponents;
}

function displayNormalAttack($db, $userID, $level, $agencySize) {
	$opponents = getPotentialOpponents($db, $userID, $level, $agencySize);
	
	// treat each value as a row received from a PDO fetch
	foreach ($opponents as $value) {
		$id = $value['id'];
		$name = $value['name'];
		$opposingLevel = $value['level'];
		$opposingAgencySize = $value['agency_size'];
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

function displayBountyAttack($db, $userID) {
	$bountyStmt = $db->prepare("SELECT * FROM bounties JOIN users ON (bounties.target_id = users.id) WHERE bounties.is_complete = 0 AND bounties.target_id != ?");
	$bountyStmt->execute(array($userID));
	$numBounties = $bountyStmt->rowCount();
	
	if ($numBounties <= 0) {
		print "There are currently no bounties. Sorry!";
	} else {
		while ($row = $bountyStmt->fetch(PDO::FETCH_ASSOC)) {
			$id = $row["target_id"];
			$userName = $row["name"];
			$bountyAmount = $row["payment"];
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
	} else {
		echo "Sorry, you're a Conrad (i.e. a loser). <br>";
	}
	unset($_SESSION['won']);
}

$userID = $_SESSION['userID'];
// $result is from topmenu
$playerAgencySize = $result['agency_size'];

if (isset($_POST['battleTab'])) {
	if ($_POST['battleTab'] == 'normal') {
		displayNormalAttack($db, $userID, $playerLevel, $playerAgencySize);
	}
	if ($_POST['battleTab'] == 'bounty') {
		displayBountyAttack($db, $userID);
	}
} else {
	if (isset($_SESSION['battleTab'])) {
		if ($_SESSION['battleTab'] == 'bounty') {
			displayBountyAttack($db, $userID);
		}
		unset($_SESSION['battleTab']);
	} else {
		displayNormalAttack($db, $userID, $playerLevel, $playerAgencySize);
	}
}
?>


</body>
</html>