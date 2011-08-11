<?php
include("topmenu.php");
?>

<form action='../recruit.php' method='GET'>
<input type='submit' value='Back'/>
</form>

<?php 
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
		?>
		<form action='externalplayerprofile.php' method='GET'>
		<input type='hidden' name='userID' value='<?php echo $agentId;?>'/>
		<input type='submit' value='<?php echo $userName;?>'/>
		</form>
		<?php
	}
}

mysql_close();
?>