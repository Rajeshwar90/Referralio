<?php 
include 'db_conn.php';
include 'logger.php';
$logger = new Logger();

//added 
include_once 'GCM.php';
include_once 'iosPushUniversal.php';
$gcm = new GCM();

$ios_push = new iosPushUniversal();

$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);


$hospital_id=$_POST['hospital_id'];
$doctor_req=$_POST['doctor_type'];
$msg=$_POST['msg'];

$hospital_name="";
$message_title="ReferraliO";
$refer_out_gcm_Android=array();
$refer_out_gcm_Ios=array();
$refer_in_gcm_Android=array();
$refer_in_gcm_Ios=array();
$read_flag_value='read';
$count_query_msg_count=1;

$logger->write("INFO :","doctor_req name".$doctor_req);

$query_getrefer_out="select * from doctor_stub ds inner join hospital_refer_out_doctor_stub hs on ds.Doctor_serial_id=hs.doctor_stub_id where hs.hospital_id='$hospital_id'";

$result_getrefer_out=mysql_query($query_getrefer_out);
$count_getrefer_out=mysql_num_rows($result_getrefer_out);

$query_getrefer_in="select * from doctor_stub ds inner join hospital_refer_in_doctor_stub hs on ds.Doctor_serial_id=hs.doc_stub_id where hs.refer_by_hos_id='$hospital_id'";

$result_getrefer_in=mysql_query($query_getrefer_in);
$count_getrefer_in=mysql_num_rows($result_getrefer_in);

$logger->write("INFO :","refer in count".$count_getrefer_in);

//to get hospital name
$query_get_hos_details=mysql_query("select * from hospital_stub where hospital_id='$hospital_id'");
while($row_get_hospital=mysql_fetch_assoc($query_get_hos_details)){
  $hospital_name=$row_get_hospital['hospital_name'];
}

$logger->write("INFO :","hospital name".$hospital_name);

if($doctor_req=='refer-out')
{
   if($count_getrefer_out>0){
     
	   while($row=mysql_fetch_assoc($result_getrefer_out)){
	     $mobile=$row['Doctor_mobile_number'];
		 $logger->write("INFO :","inside refer out mobile number".$mobile);
		 
		 $query_get_gcm=mysql_query("select * from gcm_users where mob_number='$mobile'");
		 $count_get_gcm=mysql_num_rows($query_get_gcm);
		 if($count_get_gcm>0){
			 while($row_mobile=mysql_fetch_assoc($query_get_gcm)){
			     if($row_mobile['mobile_os_type']=='Android'){
			         array_push($refer_out_gcm_Android,$row_mobile['gcm_regid']);
			     }else if($row_mobile['mobile_os_type']=='IOS'){
			         array_push($refer_out_gcm_Ios,$row_mobile['gcm_regid']);
			     }
				 
			 }
		 }
	     //send_msg($mobile,$msg);
	   }
	   
	  //saving the push message to database
      $insert_push_broadcast=mysql_query("insert into doc_broadcast_msg(message_title,message_content,doctor_id,hospital_id_author,hospital_name,read_flag) values('$message_title','$msg','$doctor_id','$hospital_id','$hospital_name','$read_flag_value')");	  
	  
      $logger->write("INFO :","inside push out mobile number...**###");
	  
	  //sending push message to all refer out doctors Android
      $registatoin_ids = $refer_out_gcm_Android;
	  $val_broad_msg="broadcast_msg";
	  $message = array("msg" => $msg,"flag_push" => $val_broad_msg,"Broadcast_In_cnt" => $count_query_msg_count);
	  $result = $gcm->send_notification($registatoin_ids, $message);
	  $logger->write("INFO :","result".$result);
	  
	  //sending push message to all refer out doctors IOS
	  $registatoin_ids_IOS = $refer_out_gcm_Ios;
	  $message =$msg;
	  $val_broad_msg="broadcast_msg";
	  $extra = array("msg" => $msg,"flag_push" => $val_broad_msg,"Broadcast_In_cnt" => $count_query_msg_count);
	  $result_ios = $ios_push->sendIosPush($registatoin_ids_IOS,$message,$extra);
	  $logger->write("INFO :","inside IOS result *************".$result_ios);
	  $success = array('status' => "Success", "msg" => "Successfully msg has been sent to refer out doctors");
	  //$this->response($this->json($success),200);
	  echo json_encode($success);
	  exit(0);
	   
   }else{
   
       $success = array('status' => "Success", "msg" => "No Refer out doctors available");
	   //$this->response($this->json($success),200);
	   echo json_encode($success);
	   exit(0);
   
   }
   
}else {
  
   if($count_getrefer_in>0){
   
     while($row=mysql_fetch_assoc($result_getrefer_in)){
	    $mobile=$row['Doctor_mobile_number'];
		
		$query_get_gcm=mysql_query("select * from gcm_users where mob_number='$mobile'");
		 $count_get_gcm=mysql_num_rows($query_get_gcm);
		 if($count_get_gcm>0){
			 while($row_mobile=mysql_fetch_assoc($query_get_gcm)){
			     if($row_mobile['mobile_os_type']=='Android'){
			         array_push($refer_in_gcm_Android,$row_mobile['gcm_regid']);
			     }else if($row_mobile['mobile_os_type']=='IOS'){
			         array_push($refer_in_gcm_Ios,$row_mobile['gcm_regid']);
			     }
				 
			 }
		 }
		
		
		//$query_check_mobile
		//send_msg($mobile,$msg);
	 }
	 
	 //saving the push message to database
      $insert_push_broadcast=mysql_query("insert into doc_broadcast_msg(message_title,message_content,doctor_id,hospital_id_author,hospital_name,read_flag) values('$message_title','$msg','$doctor_id','$hospital_id','$hospital_name','$read_flag_value')");	  
	   
	  //sending push message to all refer in doctors Android
      $registatoin_ids = $refer_in_gcm_Android;
	  $val_broad_msg="broadcast_msg";
	  $message = array("msg" => $msg,"flag_push" => $val_broad_msg,"Broadcast_In_cnt" => $count_query_msg_count);
	  $result = $gcm->send_notification($registatoin_ids, $message);
	  $logger->write("INFO :","result".$result);
	  $logger->write("INFO :","inside push in mobile number");
	 
	 //sending push message to all refer in doctors IOS
	 $logger->write("INFO :","inside IOS***************");
	 $registatoin_ids_IOS = $refer_in_gcm_Ios;
	 $message =$msg;
	 $val_broad_msg="broadcast_msg";
	 $extra = array("msg" => $msg,"flag_push" => $val_broad_msg,"Broadcast_In_cnt" => $count_query_msg_count);
	 $result_ios = $ios_push->sendIosPush($registatoin_ids_IOS,$message,$extra);
	 $logger->write("INFO :","inside IOS result +++++++++++".$result_ios);
	 
	 
	 $success = array('status' => "Success", "msg" => "Successfully msg has been sent to refer in doctors");
	 //$this->response($this->json($success),200);
	 echo json_encode($success);
	 exit(0);
   
   }else{
   
     $success = array('status' => "Success", "msg" => "No Refer in doctors available");
	 //$this->response($this->json($success),200);
	 echo json_encode($success);
	 exit(0);
   
   }

}



?>