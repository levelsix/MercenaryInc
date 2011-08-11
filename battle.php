<html>
<head>
</head>
<body>

<?php include("topmenu.php"); 

function displayNormalAttack() {
	
}

function displayBountyAttack() {	
	$bountyQuery = "SELECT * FROM bounties JOIN users ON (bounties.target_id = users.id) WHERE bounties.is_complete = 0;";
	$bountyResult = mysql_query($bountyQuery);
	$numBounties = mysql_numrows($bountyResult);
	
	if ($numBounties <= 0) {
		print "There are currently no bounties. Sorry!";
	} else {
		//print "Mercenary\t\tBounty <br>";
		for ($i = 0; $i < $numBounties; $i++) {
			$userID = mysql_result($bountyResult, $i, "target_id");
			$userName = mysql_result($bountyResult, $i, "name");
			$bountyAmount = mysql_result($bountyResult, $i, "payment");
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
mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

/*
 * TODO: if first time here, have them choose their class/type
	dont make this db call tho..
 */


if (isset($_POST['battleTab'])) {
	if ($_POST['battleTab']=='normal') {
		displayNormalAttack();
	}
	if ($_POST['battleTab']=='bounty') {
		displayBountyAttack();
	}
} else {
	displayNormalAttack();
}


?>


</body>
</html>