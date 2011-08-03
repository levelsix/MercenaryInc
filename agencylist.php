<?php
include("topmenu.php");

print "<form action='../recruit.php' method='GET'>";
print "<input type='submit' value='Back'/>";
print "</form>";

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");
session_start();

$userId = $_SESSION['userID'];

$agencyQuery = "SELECT * FROM agencies WHERE (user_one_id = " . $userId 
. " OR user_two_id = " . $userId . ") AND accepted = 1;";
$agencyResult = mysql_query($agencyQuery);
$agencySize = mysql_numrows($agencyResult);

if ($agencySize == 0) {
	print "You currently have no other people in your agency.";
} else {
	print "People in your agency: <br>";
	for ($i = 0; $i < $agencySize; $i++) {
		$agentId = mysql_result($agencyResult, $i, "user_one_id");
		if ($agentId == $userId) $agentId = mysql_result($agencyResult, $i, "user_two_id");
		$userQuery = "SELECT * FROM users WHERE id = " . $agentId . ";";
		$userResult = mysql_query($userQuery);
		$userName = mysql_result($userResult, 0, "name");
		print "<form action='externalplayerprofile.php' method='GET'>";
		print "<input type='hidden' name='userID' value='" . $agentId . "'/>";
		print "<input type='submit' value='" . $userName . "'/>";
		print "</form>";
	}
}

mysql_close();
?>