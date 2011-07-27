<b>This is the top menu</b>
<form action="charhome.php"> 
<input type="submit" value="Home" />
</form>

<form action="choosemission.php"> 
<input type="submit" value="Choose Mission" />
</form>

<form action="battle.php"> 
<input type="submit" value="Battle" />
</form>

<form action="preferences.php"> 
<input type="submit" value="Preferences" />
</form>

<form action="shoplist.php"> 
<input type="submit" value="Shop" />
</form>

<form action="playeritemlist.php"> 
<input type="submit" value="My Gear" />
</form>

<form action="recruit.php"> 
<input type="submit" value="Recruit" />
</form>

<?php
include("properties/dbproperties.php");

mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");
  
echo "Your stats are: ";
print "<br>";

session_start();

$query="SELECT * FROM users WHERE id = ". $_SESSION['userID'] . ";";
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
?>
<br><br>
-----------------------------------------------------
<br>