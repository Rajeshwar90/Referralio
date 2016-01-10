<?php

session_start();

$username=stripslashes($_REQUEST['user1']);
$password=stripslashes($_REQUEST['pass1']);

if($username=='Referralappdb' && $password='Medisenserefer2015')
{
  $_SESSION['Valid_Session']="valid";
  $success=array("status"=>"Success","msg"=>"Successful Login");
  echo json_encode($success);
}else{
   
  $success=array("status"=>"Failure","msg"=>"Unsuccessful Login");
  echo json_encode($success);

}

?>