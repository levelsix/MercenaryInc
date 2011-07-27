<html>

<head>
</head>

<body>

<form action="userhome.php" onSubmit="return validateAccount();" method="post">
Username: 
<input type="text" id="username" name="password"/>

<br>

Password: 
<input type="text" id="password" name="password"/>

<br>


<input type="hidden" name="justloggedin" value="true"> 
<input type="submit" value="Login"/>

</body>

<script>
   
 function validateAccount() {
   
   var username = document.getElementById('username').value;
   var password = document.getElementById('password').value;
   
   if (username.trim() == '') {
     alert('You cannot have a blank username.');
     return false;
   }

   if (password.trim() == '') {
     alert('You cannot have a blank password.');
     return false;
   }


   //check if the username exists in db, if not return false
   //check if the usernames password is password (and hash both later)
   //if it doesnt return false
     
   return true;
 }
</script>

</html>