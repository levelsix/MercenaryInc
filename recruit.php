<html>
<head></head>
<body>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");?>

<!-- Create link to agency list page -->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/agencylist.php' method='GET'>
<input type='submit' value='My Agency'/>
</form>

<!--  Show pending agency invitations-->
Pending agency invitations: <br>
<?php 
$pendingAgencyInviteUsers = $user->getPendingAgencyInviteUsers();
foreach($pendingAgencyInviteUsers as $pendingUser) {
	$inviterID = $pendingUser->getID();
	$inviterName = $pendingUser->getName();
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
print $user->getAgencyCode();
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
