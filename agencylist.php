<?php
include("topmenu.php");
?>

<form action='../recruit.php' method='GET'>
<input type='submit' value='Back'/>
</form>

<?php 
session_start();

$userId = $_SESSION['userID'];

$agencyStmt = $db->prepare("SELECT * FROM agencies WHERE (user_one_id = ? OR user_two_id = ?) AND accepted = 1");
$agencyStmt->execute(array($userId, $userId));

$agencySize = $agencyStmt->rowCount();

if ($agencySize == 0) {
	print "You currently have no other people in your agency.";
} else {
	print "People in your agency: <br>";
	while ($row = $agencyStmt->fetch(PDO::FETCH_ASSOC)) {
		$agentId = $row["user_one_id"];
		if ($agentId == $userId) $agentId = $row["user_two_id"];
		
		$userStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
		$userStmt->execute(array($agentId));
		$userName = "";
		if ($userResult = $userStmt->fetch(PDO::FETCH_ASSOC))
			$userName = $userResult['name'];
		?>
		<form action='externalplayerprofile.php' method='GET'>
		<input type='hidden' name='userID' value='<?php echo $agentId;?>'/>
		<input type='submit' value='<?php echo $userName;?>'/>
		</form>
		<?php
	}
}

?>