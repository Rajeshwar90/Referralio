<?php

include 'db_conn.php';

$newpass=mysql_real_escape_string($_POST['pass']);
$doc_id=$_POST['doc_id'];

$newpass_enc=md5($newpass);

$query="update doctor_stub set Doctor_password='$newpass_enc' where Doctor_serial_id='$doc_id'";
$result=mysql_query($query);
if($result=="success")
{
  echo "Your password has been reset.Please login into your application with the new password";
  
  
}
else
{
  echo "Your password could not be reset.Please click on the reset password link from your application again";
  
}


?>