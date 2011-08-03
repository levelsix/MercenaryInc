<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

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
?>