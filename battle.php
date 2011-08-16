<html>
<head>
</head>
<body>

<?php include("topmenu.php"); 

function displayNormalAttack() {
	
}

function displayBountyAttack() {
	$db = ConnectionFactory::getFactory()->getConnection();
	
	$bountyStmt = $db->prepare("SELECT * FROM bounties JOIN users ON (bounties.target_id = users.id) WHERE bounties.is_complete = 0");
	$bountyStmt->execute();
		
	$numBounties = $bountyStmt->rowCount();
	
	if ($numBounties <= 0) {
		print "There are currently no bounties. Sorry!";
	} else {
		//print "Mercenary\t\tBounty <br>";
		while ($row = $bountyStmt->fetch(PDO::FETCH_ASSOC)) {
			$userID = $row["target_id"];
			$userName = $row["name"];
			$bountyAmount = $row["payment"];
?>
			
			Mercenary: <a href='externalplayerprofile.php?userID=<?php echo $userID;?>'><?php echo $userName?></a> Bounty: <?php echo $bountyAmount;?>
			<!-- Implement the attacking button and functionality -->
			<form action="attackplayer.php" method="POST">
			<input type='hidden' name='userID' value='<?php echo $userID?>' />
			<input type='submit' value='Attack'/>
			</form>
			<?php 
		}
	}
}
?>

<form action='battle.php' method='post'>
<input type='hidden' name='battleTab' value='normal' />
<input type='submit' value='Attack an Enemy Agency'/>
</form>

<form action='battle.php' method='post'>
<input type='hidden' name='battleTab' value='bounty' />
<input type='submit' value='Check the Bounty List'/>
</form>

<?php 
/*
 * TODO: if first time here, have them choose their class/type
	dont make this db call tho..
 */

if (isset($_POST['battleTab'])) {
	if ($_POST['battleTab'] == 'normal') {
		displayNormalAttack();
	}
	if ($_POST['battleTab'] == 'bounty') {
		displayBountyAttack();
	}
} else {
	displayNormalAttack();
}
?>


</body>
</html>