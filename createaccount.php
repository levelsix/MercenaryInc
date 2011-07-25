<html>

<head>
<script type='text/javascript' src='AJAX/ajax.js'></script>
</head>

<body>

<form action=""  onsubmit="return false;" method="post">
Create Account: 
<br>
Username: 
<input type="text" id="username" name="password"/>
<br>
Password: 
<input type="text" id="password" name="password"/>
<br>
<input type="hidden" name="justcreateduser" value="true"> 
<input type="submit" value="Login" onclick="validateAndCreateAccount();"/>
</form>

</body>






<script>
   
 function validateAndCreateAccount() {
   
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

   checkAndCreate(username, password);

   return true;

 }

   function checkAndCreate(username, password){
     var xmlHttp = getXMLHttp();
     
     xmlHttp.onreadystatechange = function()
       {
       
	 //alert(xmlHttp.readyState + " " + xmlHttp.status);
	 //alert(xmlHttp.responseText);

	 if(xmlHttp.readyState == 4)
	   {
	     CreateAccountCallback(xmlHttp.responseText);
	   }
	 return;
       }
     
     xmlHttp.open("GET", "AJAX/createAccountAJAX.php", true); 
     //xmlHttp.open("GET", "http://www.google.com", true); 
     xmlHttp.send(null);
     
   }

function CreateAccountCallback(response)
{
  if (response == 'success') {
    window.location = "userhome.php";
  } else if (response == 'usertaken') {
    alert("Username already taken, please select a new one");
  } else {
    alert("error");
  }
}




</script>


</html>