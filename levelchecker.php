<?php
include("topmenu.php");

$missionsMinLevel = 1;
$homeMinLevel = 2;
$battleMinLevel = 3;
$shopMinLevel = 4;
$profileMinLevel = 5;
$recruitMinLevel = 6;

session_start();

$stmt = $db->prepare("SELECT level FROM users WHERE id = ?");
$stmt->execute(array($_SESSION['userID']));

$level = 0;
if ($result = $stmt->fetch(PDO::FETCH_ASSOC))
	$level = $result['level'];


$tooLowLevelString = "You need to be at least level ";
if (strcmp($_POST['pageRequestType'], "mission") == 0){
	if ($level >=  $missionsMinLevel) {
		echo "<script>location.href='choosemission.php'</script>";
	} else {
		echo $tooLowLevelString . $missionsMinLevel . " to do missions";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "battle") == 0){
	if ($level >=  $battleMinLevel) {
		echo "<script>location.href='battle.php'</script>";
	} else {
		echo $tooLowLevelString . $battleMinLevel . " to battle";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "shop") == 0){
	if ($level >=  $shopMinLevel) {
		echo "<script>location.href='shoplist.php'</script>";
	} else {
		echo $tooLowLevelString . $shopMinLevel . " to shop";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "recruit") == 0){
	if ($level >=  $recruitMinLevel) {
		echo "<script>location.href='recruit.php'</script>";
	} else {
		echo $tooLowLevelString . $recruitMinLevel . " to recruit";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "home") == 0){
	if ($level >=  $homeMinLevel) {
		echo "<script>location.href='charhome.php'</script>";
	} else {
		echo $tooLowLevelString . $homeMinLevel . " to get to home";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "profile") == 0){
	if ($level >=  $profileMinLevel) {
		echo "<script>location.href='charprofile.php'</script>";
	} else {
		echo $tooLowLevelString . $profileMinLevel . " to get to profile";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
}

?>