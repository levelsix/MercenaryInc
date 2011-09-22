<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");

$missionsMinLevel = 1;
$homeMinLevel = 2;
$battleMinLevel = 3;
$shopItemMinLevel = 4;
$shopREMinLevel = 4;
$profileMinLevel = 5;
$hospitalMinLevel = 5;
$recruitMinLevel = 6;

session_start();

$level = $playerLevel;

//TODO: refactor js to have absolute paths
$tooLowLevelString = "You need to be at least level ";
if ($_POST['pageRequestType'] == "mission"){
	if ($level >=  $missionsMinLevel) {
		echo "<script>location.href='$serverRoot/choosemission.php'</script>";
	} else {
		echo $tooLowLevelString . $missionsMinLevel . " to do missions";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "battle"){
	if ($level >=  $battleMinLevel) {
		echo "<script>location.href='$serverRoot/battle.php'</script>";
	} else {
		echo $tooLowLevelString . $battleMinLevel . " to battle";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "shopitem"){
	if ($level >=  $shoItemMinLevel) {
		echo "<script>location.href='$serverRoot/shopitemlist.php'</script>";
	} else {
		echo $tooLowLevelString . $shopItemMinLevel . " to shop";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "shoprealestate"){
	if ($level >=  $shopREMinLevel) {
		echo "<script>location.href='$serverRoot/shoprealestatelist.php'</script>";
	} else {
		echo $tooLowLevelString . $shopREMinLevel . " to shop";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "recruit"){
	if ($level >=  $recruitMinLevel) {
		echo "<script>location.href='$serverRoot/recruit.php'</script>";
	} else {
		echo $tooLowLevelString . $recruitMinLevel . " to recruit";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "home"){
	if ($level >=  $homeMinLevel) {
		echo "<script>location.href='$serverRoot/charhome.php'</script>";
	} else {
		echo $tooLowLevelString . $homeMinLevel . " to get to home";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "profile"){
	if ($level >=  $profileMinLevel) {
		echo "<script>location.href='$serverRoot/charprofile.php'</script>";
	} else {
		echo $tooLowLevelString . $profileMinLevel . " to get to profile";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if ($_POST['pageRequestType'] == "hospital"){
	if ($level >=  $hospitalMinLevel) {
		echo "<script>location.href='$serverRoot/hospital.php'</script>";
	} else {
		echo $tooLowLevelString . $hospitalMinLevel . " to get to hospital";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
}
?>