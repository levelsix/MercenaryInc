<?php
include("topmenu.php");
include("properties/dbproperties.php");

$missionsMinLevel = 1;
$battleMinLevel = 3;
$shopMinLevel = 4;
$recruitMinLevel = 6;

mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
session_start();
$query="SELECT * FROM users WHERE id = ". $_SESSION['userID'] . ";";
$result=mysql_query($query);
$level=mysql_result($result,0,"level");
mysql_close();  


$tooLowLevelString = "You need to be at least level ";
if (strcmp($_POST['pageRequestType'], "mission") == 0){
	if ($level >=  $missionsMinLevel) {
		//flush();
		//header("Location: choosemission.php");
		//exit();
		echo "put in page redirect";
	} else {
		echo $tooLowLevelString . $missionsMinLevel . " to do missions";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "battle") == 0){
	if ($level >=  $battleMinLevel) {
		//header("Location: battle.php");
		echo "put in page redirect";
	} else {
		echo $tooLowLevelString . $battleMinLevel . " to battle";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "shop") == 0){
	if ($level >=  $shopMinLevel) {
		//header("Location: shoplist.php");
		echo "put in page redirect";		
	} else {
		echo $tooLowLevelString . $shopMinLevel . " to shop";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
} else if (strcmp($_POST['pageRequestType'], "recruit") == 0){
	if ($level >=  $recruitMinLevel) {
		//header("Location: recruit.php");
		echo "put in page redirect";
	} else {
		echo $tooLowLevelString . $recruitMinLevel . " to recruit";
		print "<br>";
		echo "<insert links to allowable paths here>";
	}
}

?>