<html>
<head></head>
<body>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
if (isset($_SESSION['noUserWithAgencyCode']) && $_SESSION['noUserWithAgencyCode']) {
	echo "no invite sent because no user with that agency code exists";
	print "<br><br>";
	unset($_SESSION['noUserWithAgencyCode']);
}
if (isset($_SESSION['successInvite']) && $_SESSION['successInvite']) {
	echo "invite successfully sent";
	print "<br><br>";
	unset($_SESSION['successInvite']);
}


if (isset($_POST['recruitTab'])) {
	$_SESSION['recruitTab'] = $_POST['recruitTab'];
} else {
	$_SESSION['recruitTab'] = 'recruit';
}

?>


<!-- Create link to agency list page -->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/recruit.php' method='POST'>
	<input type='hidden' name='recruitTab' value='recruit'/>
	<input type='submit' value='Recruit'/>
</form>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/recruit.php' method='POST'>
	<input type='hidden' name='recruitTab' value='myagency'/>
	<input type='submit' value='My Agency'/>
</form>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/recruit.php' method='POST'>
	<input type='hidden' name='recruitTab' value='leaderboard'/>
	<input type='submit' value='Leaderboard'/>
</form>


<?php 
if (isset($_SESSION['recruitTab'])) {
	print "----------------------------------------------------<br>";
	
	if ($_SESSION['recruitTab'] == 'recruit') {
		?>
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
		<?php 
	}
	if ($_SESSION['recruitTab'] == 'myagency') {
		$recruitUser = $user;
		include_once($_SERVER['DOCUMENT_ROOT'] . "/agencylist.php");
	}
	if ($_SESSION['recruitTab'] == 'leaderboard') {
		echo "not sure if we're going to implement this";
	}
	unset($_SESSION['recruitTab']);
}
?>


</body>
</html>
</html>
