<html>

<head>
</head>

<body>


<?php
if (is_null($_GET['justloggedout']) == false) {
  echo "You have just logged out.";
} else {
  echo "Welcome!";
}
?>

<br><br><br>

<form action="login.php"> 
<input type="submit" value="Login!" />
</form>

<form action="createaccount.php"> 
<input type="submit" value="Create New Account" />
</form>



</body>

</html>