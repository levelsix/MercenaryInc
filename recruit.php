<html>
<head></head>
<body>

<?php include("topmenu.php");?>

<!-- Create link to agency list page -->
<form action='agencylist.php' method='GET'>
<input type='submit' value='My Agency'/>
</form>

<!--  Show pending agency invitations-->
Pending agency invitations: <br>
<?php 
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
?>

<?php echo $inviterName;?>
<form action='backend/respondtoinvitation.php' method='POST'>
	<input type='hidden' name='accepted' value='true'/>
	<input type='hidden' name='inviterID' value='<?php echo $inviterID;?>'/>
	<input type='submit' value='Accept'/>
</form>
<form action='backend/respondtoinvitation.php' method='POST'>
	<input type='hidden' name='accepted' value='false'/>
	<input type='hidden' name='inviterID' value='<?php echo $inviterID;?>'/>
	<input type='submit' value='Decline'/>
</form>
<?php 
}

// Show agency code
print "Your agency code: <br>";

$userQuery = "SELECT * FROM users WHERE id = ". $_SESSION['userID'] . ";";
$userResult = mysql_query($userQuery);
$agencyCode = mysql_result($userResult, 0, "agency_code");

print $agencyCode;
print "<br>";

mysql_close();
?>

Invite using agency code: <br>
<form action="backend/inviteplayer.php" method="GET">
<input type="text" name="agencyCode"/>
<input type="submit" value="Recruit!"/>
</form>
</body>
</html>
</html>
