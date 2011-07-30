<?php 
include("../properties/dbproperties.php");
include("../properties/playertypeproperties.php");
include("../properties/playerinitproperties.php");

mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

$charname = $_POST['charname'];
$playertype = $_POST['playertype'];
if (strcmp($playertype, $playertype1) == 0) {
	$attack = $playertype1atk;
	$defense = $playertype1def;
}
else if (strcmp($playertype, $playertype2) == 0) {
	$attack = $playertype2atk;
	$defense = $playertype2def;
}
else if (strcmp($playertype, $playertype3) == 0) {
	$attack = $playertype3atk;
	$defense = $playertype3def;
}
	
$query = "INSERT INTO users (name, level, type, attack, defense) VALUES
('". $charname ."', ". $initlevel ." , '" . $playertype . 
"', ". $attack .", " . $defense . ");"; 

mysql_query($query) or die(mysql_error()); 
$justAddedID = mysql_insert_id();
mysql_close();  

session_start();
$_SESSION['userID']=$justAddedID;
header("Location: ../charhome.php");
exit;
?>