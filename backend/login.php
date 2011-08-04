<?php
include("../properties/dbproperties.php"); 

// Set the ID in the session
$id = $_POST['id'];
session_start();
$_SESSION['userID'] = $id;

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

// Check if daily bonus should be given
// Daily bonus will be given starting at 00:00:00 (midnight) PST -> 08:00:00 GMT
// For now we operate in PST since we're developing locally
//TODO: we change everything back to GMT when pushing to production
$userQuery = "SELECT * FROM users WHERE id = " . $id . ";";
$userResult = mysql_query($userQuery);
$lastLogin = mysql_result($userResult, 0, "last_login");
$currentDate = date('Y-m-d H:i:s');
//$dailyBonusDate = date('Y-m-d') . " 08:00:00";
$dailyBonusDate = date('Y-m-d') . " 00:00:00";

if (strcmp($currentDate, $dailyBonusDate) >= 0) {
	// <= or < here?
	if (strcmp($lastLogin, $dailyBonusDate) < 0) {
		// Give daily bonus and update last_login
		$dailyBonusAmount = 1000;
		/*
		$dailyBonusUpdate = "UPDATE users SET cash = cash + " . $dailyBonusAmount
			. ", last_login = CURRENT_TIMESTAMP WHERE id = " . $id . ";";
		*/
		$timestampUpdate = "UPDATE users SET cash = cash + " . $dailyBonusAmount
			. ", last_login = '" . $currentDate . "' WHERE id = " . $id . ";";
		$_SESSION['dailyBonus'] = $dailyBonusAmount;
	} else {
		// Update last_login in database
		//$timestampUpdate = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = " . $id . ";";
		$timestampUpdate = "UPDATE users SET last_login = '" . $currentDate . "' WHERE id = " . $id . ";";
	}
} else {
	// Update last_login in database
	//$timestampUpdate = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = " . $id . ";";
	$timestampUpdate = "UPDATE users SET last_login = '" . $currentDate . "' WHERE id = " . $id . ";";
}

mysql_query($timestampUpdate) or die(mysql_error());
mysql_close();

header("Location: ../charhome.php");
exit;
?>