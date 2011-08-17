<html>
<head>
</head>

<body>
<?php 
include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php"); 

// Daily bonus check
if (isset($_SESSION['dailyBonus'])) {
	print "Congratulations! You found " . $_SESSION['dailyBonus'] . " cash. <br>";
	unset($_SESSION['dailyBonus']);
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