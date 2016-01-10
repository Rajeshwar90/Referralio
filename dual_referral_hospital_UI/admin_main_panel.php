<?php
session_start();


?>
<html>
<head>
<style>
.header{
  
}
</style>
<script type="text/Javascript" src="js/jquery.js"/></script>
<script type="text/Javascript">
 function call_login(){

   var user=document.getElementById('admin_username').value;
   var pass=document.getElementById('admin_password').value;
   
   if(user=="" || pass==""){
     alert("Please enter the credentials to login");
	 return false;
   }
   
   $.ajax({
     method:'POST',
	 url: "admin_panel_backend.php",
	 data:{"user1":user,"pass1":pass},
	 type: "json",
	 success: function(msg){
	   //alert(msg);
	   
	   var obj=jQuery.parseJSON(msg);
	   if(obj.status=='Success'){
	     
	     window.location.href="admin_add.php";
	   }
	   else
	   {
	     alert("Invalid Credentials");
		 return false;
	   }
	 }
   });
 
 }
</script>
</head>
<title>
</title>
<body>
  <div id="header" class="header" align="center">
     <h1>Referralio Admin Panel Login</h1>
  </div>
  <br/>
  <br/>
  <div>
	<div align="center">
	  <h2>Admin Login</h2>
	</div>
	<div align='center'>
	  <table>
	  <tr><td><b>Username:</b></td><td><input type="text" name="admin_username" id="admin_username" value=""/></td></tr>
	  <br/>
	  <tr><td><b>Password:</b></td><td><input type="password" name="admin_password" id="admin_password" value=""/></td></tr>
	  </table>
	</div>
	<br/>
	
	<div align="center">
	  <input type="button" name="Login" value="Login" onClick="call_login()"/>
	</div>
  </div>
</body>
</html>