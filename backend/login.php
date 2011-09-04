<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

function updateLogin($db, $userID, $loginTime) {
	// Update last_login in database
	//$stmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
	//$stmt->execute(array($userID));
	$stmt = $db->prepare("UPDATE users SET last_login = ? WHERE id = ?");
	if (!$stmt->execute(array($loginTime, $userID))) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function updateUserCashAndLogin($db, $userID, $cashAmount, $loginTime) {
	// Give daily bonus and update last_login
	/*
	$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = CURRENT_TIMESTAMP WHERE id = ?");
	$stmt->execute(array($cashAmount, $userID));
	*/
	$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = ? WHERE id = ?");
	if (!$stmt->execute(array($cashAmount, $loginTime, $userID))) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function updateUserCashLoginAndNumConsec($db, $userID, $cashAmount, $loginTime, $numConsecDays) {
	// Give daily bonus and update last_login
	/*
	$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = CURRENT_TIMESTAMP, num_consecutive_days_played = ? WHERE id = ?");
	$stmt->execute(array($cashAmount, $numConsecDays, $userID));
	*/
	$stmt = $db->prepare("UPDATE users SET cash = cash + ?, last_login = ?, num_consecutive_days_played = ? WHERE id = ?");
	if (!$stmt->execute(array($cashAmount, $loginTime, $numConsecDays, $userID))) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function updateLoginAndNumConsec($db, $userID, $loginTime, $numConsecDays) {
	// Update last_login in database
	//$stmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP, num_consecutive_days_played = ? WHERE id = ?");
	//$stmt->execute(array($numConsecDays, $userID));
	$stmt = $db->prepare("UPDATE users SET last_login = ?, num_consecutive_days_played = ? WHERE id = ?");
	if (!$stmt->execute(array($loginTime, $numConsecDays, $userID))) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function updateUserItems($db, $userID, $itemID) {
	// First check if the user has the item already
	$itemCheckStmt = $db->prepare("SELECT * FROM users_items WHERE user_id = ? AND item_id = ?");
	$itemCheckStmt->execute(array($userID, $itemID));
	
	$numRows = $itemCheckStmt->rowCount();
	
	if ($numRows == 0) { // Does not yet have the item
		$updateString = "INSERT INTO users_items (user_id, item_id, quantity) VALUES (?, ?, 1)";
		$params = array($userID, $itemID);
	} else { // Already has the item
		$updateString = "UPDATE users_items SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?";
		$params = array($userID, $itemID);
	}
	
	$stmt = $db->prepare($updateString);
	if (!$stmt->execute($params)) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function updateLatest($db, $userID, $cashAmount, $numConsecDays) {
	switch($numConsecDays) {
		case 1:
			$updateString = "UPDATE users_dailybonuses SET day2 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
		case 2: 
			$updateString = "UPDATE users_dailybonuses SET day3 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
		case 3:
			$updateString = "UPDATE users_dailybonuses SET day4 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
		case 4:
			$updateString = "UPDATE users_dailybonuses SET day5 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
		case 5:
			$updateString = "UPDATE users_dailybonuses SET day6 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
		case 6: // Clear the row of daily bonuses, might only need to reset day1 = 0
			$updateString = "UPDATE users_dailybonuses SET day1 = 0, day2 = 0, day3 = 0, day4 = 0, day5 = 0, day6 = 0 WHERE user_id = ?";
			$params = array($userID);
			break;
		case 0:
		default:
			$updateString = "UPDATE users_dailybonuses SET day1 = ? WHERE user_id = ?";
			$params = array($cashAmount, $userID);
			break;
	}
	
	$updateStmt = $db->prepare($updateString);
	if (!$updateStmt->execute($params)) {
		header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
		exit;
	}
}

function getWeeklyBonusItemID($db) {
	return 1;
}

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
$stmt = $db->prepare("SELECT last_login, num_consecutive_days_played FROM users WHERE id = ?");
$stmt->execute(array($id));
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$lastLogin = $result['last_login'];

$currentDate = date('Y-m-d H:i:s');
//$dailyBonusDate = date('Y-m-d') . " 08:00:00";
$dailyBonusDate = date('Y-m-d') . " 00:00:00";

if (strcmp($currentDate, $dailyBonusDate) >= 0) {
	// <= or < here?
	if (strcmp($lastLogin, $dailyBonusDate) < 0) {
		$dailyBonusAmount = 1000;
		
		// Daily bonus over the week and weekly bonus
		$pastDailyBonusesStmt = $db->prepare("SELECT * FROM users_dailybonuses WHERE user_id = ?");
		$pastDailyBonusesStmt->execute(array($id));
		
		$numRows = $pastDailyBonusesStmt->rowCount();
		// No daily bonuses for this user yet
		if ($numRows == 0) {
			$dailyBonusInsertStmt = $db->prepare("INSERT INTO users_dailybonuses (user_id, day1) VALUES (?, ?)");
			if (!$dailyBonusInsertStmt->execute(array($id, $dailyBonusAmount))) {
				header("Location: ." . $GLOBALS['serverRoot'] . "/errorpage.html");
				exit;
			}
			
			updateUserCashLoginAndNumConsec($db, $id, $dailyBonusAmount, $currentDate, 1);
			$_SESSION['dailyBonus'] = $dailyBonusAmount;
		} else { // Update daily bonuses for this user
			$numConsecDays = $result['num_consecutive_days_played'];
			
			// Fetch the daily bonuses and pass it into the session to display
			$_SESSION['allPastBonuses'] = $pastDailyBonusesStmt->fetch(PDO::FETCH_ASSOC);
			unset($_SESSION['allPastBonuses']['user_id']); // Don't pass along the user ID
			
			updateLatest($db, $id, $dailyBonusAmount, $numConsecDays);
						
			if ($numConsecDays == 6) { // weekly bonus
				$weeklyBonusItemID = getWeeklyBonusItemID($db);
				
				//updateUserItems($db, $id, $weeklyBonusItemID);
				updateLoginAndNumConsec($db, $id, $currentDate, 0);
				
				$_SESSION['weeklyBonus'] = $weeklyBonusItemID;
			} else { // cash bonus only
				updateUserCashLoginAndNumConsec($db, $id, $dailyBonusAmount, $currentDate, $numConsecDays + 1);
				$_SESSION['dailyBonus'] = $dailyBonusAmount;
			}
		}
	} else {
		updateLogin($db, $id, $currentDate);
	}
} else {
	updateLogin($db, $id, $currentDate);
}

header("Location: $serverRoot/charhome.php");
exit;
?>