<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/playertypeproperties.php");

session_start();
?>


Choose your specialty:
<br>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/chooseclass.php' method='POST'>
<select name="playertype">
<option value='1'><?php echo ucfirst(getPlayerTypeFromTypeID(1)) ?></option>
<option value='2'><?php echo ucfirst(getPlayerTypeFromTypeID(2)) ?></option>
<option value='3'><?php echo ucfirst(getPlayerTypeFromTypeID(3)) ?></option>
</select>
<input type='submit' value='Choose'/>
<br><br>
</form>