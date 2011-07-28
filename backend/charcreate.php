<?php 
include("../properties/dbproperties.php");
include("../properties/playertypeproperties.php");
include("../properties/playerinitproperties.php");

mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

$charname = $_POST['charname'];
$playertype = $_POST['playertype'];
if (strcmp($playertype, $type1) == 0) {
	$attack = $type1atk;
	$defense = $type1def;
}
else if (strcmp($playertype, $type2) == 0) {
	$attack = $type2atk;
	$defense = $type2def;
}
else if (strcmp($playertype, $type3) == 0) {
	$attack = $type3atk;
	$defense = $type3def;
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