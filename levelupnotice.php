<b>LEVEL UP!</b> <br>
Congratulations! You are now level <?php echo $newLevel ?>. <br>
You gained <?php echo $skillPointsGained ?> skill points. <br>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/skills.php" method="GET">
<input type="submit" value="Spend Skill Points"/>
</form>