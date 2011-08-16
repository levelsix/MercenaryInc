<html>
<head>
</head>

<body>
<?php 
include("topmenu.php"); 

// Daily bonus check
if (isset($_SESSION['dailyBonus'])) {
	print "Congratulations! Daily bonus: " . $_SESSION['dailyBonus'] . " cash. <br>";
	unset($_SESSION['dailyBonus']);
}
?>
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

<form action="levelchecker.php" method="post">
<input type="hidden" name="pageRequestType" value="profile" />
<input type="submit" value="My Profile" />
</form>
</body>

</html>