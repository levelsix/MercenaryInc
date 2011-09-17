<html>
<head>
</head>

<body>
<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php"); 
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");

// Daily and weekly bonus check
if (isset($_SESSION['allPastBonuses'])) {
	$i = 1;
	foreach($_SESSION['allPastBonuses'] as $value) {
		if ($value == 0) break;
		print "Day $i Bonus: $value cash <br>";
		$i++;
	}
	unset($_SESSION['allPastBonuses']);
}

if (isset($_SESSION['dailyBonus'])) {
	print "Congratulations! You found " . $_SESSION['dailyBonus'] . " cash. <br>";
	unset($_SESSION['dailyBonus']);
} else if (isset($_SESSION['weeklyBonus'])) {
	$item = Item::getItem($_SESSION['weeklyBonus']);
	
	print "Congratulations! For playing the last 7 days, you received one ". $item->getName() ."! <br>";
	unset($_SESSION['weeklyBonus']);
}
?>
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
<input type="hidden" name="pageRequestType" value="shop" />
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

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/levelchecker.php" method="post">
<input type="hidden" name="pageRequestType" value="profile" />
<input type="submit" value="My Profile" />
</form>
</body>

</html>