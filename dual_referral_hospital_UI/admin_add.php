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


//for transactions

$transactions_query="select res2.*, Doctor_name as doc_ref_id_Doctor_name,Doctor_mobile_number as doc_ref_id_Doctor_mobile_number from (select res1.*,Doctor_name as reg_by_doc_Doctor_name,Doctor_mobile_number as reg_by_doc_Doctor_mobile_number from (SELECT Patient_Name,Patient_mobile_number,Reg_by_doc,doc_ref_id FROM patient_stub)as res1 inner join doctor_stub ds on res1.Reg_by_doc=ds.Doctor_serial_id)as res2 inner join doctor_stub sd on res2.doc_ref_id=sd.Doctor_serial_id ";

$result_transactions=mysql_query($transactions_query);
$count_result_transactions=mysql_num_rows($result_transactions);


$refer_out_query="select * from doctor_stub where type_value!='hospital' and type_value!='hospital_user' and type_value!='hospital_user_all'";
$result_refer_out_query=mysql_query($refer_out_query);
$count_refer_out_query=mysql_num_rows($result_refer_out_query);


$hospital_query="select temp.*,hs.hospital_name from (select * from doctor_stub where type_value ='hospital_user' or type_value ='hospital_user_all') as temp inner join hospital_stub hs on temp.Doctor_yxp=hs.hospital_id";
$result_hospital_query=mysql_query($hospital_query);
$count_hospital_query=mysql_num_rows($result_hospital_query);



?>
<html>
<head>
<script src="tabcontent.js" type="text/javascript"></script>
<script src="javascript/doc_reg.js" type="text/Javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="tab-content/template2/tabcontent.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="css/style.css">
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
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

function sendBroadcast(){

  var message=document.getElementById('spec_msg').value;
  if(message==""){
     alert("Please enter the message to broadcast");
	 return false;
  
  }
  
  $.ajax({
     method:'POST',
	 url: "admin_broadcast.php",
	 data:{"msg":message},
	 type: "json",
	 success: function(msg){
	   if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	 }
 });
}

function delete_user(id){
	alert("Development in progress");
}

</script>

</head>
<title>
 Add Hospital-Referralio
