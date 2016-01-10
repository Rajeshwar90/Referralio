<?php 

session_start();

if(!isset($_SESSION['Valid_Session'])){
  //redirect to login url in PHP
  header("Location: admin_main_panel.php");
  die();
}

include 'db_conn.php';
include_once'GCM.php';
include 'logger.php';
$logger = new Logger();
$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);
$msg=$_POST['msg'];

$gcm_id_arr=array();
$query_get_gcm_id=mysql_query("select * from gcm_users");
while($row_gcm_id=mysql_fetch_assoc($query_get_gcm_id)){
  $gcm_id=$row_gcm_id['gcm_regid'];
  array_push($gcm_id_arr,$gcm_id);
}


$gcm = new GCM();

$registatoin_ids = $gcm_id_arr;
$val_broad_msg="broadcast_msg";
$count_query_msg_count=1;
$msg_title_val="ReferralIO";
$doctor_id="ADMIN";
$hospital_id="ADMIN";
$hospital_name="";
$read_flag_value="read";


$result_query_broadcast_insert=mysql_query("insert into doc_broadcast_msg(message_title,message_content,doctor_id,hospital_id_author,hospital_name,read_flag) values('$msg_title_val','$msg','$doctor_id','$hospital_id','$hospital_name','$read_flag_value')");


$message = array("msg" => $msg,"flag_push" => $val_broad_msg,"Broadcast_In_cnt" => $count_query_msg_count);
$result = $gcm->send_notification($registatoin_ids, $message);
$logger->write("INFO :","result".$result);

if($result){
   $success = array('status' => "Success", "msg" => "Successfully msg has been sent to doctor");
	   //$this->response($this->json($success),200);
   echo json_encode($success);
   exit(0);
}
else
{
   $success = array('status' => "Success", "msg" => "Successfully msg has been sent to doctor");
	 //$this->response($this->json($success),200);
	 echo json_encode($success);
	 exit(0);
}




?>