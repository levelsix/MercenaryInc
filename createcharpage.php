<html>

<head>
</head>

<body>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/properties/playertypeproperties.php"); ?>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/charcreate.php" onsubmit="return validateChar();" method="post"> 
Greetings, young warrior. What will you choose as your mercenary alias?
<br>
<input type="text" name="charname" id="charname"/>
<br><br>

<!--
Choose your specialty:
<br>
<select name="playertype">
<?php
/*
print ("<option value=" . $playertype1 . ">" . ucfirst($playertype1) . "</option>");
print ("<option value=" . $playertype2 . ">" . ucfirst($playertype2). "</option>");
print ("<option value=" . $playertype3 . ">" . ucfirst($playertype3). "</option>");
*/
?>
</select>
<br><br>
-->

<input type="hidden" name="justmadechar" value="true"> 
<input type="submit" value="Finish!" />
</form>

</body>


<script>
 function validateChar() {
   var charname = document.getElementById('charname').value;

  if (charname.trim() == '') {
    alert('The character needs a name to proceed.');
    return false;
  }

//check to see if user already has char with this name


   return true;
 }
</script>

</html>

