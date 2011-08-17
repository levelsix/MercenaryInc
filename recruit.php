<html>
<head></head>
<body>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");?>

<!-- Create link to agency list page -->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/agencylist.php' method='GET'>
<input type='submit' value='My Agency'/>
</form>

<!--  Show pending agency invitations-->
Pending agency invitations: <br>
<?php 
$agenciesStmt = $db->prepare("SELECT * FROM agencies WHERE user_two_id = ? AND accepted = 0");
$agenciesStmt->execute(array($_SESSION['userID']));
$numPending = $agenciesStmt->rowCount();

while ($row = $agenciesStmt->fetch(PDO::FETCH_ASSOC)) {
	$inviterID = $row["user_one_id"];
	$usersStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
	$usersStmt->execute(array($inviterID));
	$usersResult = $usersStmt->fetch(PDO::FETCH_ASSOC);
	$inviterName = "";
	if (!$usersResult) {
		continue;
	} else {
		$inviterName = $usersResult["name"];
	}
?>

<?php echo $inviterName;?>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/respondtoinvitation.php' method='POST'>
	<input type='hidden' name='accepted' value='true'/>
	<input type='hidden' name='inviterID' value='<?php echo $inviterID;?>'/>
	<input type='submit' value='Accept'/>
</form>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/respondtoinvitation.php' method='POST'>
	<input type='hidden' name='accepted' value='false'/>
	<input type='hidden' name='inviterID' value='<?php echo $inviterID;?>'/>
	<input type='submit' value='Decline'/>
</form>
<?php 
}

// Show agency code
print "Your agency code: <br>";

$userStmt = $db->prepare("SELECT agency_code FROM users WHERE id = ?");
$userStmt->execute(array($_SESSION['userID']));

$userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
$agencyCode = "";
if ($userResult) $agencyCode = $userResult['agency_code'];

print $agencyCode;
print "<br>";
?>

Invite using agency code: <br>
<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/inviteplayer.php" method="GET">
<input type="text" name="agencyCode"/>
<input type="submit" value="Recruit!"/>
</form>
</body>
</html>
</html>
