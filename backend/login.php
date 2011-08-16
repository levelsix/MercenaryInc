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
$db = ConnectionFactory::getFactory()->getConnection();

// Get last login time
$stmt = $db->prepare("SELECT last_login FROM users WHERE id = ?");
$stmt->execute(array($id));
$result = $stmt->fetch(PDO::FETCH_ASSOC);
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
		$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = CURRENT_TIMESTAMP WHERE id = ?");
		$stmt->execute(array($dailyBonusAmount, $id));
		*/
		$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = ? WHERE id = ?");
		$stmt->execute(array($dailyBonusAmount, $currentDate, $id));
		$_SESSION['dailyBonus'] = $dailyBonusAmount;
	} else {
		// Update last_login in database
		//$stmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
		//$stmt->execute(array($id));
		$stmt = $db->prepare("UPDATE users SET last_login = ? WHERE id = ?");
		$stmt->execute($currentDate, $id);
	}
} else {
	// Update last_login in database
	//$stmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
	//$stmt->execute(array($id));
	$stmt = $db->prepare("UPDATE users SET last_login = ? WHERE id = ?");
	$stmt->execute($currentDate, $id);
}

header("Location: ../charhome.php");
exit;
?>