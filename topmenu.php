<b>This is the top menu</b>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="home" />
<input type="submit" value="Home" />
</form>


<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="mission" />
<input type="submit" value="Choose Mission" />
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="battle" />
<input type="submit" value="Battle" />
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/preferences.php"> 
<input type="submit" value="Preferences" />
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post"> 
<input type="hidden" name="pageRequestType" value="shopitem" />
<input type="submit" value="Shop" />
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/playeritemlist.php"> 
<input type="submit" value="My Items" />
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/bank.php">
<input type="submit" value="Bank"/>
</form>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post">
<input type="hidden" name="pageRequestType" value="recruit" />
<input type="submit" value="Recruit" />
</form>


<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");

echo "Your stats are: ";
print "<br>";

session_start();
$user = User::getUser($_SESSION['userID']);

if (!$user) {
	// Redirect to error page
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$playerName = $user->getName();
$playerLevel = $user->getLevel();
$playerType = $user->getType();
$playerCash = $user->getCash();
$playerStamina = $user->getStamina();
$playerHealth = $user->getHealth();
$playerEnergy = $user->getEnergy();
$playerStaminaMax = $user->getStaminaMax();
$playerHealthMax = $user->getHealthMax();
$playerEnergyMax = $user->getEnergyMax();
$playerExp = $user->getExperience();

//$playerExpToNextLevel
?>
Name: <?php echo $playerName;?>  
Level: <?php echo $playerLevel;?> 
Cash: <?php echo $playerCash;?> 
Stamina: <?php echo $playerStamina;?>/<?php echo $playerStaminaMax;?> 
Health: <?php echo $playerHealth;?>/<?php echo $playerHealthMax;?> 
Energy: <?php echo $playerEnergy;?>/<?php echo $playerEnergyMax;?> 
Experience: <?php echo $playerExp; ?>
<br><br>
-----------------------------------------------------
<br>