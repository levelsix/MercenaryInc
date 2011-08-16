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
include("classes/ConnectionFactory.php");
  
echo "Your stats are: ";
print "<br>";

session_start();

$db = ConnectionFactory::getFactory()->getConnection();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute(array($_SESSION['userID']));

$num = $stmt->rowCount();
if (!($result = $stmt->fetch(PDO::FETCH_ASSOC))) {
	// Redirect to error page
	header("Location: errorpage.html");
	exit;
}

$playerName = $result['name'];
$playerLevel = $result['level'];
$playerType = $result['type'];
$playerCash = $result['cash'];
$playerStamina = $result['stamina'];
$playerHealth = $result['health'];
$playerEnergy = $result['energy'];
$playerStaminaMax = $result['stamina_max'];
$playerHealthMax = $result['health_max'];
$playerEnergyMax = $result['energy_max'];
//$playerExpToNextLevel
?>
Name: <?php echo $playerName;?>  
Level: <?php echo $playerLevel;?> 
Cash: <?php echo $playerCash;?> 
Stamina: <?php echo $playerStamina;?>/<?php echo $playerStaminaMax;?> 
Health: <?php echo $playerHealth;?>/<?php echo $playerHealthMax;?> 
Energy: <?php echo $playerEnergy;?>/<?php echo $playerEnergyMax;?> 
<br><br>
-----------------------------------------------------
<br>