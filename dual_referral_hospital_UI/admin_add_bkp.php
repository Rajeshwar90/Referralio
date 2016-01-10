<?php
session_start();

if(!isset($_SESSION['Valid_Session'])){
  //redirect to login url in PHP
  header("Location: admin_main_panel.php");
  die();
}
include 'db_conn.php';

$query_gethospitals=mysql_query("select * from hospital_stub");
$count_hospitals=mysql_num_rows($query_gethospitals);


?>
<html>
<head>
<style type="text/css">
table {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;	
}
table th {
    padding: 8px;
	background-color: #dedede;
}
table td {
	padding: 8px;
	background-color: #ffffff;
}
</style>
<script type="text/Javascript" src="js/jquery.js"/></script>
<script type="text/Javascript">
function add_hos(){
 var hospital_name=document.getElementById('hospital_name').value;
 var hospital_loc=document.getElementById('hospital_loc').value;
 var hospital_user=document.getElementById('hospital_user').value;
 var hospital_pass=document.getElementById('hospital_pass').value;
 
 if(hospital_name==""){
   alert("Plase enter the hospital name");
   return false;
 }
 
 if(hospital_loc==""){
   alert("Please enter the hospital location");
   return false;
 }
 
 if(hospital_user==""){
   alert("Please enter the hospital username");
   return false;
 }
 
 if(hospital_pass==""){
   alert("Please enter the hospital password");
   return false;
 }
 
 $.ajax({
     method:'POST',
	 url: "hospital_registration.php",
	 data:{"hospital_name":hospital_name,"hospital_location":hospital_loc,"hospital_username":hospital_user,"hospital_password":hospital_pass},
	 type: "json",
	 success: function(msg){
	   //alert(msg);
	   
	   var obj=jQuery.parseJSON(msg);
	   var html="";
	   if(obj.status=='Success'){
	     alert("Hospital added successfully");
		 window.location.href="admin_add.php";
		 
	   }
	   else
	   {
	     alert("Hospital could not be added.Please try again after sometime");
		 return false;
	   }
	 }
 });
 
}
</script>

</head>
<title>
 Add Hospital-Referralio
</title>
<body>
  <div align="right">
    <a href="logout.php" style="text-decoration:none"><input type="button" name="logout" value="Logout"/></a>
  </div>
  
  <div>
  <div align="center">
    <h2>Add Hospital</h2>
	<table>
		 <tr><td>Hospital Name:</td><td><input type="text" name="hospital_name" id="hospital_name" value=""/></td></tr>
		 <tr><td>Hospital Location:</td><td><input type="text" name="hospital_loc" id="hospital_loc" value=""/></td></tr>
		 <tr><td>Hospital Username</td><td><input type="text" name="hospital_user" id="hospital_user" value=""/></td></tr>
		 <tr><td>Hospital Password</td><td><input type="text" name="hospital_pass" id="hospital_pass" value=""/></td></tr>
	 </table>
	 <br/>
	 <input type="button" name="add_hos" id="add_hos" value="ADD HOSPITAL" onclick="add_hos()"/>
  </div>
  <br/>
  <br/>
  <div align="center">
    <p align="center"> <h2>Hospital List </h2></p>
    <table>
	   <tr>
	      <th>Hospital Name</th>
		  <th>Hospital Location</th>
		  <th>Hospital Username</th>
		  <th>Hospital Password</th>
		  <th>Register Time</th>
	   </tr>
	   <?php
	     if($count_hospitals==0){?>
		  <h3> No Hospitals added Yet</h3>
		<?php }
		else{
	      while($row=mysql_fetch_assoc($query_gethospitals)){
	   ?>
	   <tr>
	      <td><?php echo $row['hospital_name']; ?></td>
		  <td><?php echo $row['hospital_location']; ?></td>
		  <td><?php echo $row['hospital_username']; ?></td>
		  <td><?php echo $row['hospital_password']; ?></td>
		  <td><?php echo $row['hospital_registered_time']; ?></td>
	   </tr>
       <?php }
	   } 
	   ?>  
	</table>
  </div>
  
  
  
  
</body>
</html>