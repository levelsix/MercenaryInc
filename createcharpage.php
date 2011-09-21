<html>

<head>
</head>

<body>

<form action="<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/charcreate.php" onsubmit="return validateChar();" method="post"> 
Greetings, young warrior. What will you choose as your mercenary alias?
<br>
<input type="text" name="charname" id="charname"/>
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

