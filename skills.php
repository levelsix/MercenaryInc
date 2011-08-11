<?php
include("topmenu.php");

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$playerQuery = "SELECT * FROM users WHERE id = " . $_SESSION['userID'] . ";";
$playerResult = mysql_query($playerQuery);

$playerSkill = mysql_result($playerResult, 0, "skill_points");
$playerAttack = mysql_result($playerResult, 0, "attack");
$playerDefense = mysql_result($playerResult, 0, "defense");
mysql_close();
?>


You have <?php echo $playerSkill;?> skill points remaining.
<br><br>


Attack <?php echo $playerAttack;
if ($playerSkill >= 1) { 
?>
<form action='backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='attack' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
print "<br>Insufficient Skill Points<br><br>";
}?>

Defense <?php echo $playerDefense;
if ($playerSkill >= 1) { 
?>
<form action='backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='defense' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
print "<br>Insufficient Skill Points<br><br>";
}?>

Max Energy <?php echo $playerEnergyMax;
if ($playerSkill >= 1) { 
?>
<form action='backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='energymax' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
print "<br>Insufficient Skill Points<br><br>";
}?>

Max Health (would add 10) <?php echo $playerHealthMax;
if ($playerSkill >= 1) { 
?>
<form action='backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='healthmax' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
print "<br>Insufficient Skill Points<br><br>";
}?>

Max Stamina (takes 2 to plus 1) <?php echo $playerStaminaMax;
if ($playerSkill >= 2) { 
?>
<form action='backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='staminamax' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
print "<br>Insufficient Skill Points<br><br>";
}?>