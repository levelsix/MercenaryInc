<html>
<head></head>
<body>

<?php include("topmenu.php"); ?>

f<

<?php
// Show pending agency invitations
echo "Pending agency invitations: <br>";

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$agenciesQuery = "SELECT * FROM agencies WHERE user_two_id = " . $_SESSION['userID'] . " AND accepted = 0;";
$agenciesResult = mysql_query($agenciesQuery);
$numPending = mysql_numrows($agenciesResult);

for ($i = 0; $i < $numPending; $i++) {
	$inviterID = mysql_result($agenciesResult, $i, "user_one_id");
	$usersQuery = "SELECT * FROM users WHERE id = " . $inviterID . ";";
	$usersResult = mysql_query($usersQuery);
	$inviterName = mysql_result($usersResult, 0, "name");
	echo $inviterName;
	echo "<form action='backend/respondtoinvitation.php' method='POST'>";
	echo "<input type='submit' value='Acccept' />";
	echo "<input type='submit' value='Decline' />";
	echo "</form>";
}

// Show agency code
echo "Your agency code: <br>";

$userQuery = "SELECT * FROM users WHERE id = ". $_SESSION['userID'] . ";";
$userResult = mysql_query($userQuery);
$agencyCode = mysql_result($userResult, 0, "agency_code");

echo $agencyCode;
echo "<br>";

mysql_close();
?>

Invite using agency code: <br>
<form action="backend/inviteplayer.php" method="GET">
<input type="text" name="agencyCode"/>
<input type="submit" value="Recruit!"/>
</form>
</body>
</html>