<b>This is the top menu</b>

<form action="levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="home" />
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

<form action="bank.php">
<input type="submit" value="Bank"/>
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
$playerName=mysql_result($result, 0, "name");
$playerLevel=mysql_result($result, 0, "level");
$playerType=mysql_result($result, 0, "type");
$playerCash=mysql_result($result, 0, "cash");
$playerStamina=mysql_result($result, 0, "stamina");
$playerHealth=mysql_result($result, 0, "health");
$playerEnergy=mysql_result($result, 0, "energy");
$playerStaminaMax=mysql_result($result, 0, "stamina_max");
$playerHealthMax=mysql_result($result, 0, "health_max");
$playerEnergyMax=mysql_result($result, 0, "energy_max");
//$playerExpToNextLevel
?>
Name: <?php echo $playerName;?>  
Level: <?php echo $playerLevel;?> 
Cash: <?php echo $playerCash;?> 
Stamina: <?php echo $playerStamina;?>/<?php echo $playerStaminaMax;?> 
Health: <?php echo $playerHealth;?>/<?php echo $playerHealthMax;?> 
Energy: <?php echo $playerEnergy;?>/<?php echo $playerEnergyMax;?> 
<?php 
mysql_close();  
?>
<br><br>
-----------------------------------------------------
<br>