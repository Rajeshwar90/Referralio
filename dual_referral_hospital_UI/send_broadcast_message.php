<?phpsession_start(); //echo "test";if(!isset($_SESSION['hospital_id_SESSION'])){  header("Location: hospital_login.php");  die();}
include 'db_conn.php';
include_once'GCM.php';
include 'logger.php';
$logger = new Logger();
$logger->write("INFO :","PHP Scritp Name =>".$_SERVER['REQUEST_URI']);
$logger->write("INFO :","Type of Request =>".$_SERVER['REQUEST_METHOD']);


$msg=$_POST['msg'];
$hospital_id=$_POST['hospital_id'];
$doctor_id=$_POST['doc_id'];
$mobile="";
$gcm_id="";
$read_flag_value="unread";$msg_title_val="ReferralIO";$hospital_name="";
//get hospital_details$query_get_hos_details=mysql_query("select * from hospital_stub where hospital_id='$hospital_id'");while($row_get_hospital=mysql_fetch_assoc($query_get_hos_details)){  $hospital_name=$row_get_hospital['hospital_name'];}
$result_query_broadcast_insert=mysql_query("insert into doc_broadcast_msg(message_title,message_content,doctor_id,hospital_id_author,hospital_name,read_flag) values('$msg_title_val','$msg','$doctor_id','$hospital_id','$hospital_name','$read_flag_value')");

$result_get_mob=mysql_query("select * from doctor_stub where Doctor_serial_id='$doctor_id'");
while($row_mobile=mysql_fetch_assoc($result_get_mob))
{
  $mobile=$row_mobile['Doctor_mobile_number'];
}

$query_get_gcm_id=mysql_query("select * from gcm_users where mob_number='$mobile'");
while($row_gcm_id=mysql_fetch_assoc($query_get_gcm_id)){
  $gcm_id=$row_gcm_id['gcm_regid'];
}

$read_flag='unread';
$query_get_msg_count=mysql_query("select * from doc_broadcast_msg where doctor_id='$doctor_id' and read_flag='$read_flag'");
$count_query_msg_count=mysql_num_rows($query_get_msg_count);


$gcm = new GCM();

$registatoin_ids = array($gcm_id);
$val_broad_msg="broadcast_msg";
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




function send_msg($mobile,$mobile_msg){
		    
			$logger->write("INFO :","login with mobile".$mobile);
		    
			$msg="";
						
			$msg=urlencode($mobile_msg);
			  
			
			
			$logger->write("INFO :","login with msg".$msg);
			$url="http://sms6.routesms.com:8080/bulksms/bulksms?username=medisense&password=medi2015&type=5&dlr=0&destination=".$mobile."&source=HCHKIN&message=".$msg;
			
			$logger->write("INFO :","login with url".$url);
            
			$ch = curl_init();  // setup a curl
			curl_setopt($ch, CURLOPT_URL, $url);  // set url to send to
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return data reather than echo
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // required as godaddy fails
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
			$output=curl_exec($ch);
           //echo "output".$output;
			curl_close($ch);
			return $output;
		}

?>