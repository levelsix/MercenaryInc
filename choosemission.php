<html>

<head>
</head>

<body>
<?php 
include("topmenu.php");
include("properties/dbproperties.php");
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
  

$query="SELECT * FROM missions WHERE minlevel <= ". $playerlevel . ";";
$result=mysql_query($query);
$num=mysql_numrows($result);
echo "Name :" . mysql_result($result, 0, "name");
print "  ";
echo "Level: " . mysql_result($result,0,"level");
print "  ";
echo "Type: " . mysql_result($result,0,"type");    
print "  ";
echo "Attack: " . mysql_result($result,0,"attack");    
    
mysql_close(); 


Choose your mission:

?>
</body>

</html>