</title>
<body>
  
  <!-- modification started-->
  
    <div align="right">
	     <a href="logout.php" style="text-decoration:none"><input type="button" name="logout" value="Logout"/></a>
	</div>
    <div align="center">
	  <div>
		<h2>ADMIN PANEL</h2>
	  </div>
	  
	  <div>
	    <h3><?php echo $hospital_name;?></h3>
			  <div>Time-
				  <?php 
				  date_default_timezone_set('Asia/Kolkata');
				  echo date('Y-m-d H:i:s') ;?>
				  
			  </div>
	  </div>
			  	
	</div>
	
    <div style="width: auto; margin: 0 auto; padding: 90px 0 40px;">
        <ul class="tabs" data-persist="true">
            <li><a href="#view1">Add Hospital</a></li>
            <li><a href="#view2">All Hospitals</a></li>
            <li><a href="#view3">All Transactions</a></li>
			<li><a href="#view5">All Registered/Unregistered Doctors</a></li>
			<li><a href="#view6">All Hospital/Super Hospital Users</a></li>
			<li><a href="#view7">Broadcast Message</a></li>
			<li><a href="#view8">Personal Broadcast Message</a></li>
			
        </ul>
        <div class="tabcontents">
            
			<div id="view1">
			  <div id="hos_register" align="center">
			       <b>ADD HOSPITAL</b>
                   <br/>
                  <table>
					 <tr><td>Hospital Name:</td><td><input type="text" name="hospital_name" id="hospital_name" value=""/></td></tr>
					 <tr><td>Hospital Location:</td><td><input type="text" name="hospital_loc" id="hospital_loc" value=""/></td></tr>
					 <tr><td>Hospital Username</td><td><input type="text" name="hospital_user" id="hospital_user" value=""/></td></tr>
					 <tr><td>Hospital Password</td><td><input type="text" name="hospital_pass" id="hospital_pass" value=""/></td></tr>
				  </table>
				  <br/>
				  <input type="button" name="add_hos" id="add_hos" value="ADD HOSPITAL" onclick="add_hos()"/>
			  </div>

              <div id="Result-panel-view1">
			     <!--This div is to display the div1 resultant table-->
              </div>			  
            </div>
            <div id="view2">
			  <div align="center">
			      <b>All Hospitals</b>
                  <br/>
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
						  <td><?php echo "**********"; ?></td>
						  <td><?php echo $row['hospital_registered_time']; ?></td>
					   </tr>
					   <?php }
					   } 
					   ?>  
					</table>
			  </div>
			  <div id="Result-panel-view2">
			     <!--This div is to display the div2 resultant table-->
              </div>
                
                  				  
            </div>
            <div id="view3" align="center">
                <p align="center"><b>Transactions</b> <p> 
				<span><b>Search</b><input type="text" name="search_tech" id="search_tech" value="" /></span><!--onkeyup="search_now()" -->
				<?php
				 if($count_result_transactions!=0){
				 ?>
				  <table border="0">
				     <tr>
					   <th>Patient Name</th>
					   <th>Patient Mobile Number</th>
					   <th>Reffering Doctor Name</th>
					   <th>Reffering Doctor Mobile</th>
					   <th>Referred Doctor Name</th>
					   <th>Referred Doctor Mobile</th>
					   
					 <tr>
					 <?php
					  while ($row=mysql_fetch_assoc($result_transactions)){
					  ?>
					   <tr>
					     <td><?php echo $row['Patient_Name'];?></td>
						 <td><?php echo $row['Patient_mobile_number'];?></td>
						 <td><?php echo $row['reg_by_doc_Doctor_name'];?></td>
						 <td><?php echo $row['reg_by_doc_Doctor_mobile_number'];?></td>
						 <td><?php echo $row['doc_ref_id_Doctor_name'];?></td>
						 <td><?php echo $row['doc_ref_id_Doctor_mobile_number'];?></td>
						 
					   </tr>
					   
					 <?php   
					  }
					   
					 
					  ?>
				  </table>
				  <?php
				 }else{
				   echo "No Transactions Available Yet";
				 }
				?>
                
            </div>
			
			
			<div id="view5">
                <p align="center"><b>Doctors</b> <p> 
				
				<?php
				 if($count_refer_out_query!=0){
				 ?>
				  <table border="0" align="center">
				     <tr>
					   <th>Doctor Name</th>
					   <th>Doctor Email</th>
					   <th>Doctor Mobile Number</th>
					   <th>Doctor Specialization</th>
					   <th>Doctor experience(yrs)</th>
					   <th>Doctor Qualification</th>
					   <th>Control</th>
					 <tr>
					 <?php
					  while ($row=mysql_fetch_assoc($result_refer_out_query)){
					  ?>
					   <tr>
					     <td><?php echo $row['Doctor_name'];?></td>
						 <td><?php echo $row['Doctor_email'];?></td>
						 <td><?php echo $row['Doctor_mobile_number'];?></td>
						 <td><?php echo $row['Doctor_specialization'];?></td>
						 <td><?php echo $row['Doctor_yxp'];?></td>
						 <td><?php echo $row['Doctor_qualification'];?></td>
						 <td><input type='button' id="button_2" value="Delete" onclick="delete_user(<?php echo $row['Doctor_serial_id'];?>)"/>
						 </td>
						 
					   </tr>
					   
					 <?php   
					  }
					   
					 
					  ?>
				  </table>
				  <?php
				 }else{
				   echo "No Refer Out Available Yet";
				 }
				?>
                
            </div>
            
            <div id="view6">
                <p align="center"><b>Hospital Users</b> <p> 
				
				<?php
				 if($count_hospital_query!=0){
				 ?>
				  <table border="0" align="center">
				     <tr>
					   <th>User Name</th>
					   <th>User Email</th>
					   <th>User Mobile Number</th>
					   <th>Hospital Name</th>
					   <th>Type</th>
					   <th>Control</th>
					 <tr>
					 <?php
					  while ($row=mysql_fetch_assoc($result_hospital_query)){
					  ?>
					   <tr>
					     <td><?php echo $row['Doctor_name'];?></td>
						 <td><?php echo $row['Doctor_email'];?></td>
						 <td><?php echo $row['Doctor_mobile_number'];?></td>
						 <td><?php echo $row['hospital_name'];?></td>
						 <td><?php if($row['type_value']=='hospital_user'){ echo 'Hospital User'; }else{ echo 'Hospital Super User'; }?></td>
						 <td><input type='button' id="button_2" value="Delete" onclick="delete_user(<?php echo $row['Doctor_serial_id'];?>)"/>
						 </td>
						 
					   </tr>
					   
					 <?php   
					  }
					   
					 
					  ?>
				  </table>
				  <?php
				 }else{
				   echo "No Refer Out Available Yet";
				 }
				?>
                
            </div>
			
			
			
			<div id="view7">
                <p align="center"><b>Broadcast Messages</b> <p> 
				
				<div>
				  <h3> Message to registerd doctor</h3>
				    <textarea name="spec_msg" id="spec_msg" value=""></textarea>
					<br/>
					
                    
					<input type="button" name="send" value="Send" onClick="sendBroadcast()"/>
					
					
					
				</div>
                
            </div>
			
			<div id="view8">
			   <p align="center"><b>Broadcast Messages Personally</b> <p> 
				
				<div>
				  <h3> Message to a specific doctor</h3>
				    <textarea name="spec_msg1" id="spec_msg1" value=""></textarea>
					<br/>
					<?php
					
					$refer_out_query="select * from doctor_stub where type_value != 'hospital'";
					$result_refer_out_query=mysql_query($refer_out_query);
					$count_refer_out_query=mysql_num_rows($result_refer_out_query);
					
					//$query_broadcast_message=mysql_query("select * from doc_broadcast_msg where hospital_id_author='$hospital_id'");
					
					//$count_broadcast=mysql_num_rows($query_broadcast_message);
					
					?>
					<br/>
					<b>Doctor Selection:</b>
					
					<select name="doc_identifier1" id="doc_identifier1">
					<option value="Selected">Selected</option>
					<?php while($row_refer_out_doc=mysql_fetch_assoc($result_refer_out_query)){?>
					   <option value="<?php echo $row_refer_out_doc['Doctor_serial_id'];?>"><?php echo $row_refer_out_doc['Doctor_name'];?></option>
					<?php } ?>
					</select>
                    
					<input type="button" name="send" value="Send" onClick="sendB2B_personal()"/>
					
					
					
				</div>
			</div>
			
        </div>
    </div>
  
  
  
</body>
</html>