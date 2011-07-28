<b>This is the top menu</b>
<form action="charhome.php"> 
<input type="submit" value="Home" />
</form>

<form action="levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="mission" />
<input type="submit" value="Choose Mission" />
</form>

<form action="levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="battle" />
<input type="submit" value="Battle" />
</form>

<form action="preferences.php"> 
<input type="submit" value="Preferences" />
</form>

<form action="levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="shop" />
<input type="submit" value="Shop" />
</form>

<form action="playeritemlist.php"> 
<input type="submit" value="My Items" />
</form>

<form action="levelchecker.php" method="post">
<input type="hidden" name="pageRequestType" value="recruit" />
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