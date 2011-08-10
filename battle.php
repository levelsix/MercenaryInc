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
			//print $userName . "\t\t" . $bountyAmount;
			print "Mercenary: <a href='externalplayerprofile.php?userID=" . $userID . "'>" . $userName . "</a> Bounty: " . $bountyAmount;
			// Implement the attacking button and functionality
			print "<form>";
			print "<input type='submit' value='Attack'/>";
			print "</form>";
		}
	}
}

print "<form action='battle.php' method='post'>";
print "<input type='hidden' name='battleTab' value='normal' />";
print "<input type='submit' value='Attack an Enemy Agency'/>";
print "</form>";

print "<form action='battle.php' method='post'>";
print "<input type='hidden' name='battleTab' value='bounty' />";
print "<input type='submit' value='Check the Bounty List'/>";
print "</form>";

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

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