<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/serverproperties.php");

$playerStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$playerStmt->execute(array($_SESSION['userID']));

$playerResult = $playerStmt->fetch(PDO::FETCH_ASSOC);
if (!$playerResult) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}

$playerSkill = $playerResult["skill_points"];
$playerAttack = $playerResult["attack"];
$playerDefense = $playerResult["defense"];
?>


You have <?php echo $playerSkill;?> skill points remaining.
<br><br>


Attack <?php echo $playerAttack;
if ($playerSkill >= 1) { 
?>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/useskill.php' method='post'>
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/useskill.php' method='post'>
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/useskill.php' method='post'>
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
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='healthmax' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
	print "<br>Insufficient Skill Points<br><br>";
}?>

Max Stamina (takes 2 skill points to gain 1) <?php echo $playerStaminaMax;
if ($playerSkill >= 2) { 
?>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/useskill.php' method='post'>
<input type='hidden' name='attributeToIncrease' value='staminamax' />
<input type='submit' value='Increase' />
</form>
<?php 
} else {
	print "<br>Insufficient Skill Points<br><br>";
}?>