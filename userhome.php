<html>

<head>
</head>

<body>


<?php
if (is_null($_POST['justcreateduser']) == false) {
  echo "You have successfully created an account.";
} else {
  echo "Welcome!";
}
?>

<br>

 //if the user has characters then include this button
<br>
<form action="charhome.php"> 
   //dropdown list of this users characters
<br>
<input type="submit" value="Select Character!" />
</form>

<form action="createchar.php"> 
<input type="submit" value="Create New Character!" />
</form>

<form action="menu.php"> 
<input type="hidden" name="justloggedout" value="true"> 
<input type="submit" value="Logout" />
</form>



</body>

</html>