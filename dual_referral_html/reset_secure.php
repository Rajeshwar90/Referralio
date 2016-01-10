<?php

include 'db_conn.php';

$link=$_GET['link'];
$doctor_id=$_GET['patch_id'];

//$chk_query=mysql_query("select * from doctor_stub where Doctor_serial_id='$doctor_id' and reset_key='$link'");

$query="select * from doctor_stub where Doctor_serial_id='$doctor_id' and reset_key='$link'";
$res=mysql_query($query);
$num=mysql_num_rows($res);

if($num==0)
{
  header('Location:page_expired.php');
  exit;
}
else
{
 $pass1="";
 $query_resetkey="update doctor_stub set reset_key='$pass1' where Doctor_serial_id='$doctor_id'";
 $res_resetkey=mysql_query($query_resetkey);
?>
<html>
<head>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript"> 
 function Submit(){
    var pass=document.getElementById('password_referral').value;
    if(pass=="")
    {
     alert("Please enter the password");
	 return false;
    }
	var conf_pass=document.getElementById('password_referral_confirm').value;
    if(conf_pass=="")
    {
     alert("Please enter the confirm password field ");
	 return false;
    }
	if(pass==conf_pass){
	  $.ajax({
         type: "POST",
         dataType: "html",
         url: "reset_secure_save.php",
		 data: {"doc_id":<?php echo $doctor_id;?>,"pass" : pass}, 
		 success: function(data) {
	     alert(data);
		 window.location.href="page_expired.php";
		 return false;
	  }
	  });
	}
	else{
	  alert("your password and confirm password is not same");
	  return false;
	}
  
    
	}
</script>

</head>
<title>
</title>
<body>
 Please Reset your password
 <table>
   <tr>
      <td> New Password: </td>
      <td> <input type='password' name='password_referral' id="password_referral" value="" maxlength="5"/>
   </tr>
   <tr>
      <td> Confirm New Password: </td>
      <td> <input type='password' name='password_referral' id="password_referral_confirm" value="" maxlength="5"/>
   </tr>
   <tr><td><input type="submit" name="submit" value="Change Password" onClick="Submit()" /></td></tr>
 </table>
</body>
</html>
<?php
}
?>



