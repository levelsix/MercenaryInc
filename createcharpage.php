<html>

<head>
</head>

<body>
<?php include("properties/playertypeproperties.php"); ?>
<?php include("properties/dbproperties.php"); ?>



<form action="backend/charcreate.php" onsubmit="return validateChar();" method="post"> 
Greetings, young warrior. What will you choose as your mercenary alias?
<br>
<input type="text" name="charname" id="charname"/>
<br><br>

Choose your specialty:
<br>
<select name="playertype">
<?php
print ("<option value=" . $type1 . ">" . ucfirst($type1) . "</option>");
print ("<option value=" . $type2 . ">" . ucfirst($type2). "</option>");
print ("<option value=" . $type3 . ">" . ucfirst($type3). "</option>");
?>
</select>
<br><br>

Choose your school:
<br>
<select name="school">
  <option value="Chrono">Chrono</option>
  <option value="Xenu">Xenu</option>
  <option value="Zinanx">Zinan</option>
</select>
<br><br>




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

