<?php
include("../classes/ConnectionFactory.php");

// Set the ID in the session
$id = $_POST['id'];
session_start();
session_destroy();
session_start();
$_SESSION['userID'] = $id;

// Check if daily bonus should be given
// Daily bonus will be given starting at 00:00:00 (midnight) PST -> 08:00:00 GMT
// For now we operate in PST since we're developing locally
//TODO: we change everything back to GMT when pushing to production
$dbh = ConnectionFactory::getFactory()->getConnection();

// Get last login time
$smh = $dbh->prepare("SELECT last_login FROM users WHERE id = ?");
$smh->execute(array($id));
$result = $smh->fetch(PDO::FETCH_ASSOC);
$lastLogin = $result['last_login'];

$currentDate = date('Y-m-d H:i:s');
//$dailyBonusDate = date('Y-m-d') . " 08:00:00";
$dailyBonusDate = date('Y-m-d') . " 00:00:00";

if (strcmp($currentDate, $dailyBonusDate) >= 0) {
	// <= or < here?
	if (strcmp($lastLogin, $dailyBonusDate) < 0) {
		// Give daily bonus and update last_login
		$dailyBonusAmount = 1000;
		/*
		$smh = $dbh->prepare("UPDATE users SET cash = cash + ?, last_login = CURRENT_TIMESTAMP WHERE id = ?");
		$smh->execute(array($dailyBonusAmount, $id));
		*/
		$smh = $dbh->prepare("UPDATE users SET cash = cash + ?, last_login = ? WHERE id = ?");
		$smh->execute(array($dailyBonusAmount, $currentDate, $id));
		$_SESSION['dailyBonus'] = $dailyBonusAmount;
	} else {
		// Update last_login in database
		//$smh = $dbh->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
		//$smh->execute(array($id));
		$smh = $dbh->prepare("UPDATE users SET last_login = ? WHERE id = ?");
		$smh->execute($currentDate, $id);
	}
} else {
	// Update last_login in database
	//$smh = $dbh->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
	//$smh->execute(array($id));
	$smh = $dbh->prepare("UPDATE users SET last_login = ? WHERE id = ?");
	$smh->execute($currentDate, $id);
}

header("Location: ../charhome.php");
exit;
?>