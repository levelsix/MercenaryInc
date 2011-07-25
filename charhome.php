<html>
<head>
</head>


<body>


<?php

$user="ccs108cchan91";
$password="azeodexu";
$database="c_cs108_cchan91";
$server="mysql-user-master.stanford.edu";
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

if (is_null($_POST['justmadechar']) == false) {
  
  $query = "INSERT INTO characters (charname, userid, class) VALUES
       ('". $_POST['charname'] ."', " . "1" . ", '" . $_POST['class']
       . "');"; 

  mysql_query($query) or die(mysql_error()); 

  echo "Congratulations on your first step,  ". $_POST['charname'] . ", for ";
  echo "choosing the way of the " . $_POST['class'] . " warrior. ";
  echo "<br><br>";
  echo "Your initial stats should go here";
  mysql_close();

} else {
  //populate this from database
  echo "Your stats should be here- strength, hp, Rank";
}
?>


<br><br>
<form action="choosemission.php"> 
<input type="submit" value="Choose Mission" />
</form>

<form action="training.php"> 
<input type="submit" value="Train" />
</form>

<form action="challengeschool.php">
<input type="submit" value="Challenge School" />
</form>

<form action="changeproperties.php"> 
<input type="submit" value="Change Alias/Specialty" />
</form>

<!--
<form action="userhome.php"> 
<input type="submit" value="Switch Character!" />
</form>

<form action="menu.php"> 
<input type="hidden" name="justloggedout" value="true"> 
<input type="submit" value="Logout" />
</form>
-->

</body>

</html>