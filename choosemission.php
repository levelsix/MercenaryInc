<html>

<head>
</head>

<body>
<?php 
include("topmenu.php");
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
  
$query="SELECT * FROM missions WHERE min_level <= ". $playerlevel . ";";
$result=mysql_query($query);
$num=mysql_numrows($result);


if ($num == 0) { 
	echo "No missions available";
} else {

	
	print "Choose your mission: <br><br>";
	$i = 0;
	while ($i < $num) {
		print "Title: " . mysql_result($result,$i,"name") . "<br>";
		print "Description: " . mysql_result($result,$i,"description") . "<br>";
		print "Cost: " . mysql_result($result,$i,"energy_cost") . " energy<br>";
		print "Will Gain: " . mysql_result($result,$i,"exp_gained") . " exp<br>";
		print "Will Gain " . mysql_result($result,$i,"min_gold_gained") . " - ";
		print mysql_result($result, $i, "max_gold_gained") . "<br>";
		print "Chance of getting loot: " . mysql_result($result,$i,"chance_of_loot") . "<br>";
		
		$item_id = mysql_result($result,$i,"item_id");
		$query="SELECT * FROM items WHERE id = ". $item_id . ";";
		$itemresult=mysql_query($query);
		print "You're not supposed to know this but the item you might get is the ";
		print mysql_result($itemresult, 0, "name");
		
		print "<br><br>";
		
		$i++;
	}
	
}


mysql_close(); 

?>
</body>

</html>