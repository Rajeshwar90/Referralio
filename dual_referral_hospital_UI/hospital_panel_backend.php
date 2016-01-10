<?php
session_start();
include 'db_conn.php';

$username=stripslashes($_REQUEST['user1']);
$password=stripslashes($_REQUEST['pass1']);
$hospital_id="";

$query="select * from hospital_stub where binary hospital_username='$username' and binary hospital_password='$password'";
$result=mysql_query($query);
$count_result=mysql_num_rows($result);
if($count_result==0)
{
  $success=array("status"=>"Failure","msg"=>"Unsuccessful Login");
  echo json_encode($success);
}else if($count_result==1){
   while($row=mysql_fetch_assoc($result)){
     $hospital_id=$row['hospital_id'];
	 $hospital_name=$row['hospital_name'];
   }
   $_SESSION['hospital_id_SESSION']=$hospital_id;
   $_SESSION['hospital_name_SESSION']=$hospital_name;
   $success=array("status"=>"Success","msg"=>"Successful Login","hospital_id"=>$hospital_id);
   echo json_encode($success);
}


?>